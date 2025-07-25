<?php
require_once('config.php');

require_login();
require_capability('moodle/site:config', context_system::instance());

echo '<h2>AMD Module Diagnostic</h2>';

// Check if the core/first file exists
$firstjs = $CFG->dirroot . '/lib/amd/build/first.min.js';
echo '<h3>1. Checking core/first module:</h3>';
if (file_exists($firstjs)) {
    echo '<p style="color: green;">✓ File exists: ' . $firstjs . '</p>';
    $content = file_get_contents($firstjs);
    echo '<p>File size: ' . strlen($content) . ' bytes</p>';
    echo '<p>First 200 chars: <code>' . htmlspecialchars(substr($content, 0, 200)) . '</code></p>';
    
    // Check if it has a proper define
    if (strpos($content, 'define') !== false) {
        echo '<p style="color: green;">✓ Contains define() call</p>';
    } else {
        echo '<p style="color: red;">✗ Missing define() call</p>';
    }
} else {
    echo '<p style="color: red;">✗ File does not exist</p>';
}

// Check RequireJS configuration
echo '<h3>2. RequireJS Configuration:</h3>';
echo '<pre>';
echo "requirejs.dir = " . (isset($CFG->requirejs_dir) ? $CFG->requirejs_dir : 'not set') . "\n";
echo "jsrev = " . $CFG->jsrev . "\n";
echo "cachejs = " . ($CFG->cachejs ? 'true' : 'false') . "\n";
echo '</pre>';

// Try to regenerate the file
echo '<h3>3. Regenerate core/first.min.js:</h3>';
$source = $CFG->dirroot . '/lib/amd/src/first.js';
if (file_exists($source)) {
    echo '<p>Source file exists. Creating minified version...</p>';
    $sourceContent = file_get_contents($source);
    
    // Simple minification - just remove comments and extra whitespace
    $minified = preg_replace('/\/\*[\s\S]*?\*\/|\/\/.*$/m', '', $sourceContent);
    $minified = preg_replace('/\s+/', ' ', $minified);
    $minified = trim($minified);
    
    // Backup existing file
    if (file_exists($firstjs)) {
        copy($firstjs, $firstjs . '.backup');
        echo '<p>Backed up existing file to first.min.js.backup</p>';
    }
    
    // Write new minified file
    if (file_put_contents($firstjs, $minified)) {
        echo '<p style="color: green;">✓ Successfully created new first.min.js</p>';
        echo '<p>New content: <code>' . htmlspecialchars($minified) . '</code></p>';
    } else {
        echo '<p style="color: red;">✗ Failed to write first.min.js</p>';
    }
}

echo '<h3>4. Solutions:</h3>';
echo '<ol>';
echo '<li><strong>Use the non-AMD version of the report</strong> (already implemented)</li>';
echo '<li>Clear browser cache and try again</li>';
echo '<li>Run grunt to rebuild all AMD modules (if you have development environment)</li>';
echo '<li>Disable JavaScript caching: Site administration → Development → Debugging → Cache JavaScript = No</li>';
echo '</ol>';

echo '<h3>5. Access the Working Report:</h3>';
echo '<p><a href="/report/codeprogress/index_noamd.php?course=2" class="btn btn-primary">Go to Coding Progress Report (Non-AMD Version)</a></p>';