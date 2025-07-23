<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * GitHub OAuth2 client implementation
 *
 * @package    core
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\oauth2\client;

defined('MOODLE_INTERNAL') || die();

/**
 * GitHub OAuth2 client implementation
 */
class github extends \core\oauth2\client {
    
    /**
     * GitHub requires Accept header for JSON responses
     * 
     * @param string $url
     * @param array $options
     * @param mixed $acceptheader
     * @return string
     */
    protected function request($url, $options = array(), $acceptheader = 'application/json') {
        // GitHub requires specific Accept header
        if (!isset($options['CURLOPT_HTTPHEADER'])) {
            $options['CURLOPT_HTTPHEADER'] = array();
        }
        $options['CURLOPT_HTTPHEADER'][] = 'Accept: application/json';
        
        return parent::request($url, $options, $acceptheader);
    }
    
    /**
     * Map GitHub user info to Moodle user fields
     * 
     * @param \stdClass $userinfo
     * @return array
     */
    protected function map_userinfo_to_fields($userinfo) {
        $fields = parent::map_userinfo_to_fields($userinfo);
        
        // GitHub returns 'login' as username
        if (!empty($userinfo->login) && empty($fields['username'])) {
            $fields['username'] = $userinfo->login;
        }
        
        // Split full name into first/last if available
        if (!empty($userinfo->name)) {
            $parts = explode(' ', $userinfo->name, 2);
            if (empty($fields['firstname'])) {
                $fields['firstname'] = $parts[0];
            }
            if (empty($fields['lastname']) && isset($parts[1])) {
                $fields['lastname'] = $parts[1];
            }
        }
        
        // Use login as firstname if name not provided
        if (empty($fields['firstname']) && !empty($userinfo->login)) {
            $fields['firstname'] = $userinfo->login;
            $fields['lastname'] = 'User';
        }
        
        // Map additional GitHub fields
        if (!empty($userinfo->bio) && empty($fields['description'])) {
            $fields['description'] = $userinfo->bio;
        }
        
        if (!empty($userinfo->location) && empty($fields['city'])) {
            $fields['city'] = $userinfo->location;
        }
        
        if (!empty($userinfo->html_url) && empty($fields['url'])) {
            $fields['url'] = $userinfo->html_url;
        }
        
        return $fields;
    }
}