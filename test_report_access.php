<?php
require_once('config.php');
require_once($CFG->libdir.'/adminlib.php');

// Get course id from parameter or use default
$courseid = optional_param('course', 2, PARAM_INT); // Default to course 2

// Check if user is logged in
require_login();

// Direct link to the report (non-AMD version to avoid JavaScript issues)
$reporturl = new moodle_url('/report/codeprogress/index_noamd.php', array('course' => $courseid));

echo "<h2>Direct Report Access Test</h2>";
echo "<p>If the report plugin is installed correctly, you should be able to access it using the link below:</p>";
echo "<p><a href='{$reporturl}' class='btn btn-primary'>Go to Coding Progress Report for Course $courseid</a></p>";

// Check if user has capability
$course = $DB->get_record('course', array('id' => $courseid));
if ($course) {
    $context = context_course::instance($courseid);
    if (has_capability('report/codeprogress:view', $context)) {
        echo "<p style='color: green;'>✓ You have permission to view the report</p>";
    } else {
        echo "<p style='color: red;'>✗ You don't have permission to view the report</p>";
    }
}

// Check if plugin is installed
$plugin_installed = $DB->record_exists('config_plugins', array('plugin' => 'report_codeprogress'));
if ($plugin_installed) {
    echo "<p style='color: green;'>✓ Plugin is installed in database</p>";
} else {
    echo "<p style='color: red;'>✗ Plugin is NOT installed in database</p>";
    echo "<p>You may need to visit <a href='/admin/index.php'>Site administration</a> to trigger plugin installation.</p>";
}

// Check navigation
echo "<h3>Navigation Debug:</h3>";
echo "<div class='alert alert-warning'>";
echo "<h4>Why you don't see 'Reports' in the navigation:</h4>";
echo "<p>In Moodle 3.5, the 'Reports' section only appears if you have the <code>moodle/site:viewreports</code> capability, which is typically only given to managers and admins, not teachers.</p>";
echo "<p>Our report uses <code>report/codeprogress:view</code> which teachers have, but this doesn't make the Reports menu appear.</p>";
echo "</div>";

echo "<h3>Solutions:</h3>";
echo "<ol>";
echo "<li><strong>Direct Access (Recommended for now):</strong> Use the direct link above or bookmark: <code>/report/codeprogress/index.php?course=COURSEID</code></li>";
echo "<li><strong>Add a Block:</strong> As admin, add the 'Coding Progress' block to the course (after installing it via admin notifications)</li>";
echo "<li><strong>Grant Permission:</strong> As admin, grant teachers the <code>moodle/site:viewreports</code> capability at the course level</li>";
echo "<li><strong>Custom Navigation:</strong> Add a custom menu item in your theme</li>";
echo "</ol>";

echo "<h3>For Testing:</h3>";
echo "<p>Since you're logged in as a teacher, the easiest way is to use the direct link above. The report is fully functional!</p>";