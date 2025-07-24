<?php
// This file is part of Moodle - http://moodle.org/
//
// Example page showing React authentication component integration

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/react_helper.php');
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');

use local_courseapi\jwt;

// Page setup
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/local/courseapi/test_react_auth.php');
$PAGE->set_title('React Authentication Test');
$PAGE->set_heading('React Authentication Component Test');

// Require login
require_login();

// Start output
echo $OUTPUT->header();

// Generate JWT token for current user
$token = jwt::create_token($USER->id);

echo '<h2>React AuthenticatedUserDisplay Component</h2>';
echo '<p>This demonstrates the React component authenticating with the Course API using JWT.</p>';

// Method 1: Using react_helper.php
echo '<div class="card">';
echo '<div class="card-body">';
echo '<h3>Method 1: Using react_helper</h3>';
render_react_component('AuthenticatedUserDisplay', 'auth-display-1', [
    'token' => $token,
    'apiUrl' => $CFG->wwwroot . '/local/courseapi/api'
]);
echo '</div>';
echo '</div>';

echo '<br>';

// Method 2: Manual integration
echo '<div class="card">';
echo '<div class="card-body">';
echo '<h3>Method 2: Manual Integration</h3>';
echo '<div id="auth-display-2"></div>';

// Load React bundle based on debug mode
if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
    // Development mode
    echo '<script type="module" src="http://localhost:5173/@vite/client"></script>';
    echo '<script type="module" src="http://localhost:5173/src/main.jsx"></script>';
} else {
    // Production mode
    echo '<link rel="stylesheet" href="' . $CFG->wwwroot . '/react-dist/style.css">';
    echo '<script src="' . $CFG->wwwroot . '/react-dist/moodle-react.iife.js"></script>';
}

// Mount component
echo '<script>
(function() {
    var checkReact = setInterval(function() {
        if (window.MoodleReact && window.MoodleReact.mount) {
            clearInterval(checkReact);
            window.MoodleReact.mount(
                "AuthenticatedUserDisplay",
                "#auth-display-2",
                {
                    token: "' . $token . '",
                    apiUrl: "' . $CFG->wwwroot . '/local/courseapi/api"
                }
            );
        }
    }, 100);
})();
</script>';

echo '</div>';
echo '</div>';

// Debug information
if ($CFG->debugdeveloper) {
    echo '<br>';
    echo '<div class="card">';
    echo '<div class="card-body">';
    echo '<h3>Debug Information</h3>';
    echo '<pre>';
    echo 'User ID: ' . $USER->id . "\n";
    echo 'Username: ' . $USER->username . "\n";
    echo 'Token (truncated): ' . substr($token, 0, 20) . '...' . "\n";
    echo 'API URL: ' . $CFG->wwwroot . '/local/courseapi/api' . "\n";
    echo '</pre>';
    echo '</div>';
    echo '</div>';
}

echo $OUTPUT->footer();