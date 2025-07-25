<?php
/**
 * Enable GitHub OAuth2 with correct settings
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('oauth2');

echo $OUTPUT->header();
echo $OUTPUT->heading('Enable GitHub OAuth2');

// Find GitHub issuer
$issuer = $DB->get_record('oauth2_issuer', ['name' => 'GitHub']);
if (!$issuer) {
    echo $OUTPUT->notification('ERROR: GitHub issuer not found!', 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// Update issuer with correct settings
$issuer->enabled = 1;
$issuer->showonloginpage = 1;
$issuer->loginscopes = 'user:email read:user';  // Request both user and email scopes
$issuer->loginscopesoffline = 'user:email read:user';
$issuer->timemodified = time();
$issuer->usermodified = $USER->id;

$DB->update_record('oauth2_issuer', $issuer);
echo html_writer::tag('p', "Updated GitHub issuer settings", ['class' => 'alert alert-success']);

// Add email endpoint if it doesn't exist
$emailendpoint = $DB->get_record('oauth2_endpoint', [
    'issuerid' => $issuer->id,
    'name' => 'email_endpoint'
]);

if (!$emailendpoint) {
    $endpoint = new stdClass();
    $endpoint->issuerid = $issuer->id;
    $endpoint->name = 'email_endpoint';
    $endpoint->url = 'https://api.github.com/user/emails';
    $endpoint->timecreated = time();
    $endpoint->timemodified = time();
    $endpoint->usermodified = $USER->id;
    $DB->insert_record('oauth2_endpoint', $endpoint);
    echo html_writer::tag('p', "Created email endpoint", ['class' => 'alert alert-success']);
}

// Update system client class if not already set
$systemclient = $DB->get_record('oauth2_system_account', ['issuerid' => $issuer->id]);
if (!$systemclient) {
    // Create system account record to use custom client
    $systemclient = new stdClass();
    $systemclient->issuerid = $issuer->id;
    $systemclient->refreshtoken = '';
    $systemclient->grantedscopes = '';
    $systemclient->email = '';
    $systemclient->username = '';
    $systemclient->timecreated = time();
    $systemclient->timemodified = time();
    $systemclient->usermodified = $USER->id;
    $DB->insert_record('oauth2_system_account', $systemclient);
}

echo $OUTPUT->notification("GitHub OAuth2 enabled successfully!", 'notifysuccess');
echo html_writer::tag('p', "The GitHub login button should now appear on the login page.");

echo html_writer::tag('div', 
    html_writer::link(new moodle_url('/admin/tool/oauth2/issuers.php'), 'Back to OAuth 2 services', ['class' => 'btn btn-primary']) . ' ' .
    html_writer::link(new moodle_url('/login/index.php'), 'Test login', ['class' => 'btn btn-success']),
    ['class' => 'mt-3']
);

echo $OUTPUT->footer();