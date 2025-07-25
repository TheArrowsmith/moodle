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
 * Github OAuth2 client implementation.
 *
 * @package    core
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\oauth2\client;

use core\oauth2\client;
use core\oauth2\user_field_mapping;
use curl;
use stdClass;

defined('MOODLE_INTERNAL') || die();

/**
 * Github OAuth2 client implementation.
 *
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class github extends client {

    /**
     * Fetch the user info from the userinfo endpoint.
     *
     * @return array|false
     */
    public function get_userinfo() {
        $url = $this->get_issuer()->get_endpoint_url('userinfo');
        if (empty($url)) {
            return false;
        }

        // GitHub requires specific headers
        $response = $this->get($url, [], [
            'CURLOPT_HTTPHEADER' => [
                'Authorization: Bearer ' . $this->get_accesstoken()->token,
                'Accept: application/json',
                'User-Agent: Moodle OAuth2'
            ]
        ]);

        if (!$response) {
            return false;
        }

        $userinfo = json_decode($response);
        if (!$userinfo) {
            return false;
        }

        // GitHub might not return email in the main user endpoint
        // We need to fetch it separately if it's not there
        if (empty($userinfo->email)) {
            $emailresponse = $this->get('https://api.github.com/user/emails', [], [
                'CURLOPT_HTTPHEADER' => [
                    'Authorization: Bearer ' . $this->get_accesstoken()->token,
                    'Accept: application/json',
                    'User-Agent: Moodle OAuth2'
                ]
            ]);
            
            if ($emailresponse) {
                $emails = json_decode($emailresponse);
                if (is_array($emails)) {
                    // Find primary email
                    foreach ($emails as $email) {
                        if (!empty($email->primary) && !empty($email->verified)) {
                            $userinfo->email = $email->email;
                            break;
                        }
                    }
                    // If no primary, use first verified email
                    if (empty($userinfo->email)) {
                        foreach ($emails as $email) {
                            if (!empty($email->verified)) {
                                $userinfo->email = $email->email;
                                break;
                            }
                        }
                    }
                }
            }
        }

        // Use the parent's mapping logic to properly map fields
        $map = $this->get_userinfo_mapping();
        
        $user = new stdClass();
        foreach ($map as $openidproperty => $moodleproperty) {
            // We support nested objects via a-b-c syntax.
            $getfunc = function($obj, $prop) use (&$getfunc) {
                $proplist = explode('-', $prop, 2);
                if (empty($proplist[0]) || empty($obj->{$proplist[0]})) {
                    return false;
                }
                $obj = $obj->{$proplist[0]};

                if (count($proplist) > 1) {
                    return $getfunc($obj, $proplist[1]);
                }
                return $obj;
            };
            
            $resolved = $getfunc($userinfo, $openidproperty);
            if (!empty($resolved)) {
                $user->$moodleproperty = $resolved;
            }
        }

        // Handle the name field - GitHub returns full name, we need to split it
        if (!empty($userinfo->name) && empty($user->lastname)) {
            $names = explode(' ', $userinfo->name, 2);
            if (!empty($names[0]) && empty($user->firstname)) {
                $user->firstname = $names[0];
            }
            if (!empty($names[1]) && empty($user->lastname)) {
                $user->lastname = $names[1];
            }
        }
        
        // Fallback if no firstname
        if (empty($user->firstname)) {
            if (!empty($user->username)) {
                $user->firstname = $user->username;
            } else if (!empty($userinfo->login)) {
                $user->firstname = $userinfo->login;
            }
        }
        
        // Ensure we have lastname
        if (empty($user->lastname)) {
            $user->lastname = '';
        }

        return (array)$user;
    }

    /**
     * Override parent to handle GitHub's token endpoint response.
     *
     * @param string $code
     * @return bool
     */
    public function upgrade_token($code) {
        // GitHub requires Accept header for JSON response
        $this->setHeader('Accept: application/json');
        return parent::upgrade_token($code);
    }
}