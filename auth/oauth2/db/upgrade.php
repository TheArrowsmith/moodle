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
 * OAuth2 authentication plugin upgrade code
 *
 * @package    auth_oauth2
 * @copyright  2017 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade function
 *
 * @param int $oldversion the version we are upgrading from
 * @return bool result
 */
function xmldb_auth_oauth2_upgrade($oldversion) {
    global $DB;

    $dbman = $DB->get_manager();

    // Automatically generated Moodle v3.2.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.3.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.4.0 release upgrade line.
    // Put any upgrade step following this.

    // Automatically generated Moodle v3.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2018051401) {
        // Fetch Facebook, Google, and Microsoft issuers. We use the URL field to determine the issuer type as it's the only
        // field that contains the keyword that can somewhat let us reliably determine the issuer type.
        $likefacebook = $DB->sql_like('oe.url', ':facebook');
        $likegoogle = $DB->sql_like('oe.url', ':google');
        $likemicrosoft = $DB->sql_like('oe.url', ':microsoft');

        $params = [
            'facebook' => '%facebook%',
            'google' => '%google%',
            'microsoft' => '%microsoft%',
        ];

        // We're querying from the oauth2_endpoint table because the base URLs of FB and Microsoft can be empty in the issuer table.
        $subsql = "
            SELECT DISTINCT oe.issuerid
                       FROM {oauth2_endpoint} oe
                      WHERE $likefacebook
                            OR $likegoogle
                            OR $likemicrosoft";

        // Update non-Facebook/Google/Microsoft issuers and set requireconfirmation to 1.
        $updatesql = "
            UPDATE {oauth2_issuer}
               SET requireconfirmation = 1
             WHERE id NOT IN ({$subsql})";
        $DB->execute($updatesql, $params);

        // Delete linked logins for non-Facebook/Google/Microsoft issuers. They can easily re-link their logins anyway.
        $DB->delete_records_select('auth_oauth2_linked_login', "issuerid NOT IN ($subsql)", $params);

        upgrade_plugin_savepoint(true, 2018051401, 'auth', 'oauth2');
    }

    // Add GitHub OAuth2 issuer
    if ($oldversion < 2024121802) {
        // Check if GitHub issuer already exists
        if (!$DB->record_exists('oauth2_issuer', ['name' => 'GitHub'])) {
            
            // Create GitHub issuer
            $issuer = new stdClass();
            $issuer->name = 'GitHub';
            $issuer->image = 'https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png';
            $issuer->baseurl = 'https://github.com';
            $issuer->clientid = '';  // To be filled by admin
            $issuer->clientsecret = '';  // To be filled by admin
            $issuer->loginscopes = 'user:email';
            $issuer->loginscopesoffline = 'user:email';
            $issuer->loginparams = '';  // No additional params needed
            $issuer->loginparamsoffline = '';  // No additional params needed
            $issuer->alloweddomains = '';  // No domain restrictions
            $issuer->scopessupported = '';  // Will be populated by GitHub
            $issuer->showonloginpage = 1;
            $issuer->enabled = 0;  // Disabled until configured
            $issuer->basicauth = 0;  // Use header auth, not basic auth
            $issuer->sortorder = 0;
            $issuer->timecreated = time();
            $issuer->timemodified = time();
            $issuer->usermodified = 0; // System created
            
            $issuerid = $DB->insert_record('oauth2_issuer', $issuer);
            
            // Create endpoints
            $endpoints = [
                [
                    'issuerid' => $issuerid,
                    'name' => 'authorization_endpoint',
                    'url' => 'https://github.com/login/oauth/authorize',
                ],
                [
                    'issuerid' => $issuerid,
                    'name' => 'token_endpoint',
                    'url' => 'https://github.com/login/oauth/access_token',
                ],
                [
                    'issuerid' => $issuerid,
                    'name' => 'userinfo_endpoint',
                    'url' => 'https://api.github.com/user',
                ],
            ];
            
            foreach ($endpoints as $endpoint) {
                $endpoint['timecreated'] = time();
                $endpoint['timemodified'] = time();
                $endpoint['usermodified'] = 0; // System created
                $DB->insert_record('oauth2_endpoint', $endpoint);
            }
            
            // Create field mappings
            $mappings = [
                ['externalfield' => 'login', 'internalfield' => 'username'],
                ['externalfield' => 'email', 'internalfield' => 'email'],
                ['externalfield' => 'name', 'internalfield' => 'firstname'],
                ['externalfield' => 'avatar_url', 'internalfield' => 'picture'],
                ['externalfield' => 'bio', 'internalfield' => 'description'],
                ['externalfield' => 'location', 'internalfield' => 'city'],
                ['externalfield' => 'html_url', 'internalfield' => 'url'],
            ];
            
            foreach ($mappings as $mapping) {
                $mapping['issuerid'] = $issuerid;
                $mapping['timecreated'] = time();
                $mapping['timemodified'] = time();
                $mapping['usermodified'] = 0; // System created
                $DB->insert_record('oauth2_user_field_mapping', $mapping);
            }
        }
        
        upgrade_plugin_savepoint(true, 2024121802, 'auth', 'oauth2');
    }

    return true;
}
