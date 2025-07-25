<?php
/**
 * Check GitHub OAuth2 status and configuration
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('oauth2');

echo $OUTPUT->header();
echo $OUTPUT->heading('GitHub OAuth2 Status Check');

// Find GitHub issuer
$issuer = $DB->get_record('oauth2_issuer', ['name' => 'GitHub']);
if (!$issuer) {
    echo $OUTPUT->notification('ERROR: GitHub issuer not found!', 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

echo html_writer::tag('h3', 'Issuer Configuration');
echo '<table class="table table-bordered">';
echo '<tr><th>Setting</th><th>Value</th></tr>';
echo "<tr><td>ID</td><td>{$issuer->id}</td></tr>";
echo "<tr><td>Name</td><td>{$issuer->name}</td></tr>";
echo "<tr><td>Enabled</td><td>" . ($issuer->enabled ? '✓ Yes' : '✗ No') . "</td></tr>";
echo "<tr><td>Show on login page</td><td>" . ($issuer->showonloginpage ? '✓ Yes' : '✗ No') . "</td></tr>";
echo "<tr><td>Login scopes</td><td>{$issuer->loginscopes}</td></tr>";
echo "<tr><td>Base URL</td><td>{$issuer->baseurl}</td></tr>";
echo '</table>';

echo html_writer::tag('h3', 'Endpoints');
$endpoints = $DB->get_records('oauth2_endpoint', ['issuerid' => $issuer->id]);
echo '<table class="table table-bordered">';
echo '<tr><th>Name</th><th>URL</th></tr>';
foreach ($endpoints as $endpoint) {
    echo "<tr><td>{$endpoint->name}</td><td>{$endpoint->url}</td></tr>";
}
echo '</table>';

echo html_writer::tag('h3', 'Field Mappings');
$mappings = $DB->get_records('oauth2_user_field_mapping', ['issuerid' => $issuer->id]);
echo '<table class="table table-bordered">';
echo '<tr><th>GitHub Field</th><th>→</th><th>Moodle Field</th></tr>';
foreach ($mappings as $mapping) {
    $highlight = '';
    if ($mapping->internalfield == 'username' || $mapping->internalfield == 'email') {
        $highlight = ' class="table-warning"';
    }
    echo "<tr{$highlight}><td>{$mapping->externalfield}</td><td>→</td><td>{$mapping->internalfield}</td></tr>";
}
echo '</table>';

// Check for critical mappings
$hasUsername = false;
$hasEmail = false;
foreach ($mappings as $mapping) {
    if ($mapping->internalfield == 'username') {
        $hasUsername = true;
    }
    if ($mapping->internalfield == 'email') {
        $hasEmail = true;
    }
}

echo html_writer::tag('h3', 'Critical Fields Check');
if (!$hasUsername) {
    echo html_writer::tag('p', '✗ Missing username mapping!', ['class' => 'alert alert-danger']);
} else {
    echo html_writer::tag('p', '✓ Username mapping found', ['class' => 'alert alert-success']);
}

if (!$hasEmail) {
    echo html_writer::tag('p', '✗ Missing email mapping!', ['class' => 'alert alert-danger']);
} else {
    echo html_writer::tag('p', '✓ Email mapping found', ['class' => 'alert alert-success']);
}

// Quick fix button
if (!$hasUsername || !$hasEmail) {
    echo html_writer::tag('h3', 'Quick Fix');
    echo '<form method="post">';
    echo '<input type="hidden" name="fix" value="1" />';
    echo '<button type="submit" class="btn btn-danger">Fix Missing Mappings</button>';
    echo '</form>';
    
    if (optional_param('fix', 0, PARAM_INT)) {
        // Clear and recreate mappings
        $DB->delete_records('oauth2_user_field_mapping', ['issuerid' => $issuer->id]);
        
        $mappings = [
            ['externalfield' => 'login', 'internalfield' => 'username'],
            ['externalfield' => 'email', 'internalfield' => 'email'],
            ['externalfield' => 'name', 'internalfield' => 'firstname'],
            ['externalfield' => 'id', 'internalfield' => 'idnumber'],  // Add ID as fallback
        ];
        
        foreach ($mappings as $mapping) {
            $record = new stdClass();
            $record->issuerid = $issuer->id;
            $record->externalfield = $mapping['externalfield'];
            $record->internalfield = $mapping['internalfield'];
            $record->timecreated = time();
            $record->timemodified = time();
            $record->usermodified = $USER->id;
            $DB->insert_record('oauth2_user_field_mapping', $record);
        }
        
        echo html_writer::tag('p', 'Mappings fixed! Please refresh the page.', ['class' => 'alert alert-success']);
        echo '<script>setTimeout(function() { location.reload(); }, 2000);</script>';
    }
}

echo html_writer::tag('div', 
    html_writer::link(new moodle_url('/admin/tool/oauth2/issuers.php'), 'Back to OAuth 2 services', ['class' => 'btn btn-primary']) . ' ' .
    html_writer::link(new moodle_url('/login/index.php'), 'Test login', ['class' => 'btn btn-success']),
    ['class' => 'mt-3']
);

echo $OUTPUT->footer();