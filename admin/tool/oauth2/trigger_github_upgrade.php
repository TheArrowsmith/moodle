<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * Trigger GitHub OAuth2 upgrade manually
 *
 * @package    tool_oauth2
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/upgradelib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('oauth2');

echo $OUTPUT->header();
echo $OUTPUT->heading('Trigger GitHub OAuth2 Upgrade');

// Force upgrade of auth_oauth2 plugin
$result = upgrade_plugin('auth', 'oauth2', false);

if ($result) {
    echo $OUTPUT->notification('GitHub OAuth2 issuer upgrade completed successfully!', 'notifysuccess');
    echo html_writer::tag('p', 'You should now be able to see GitHub in Site administration → Server → OAuth 2 services');
    echo html_writer::tag('p', html_writer::link(new moodle_url('/admin/tool/oauth2/issuers.php'), 'Go to OAuth 2 services'));
} else {
    echo $OUTPUT->notification('Upgrade failed or no upgrade was needed.', 'notifyproblem');
}

echo $OUTPUT->footer();