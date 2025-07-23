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
 * GitHub OAuth2 setup script
 *
 * @package    tool_oauth2
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

/**
 * Setup GitHub OAuth2 issuer
 */
function setup_github_oauth2_issuer() {
    global $DB;
    
    // Check if GitHub issuer already exists
    if ($DB->record_exists('oauth2_issuer', ['name' => 'GitHub'])) {
        echo "GitHub OAuth2 issuer already exists.\n";
        return;
    }
    
    // Create issuer
    $issuer = new \stdClass();
    $issuer->name = 'GitHub';
    $issuer->image = 'https://github.githubassets.com/images/modules/logos_page/GitHub-Mark.png';
    $issuer->baseurl = 'https://github.com';
    $issuer->clientid = '';  // To be filled by admin
    $issuer->clientsecret = '';  // To be filled by admin
    $issuer->loginscopes = 'user:email';
    $issuer->loginscopesoffline = 'user:email';
    $issuer->showonloginpage = 1;
    $issuer->enabled = 0;  // Disabled until configured
    $issuer->sortorder = 0;
    $issuer->timecreated = time();
    $issuer->timemodified = time();
    $issuer->usermodified = $USER->id;
    
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
        $endpoint['usermodified'] = $USER->id;
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
        $mapping['usermodified'] = $USER->id;
        $DB->insert_record('oauth2_user_field_mapping', $mapping);
    }
    
    echo "GitHub OAuth2 issuer created successfully!\n";
    echo "Next steps:\n";
    echo "1. Create a GitHub OAuth App at https://github.com/settings/developers\n";
    echo "2. Set the Authorization callback URL to: " . $CFG->wwwroot . "/auth/oauth2/callback.php\n";
    echo "3. Go to Site administration > Server > OAuth 2 services\n";
    echo "4. Edit the GitHub issuer and enter your Client ID and Client Secret\n";
    echo "5. Enable the GitHub issuer\n";
}

// Run the setup
admin_externalpage_setup('oauth2services');

echo $OUTPUT->header();
echo $OUTPUT->heading('GitHub OAuth2 Setup');

echo html_writer::start_tag('pre');
setup_github_oauth2_issuer();
echo html_writer::end_tag('pre');

echo $OUTPUT->footer();