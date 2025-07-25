<?php
// This file is part of Moodle - http://moodle.org/
//
// Debug why OAuth2 login buttons don't appear

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('oauth2');

echo $OUTPUT->header();
echo $OUTPUT->heading('Debug OAuth2 Login Buttons');

// Get all issuers
$issuers = \core\oauth2\api::get_all_issuers();

echo html_writer::start_tag('div', ['style' => 'background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px;']);
echo html_writer::tag('h3', 'All OAuth2 Issuers (' . count($issuers) . ')');

foreach ($issuers as $issuer) {
    echo html_writer::start_tag('div', ['style' => 'border: 1px solid #ccc; padding: 10px; margin: 10px 0; background: white;']);
    echo html_writer::tag('h4', $issuer->get('name'));
    
    $enabled = $issuer->get('enabled');
    $showonlogin = $issuer->get('showonloginpage');
    $configured = $issuer->is_configured();
    $clientid = $issuer->get('clientid');
    $clientsecret = $issuer->get('clientsecret');
    
    echo html_writer::tag('p', '<strong>Enabled:</strong> ' . ($enabled ? 'YES ✓' : 'NO ✗'));
    echo html_writer::tag('p', '<strong>Show on login page:</strong> ' . ($showonlogin ? 'YES ✓' : 'NO ✗'));
    echo html_writer::tag('p', '<strong>Is configured:</strong> ' . ($configured ? 'YES ✓' : 'NO ✗'));
    echo html_writer::tag('p', '<strong>Client ID:</strong> ' . (empty($clientid) ? 'EMPTY ✗' : 'SET ✓ (' . substr($clientid, 0, 10) . '...)'));
    echo html_writer::tag('p', '<strong>Client Secret:</strong> ' . (empty($clientsecret) ? 'EMPTY ✗' : 'SET ✓ (' . substr($clientsecret, 0, 10) . '...)'));
    
    // Test the is_ready_for_login_page logic
    $auth = new \auth_oauth2\auth();
    $reflection = new ReflectionClass($auth);
    $method = $reflection->getMethod('is_ready_for_login_page');
    $method->setAccessible(true);
    $ready = $method->invokeArgs($auth, [$issuer]);
    
    echo html_writer::tag('p', '<strong>Ready for login page:</strong> ' . ($ready ? 'YES ✓' : 'NO ✗'));
    
    if (!$ready) {
        echo html_writer::tag('p', '<em style="color: red;">This issuer will NOT appear on the login page</em>');
        echo html_writer::tag('p', '<strong>Reason:</strong>');
        if (!$enabled) echo html_writer::tag('p', '• Not enabled');
        if (!$configured) echo html_writer::tag('p', '• Not configured (missing client ID or secret)');
        if (!$showonlogin) echo html_writer::tag('p', '• "Show on login page" not checked');
    } else {
        echo html_writer::tag('p', '<em style="color: green;">This issuer SHOULD appear on the login page</em>');
    }
    
    echo html_writer::end_tag('div');
}

echo html_writer::end_tag('div');

// Test the actual loginpage_idp_list function
echo html_writer::start_tag('div', ['style' => 'background: #e7f3ff; padding: 15px; margin: 10px 0; border-radius: 5px;']);
echo html_writer::tag('h3', 'Login Page Identity Providers');

$auth = new \auth_oauth2\auth();
$providers = $auth->loginpage_idp_list('/');

echo html_writer::tag('p', '<strong>Number of providers for login page:</strong> ' . count($providers));

if (empty($providers)) {
    echo html_writer::tag('p', '<em style="color: red;">No OAuth2 providers will appear on the login page!</em>');
} else {
    foreach ($providers as $provider) {
        echo html_writer::tag('p', '• ' . $provider['name'] . ' - ' . $provider['url']);
    }
}

echo html_writer::end_tag('div');

// Check if OAuth2 auth is enabled
echo html_writer::start_tag('div', ['style' => 'background: #fff3cd; padding: 15px; margin: 10px 0; border-radius: 5px;']);
echo html_writer::tag('h3', 'Authentication Plugin Status');

$oauth2_enabled = is_enabled_auth('oauth2');
echo html_writer::tag('p', '<strong>OAuth2 auth plugin enabled:</strong> ' . ($oauth2_enabled ? 'YES ✓' : 'NO ✗'));

if (!$oauth2_enabled) {
    echo html_writer::tag('p', '<em style="color: red;">OAuth2 authentication plugin is disabled! This may prevent login buttons from showing.</em>');
    echo html_writer::tag('p', '<strong>Fix:</strong> Go to Site administration → Plugins → Authentication → Manage authentication and enable OAuth2');
}

echo html_writer::end_tag('div');

echo html_writer::tag('p', html_writer::link(new moodle_url('/login/index.php'), 'View login page'));
echo html_writer::tag('p', html_writer::link(new moodle_url('/admin/settings.php?section=manageauths'), 'Manage authentication plugins'));

echo $OUTPUT->footer();