<?php
require_once('config.php');
require_once($CFG->libdir.'/externallib.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

echo '<h2>Web Service Configuration Check</h2>';

// Check if web services are enabled
echo '<h3>1. Web Services Status:</h3>';
if (!empty($CFG->enablewebservices)) {
    echo '<p style="color: green;">✓ Web services are ENABLED</p>';
} else {
    echo '<p style="color: red;">✗ Web services are DISABLED</p>';
    echo '<p>To enable: Site administration → Advanced features → Enable web services</p>';
}

// Check if AJAX/REST protocols are enabled  
echo '<h3>2. REST Protocol:</h3>';
$activeprotocols = empty($CFG->webserviceprotocols) ? array() : explode(',', $CFG->webserviceprotocols);
if (in_array('rest', $activeprotocols)) {
    echo '<p style="color: green;">✓ REST protocol is ENABLED</p>';
} else {
    echo '<p style="color: red;">✗ REST protocol is DISABLED</p>';
    echo '<p>To enable: Site administration → Plugins → Web services → Manage protocols</p>';
}

// Check if our function exists
echo '<h3>3. Custom API Function:</h3>';
$function = 'local_customapi_get_sandbox_grades';
$functioninfo = external_api::external_function_info($function);
if ($functioninfo) {
    echo '<p style="color: green;">✓ Function ' . $function . ' is registered</p>';
    echo '<pre>' . print_r($functioninfo, true) . '</pre>';
} else {
    echo '<p style="color: red;">✗ Function ' . $function . ' is NOT registered</p>';
}

// Check AJAX availability
echo '<h3>4. AJAX Availability:</h3>';
if (isset($functioninfo->ajax) && $functioninfo->ajax) {
    echo '<p style="color: green;">✓ Function is available for AJAX calls</p>';
} else {
    echo '<p style="color: red;">✗ Function is NOT available for AJAX calls</p>';
}

echo '<h3>Quick Fix Instructions:</h3>';
echo '<ol>';
echo '<li>Go to Site administration → Advanced features</li>';
echo '<li>Check "Enable web services" and save</li>';
echo '<li>Go to Site administration → Plugins → Web services → Manage protocols</li>';
echo '<li>Enable REST protocol</li>';
echo '<li>Go to Site administration → Notifications to update plugins</li>';
echo '<li>Purge all caches</li>';
echo '</ol>';