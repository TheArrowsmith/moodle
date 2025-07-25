<?php
/**
 * Debug GitHub OAuth2 API response
 */

require_once('../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

admin_externalpage_setup('oauth2');

echo $OUTPUT->header();
echo $OUTPUT->heading('Debug GitHub OAuth2 Response');

// Get GitHub issuer
$issuer = $DB->get_record('oauth2_issuer', ['name' => 'GitHub']);
if (!$issuer) {
    echo $OUTPUT->notification('GitHub issuer not found!', 'notifyproblem');
    echo $OUTPUT->footer();
    exit;
}

// Check if we have a test token parameter
$testtoken = optional_param('token', '', PARAM_RAW);

if ($testtoken) {
    // Test the API with provided token
    echo html_writer::tag('h3', 'Testing GitHub API with provided token');
    
    $curl = new curl();
    $curl->setHeader(array(
        'Authorization: Bearer ' . $testtoken,
        'Accept: application/json',
        'User-Agent: Moodle OAuth2'
    ));
    
    $response = $curl->get('https://api.github.com/user');
    $info = $curl->get_info();
    
    echo html_writer::tag('h4', 'Response Info:');
    echo html_writer::tag('pre', print_r($info, true));
    
    echo html_writer::tag('h4', 'Raw Response:');
    echo html_writer::tag('pre', htmlspecialchars($response));
    
    $userinfo = json_decode($response);
    if ($userinfo) {
        echo html_writer::tag('h4', 'Decoded Response:');
        echo html_writer::tag('pre', print_r($userinfo, true));
        
        echo html_writer::tag('h4', 'Field Mapping Test:');
        $mappings = $DB->get_records('oauth2_user_field_mapping', ['issuerid' => $issuer->id]);
        echo '<table class="table table-bordered">';
        echo '<tr><th>External Field</th><th>Internal Field</th><th>Value</th><th>Status</th></tr>';
        foreach ($mappings as $mapping) {
            $value = isset($userinfo->{$mapping->externalfield}) ? $userinfo->{$mapping->externalfield} : null;
            $status = $value ? '✓' : '✗ Missing';
            $displayValue = is_null($value) ? '(null)' : htmlspecialchars(substr((string)$value, 0, 100));
            echo "<tr>";
            echo "<td>{$mapping->externalfield}</td>";
            echo "<td>{$mapping->internalfield}</td>";
            echo "<td>{$displayValue}</td>";
            echo "<td>{$status}</td>";
            echo "</tr>";
        }
        echo '</table>';
        
        // Check specifically for username and email
        echo html_writer::tag('h4', 'Critical Fields Check:');
        $username = null;
        $email = null;
        
        // Check all possible username fields
        $usernameFields = ['login', 'username', 'id', 'node_id'];
        foreach ($usernameFields as $field) {
            if (isset($userinfo->$field) && !empty($userinfo->$field)) {
                $username = $userinfo->$field;
                echo html_writer::tag('p', "Found username in field '$field': $username", ['class' => 'alert alert-success']);
                break;
            }
        }
        
        // Check email
        if (isset($userinfo->email) && !empty($userinfo->email)) {
            $email = $userinfo->email;
            echo html_writer::tag('p', "Found email: $email", ['class' => 'alert alert-success']);
        } else {
            echo html_writer::tag('p', "Email is missing or empty!", ['class' => 'alert alert-danger']);
            echo html_writer::tag('p', "Note: GitHub may return null email if the user has not set a public email. The 'user:email' scope should help.", ['class' => 'alert alert-warning']);
        }
    } else {
        echo html_writer::tag('p', 'Failed to decode JSON response', ['class' => 'alert alert-danger']);
    }
} else {
    // Show token input form
    echo html_writer::tag('p', 'To test the GitHub API response, you need a GitHub personal access token.');
    echo html_writer::tag('p', 'Get one from: ' . html_writer::link('https://github.com/settings/tokens', 'https://github.com/settings/tokens', ['target' => '_blank']));
    
    echo '<form method="post">';
    echo '<div class="form-group">';
    echo '<label for="token">GitHub Access Token:</label>';
    echo '<input type="text" name="token" id="token" class="form-control" placeholder="ghp_xxxxxxxxxxxx" />';
    echo '</div>';
    echo '<button type="submit" class="btn btn-primary">Test API</button>';
    echo '</form>';
}

// Show current configuration
echo html_writer::tag('h3', 'Current Configuration', ['class' => 'mt-4']);
echo html_writer::tag('h4', 'Endpoints:');
$endpoints = $DB->get_records('oauth2_endpoint', ['issuerid' => $issuer->id]);
echo '<table class="table table-bordered">';
echo '<tr><th>Name</th><th>URL</th></tr>';
foreach ($endpoints as $endpoint) {
    echo "<tr><td>{$endpoint->name}</td><td>{$endpoint->url}</td></tr>";
}
echo '</table>';

echo html_writer::tag('h4', 'Field Mappings:');
$mappings = $DB->get_records('oauth2_user_field_mapping', ['issuerid' => $issuer->id]);
echo '<table class="table table-bordered">';
echo '<tr><th>External Field</th><th>Internal Field</th></tr>';
foreach ($mappings as $mapping) {
    echo "<tr><td>{$mapping->externalfield}</td><td>{$mapping->internalfield}</td></tr>";
}
echo '</table>';

echo html_writer::tag('div', 
    html_writer::link(new moodle_url('/admin/tool/oauth2/fix_github_endpoints.php'), 'Run Fix Script', ['class' => 'btn btn-warning']) . ' ' .
    html_writer::link(new moodle_url('/admin/tool/oauth2/issuers.php'), 'Back to OAuth 2 services', ['class' => 'btn btn-primary']),
    ['class' => 'mt-3']
);

echo $OUTPUT->footer();