<?php
/**
 * Fix GitHub OAuth2 endpoints and field mappings
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('oauth2');

echo $OUTPUT->header();
echo $OUTPUT->heading('Fix GitHub OAuth2 Configuration');

// Find GitHub issuer
$issuer = $DB->get_record('oauth2_issuer', ['name' => 'GitHub']);
if (!$issuer) {
    echo $OUTPUT->notification('ERROR: GitHub issuer not found!', 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

echo html_writer::tag('p', "Found GitHub issuer (ID: {$issuer->id})");

// Update endpoints
$endpoints = [
    'authorization_endpoint' => 'https://github.com/login/oauth/authorize',
    'token_endpoint' => 'https://github.com/login/oauth/access_token',
    'userinfo_endpoint' => 'https://api.github.com/user'
];

foreach ($endpoints as $name => $url) {
    $endpoint = $DB->get_record('oauth2_endpoint', [
        'issuerid' => $issuer->id,
        'name' => $name
    ]);
    
    if ($endpoint) {
        if ($endpoint->url !== $url) {
            $endpoint->url = $url;
            $endpoint->timemodified = time();
            $endpoint->usermodified = $USER->id;
            $DB->update_record('oauth2_endpoint', $endpoint);
            echo html_writer::tag('p', "Updated endpoint: {$name} -> {$url}", ['class' => 'alert alert-info']);
        } else {
            echo html_writer::tag('p', "Endpoint already correct: {$name}");
        }
    } else {
        $endpoint = new stdClass();
        $endpoint->issuerid = $issuer->id;
        $endpoint->name = $name;
        $endpoint->url = $url;
        $endpoint->timecreated = time();
        $endpoint->timemodified = time();
        $endpoint->usermodified = $USER->id;
        $DB->insert_record('oauth2_endpoint', $endpoint);
        echo html_writer::tag('p', "Created endpoint: {$name} -> {$url}", ['class' => 'alert alert-success']);
    }
}

// Clear existing field mappings
$DB->delete_records('oauth2_user_field_mapping', ['issuerid' => $issuer->id]);
echo html_writer::tag('p', "Cleared existing field mappings", ['class' => 'alert alert-warning']);

// Create proper field mappings for GitHub API
$mappings = [
    'login' => 'username',     // GitHub username -> Moodle username
    'email' => 'email',        // GitHub email -> Moodle email
    'name' => 'firstname',     // GitHub full name -> Moodle first name (we'll handle lastname split later)
    'avatar_url' => 'picture', // GitHub avatar -> Moodle picture
    'bio' => 'description',    // GitHub bio -> Moodle description
    'location' => 'city',      // GitHub location -> Moodle city
    'html_url' => 'url'        // GitHub profile URL -> Moodle URL
];

foreach ($mappings as $external => $internal) {
    $mapping = new stdClass();
    $mapping->issuerid = $issuer->id;
    $mapping->externalfield = $external;
    $mapping->internalfield = $internal;
    $mapping->timecreated = time();
    $mapping->timemodified = time();
    $mapping->usermodified = $USER->id;
    $DB->insert_record('oauth2_user_field_mapping', $mapping);
    echo html_writer::tag('p', "Created mapping: {$external} -> {$internal}");
}

// Update issuer settings to ensure proper scopes
$issuer->loginscopes = 'user:email';
$issuer->loginscopesoffline = 'user:email';
$issuer->timemodified = time();
$issuer->usermodified = $USER->id;
$DB->update_record('oauth2_issuer', $issuer);
echo html_writer::tag('p', "Updated issuer scopes to: user:email", ['class' => 'alert alert-info']);

echo $OUTPUT->notification("GitHub OAuth2 configuration fixed!", 'notifysuccess');
echo html_writer::tag('p', "Make sure your GitHub OAuth app callback URL is: <strong>{$CFG->wwwroot}/admin/oauth2callback.php</strong>");

echo html_writer::tag('div', 
    html_writer::link(new moodle_url('/admin/tool/oauth2/issuers.php'), 'Back to OAuth 2 services', ['class' => 'btn btn-primary']) . ' ' .
    html_writer::link(new moodle_url('/login/index.php'), 'Test login', ['class' => 'btn btn-success']),
    ['class' => 'mt-3']
);

echo $OUTPUT->footer();