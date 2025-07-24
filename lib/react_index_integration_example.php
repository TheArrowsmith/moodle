<?php
/**
 * Example integration code for index.php
 * 
 * This file shows how to safely integrate React components into Moodle's index.php
 * with proper error handling and fallbacks.
 */

// This code should be added to index.php before echo $OUTPUT->footer();

// React component integration with error handling
try {
    // Check if React helper exists
    $react_helper_path = $CFG->libdir . '/react_helper.php';
    if (!file_exists($react_helper_path)) {
        // Log error but don't break the page
        debugging('React helper not found at: ' . $react_helper_path, DEBUG_DEVELOPER);
    } else {
        require_once($react_helper_path);
        
        // Check if user is logged in for personalized content
        if (isloggedin() && !isguestuser()) {
            $props = [
                'userName' => fullname($USER),
                'userId' => $USER->id,
                'courseName' => get_string('sitehome'),
                'isGuest' => false,
                'capabilities' => [
                    'canViewReports' => has_capability('moodle/site:viewreports', context_system::instance()),
                    'canManageCourses' => has_capability('moodle/course:create', context_system::instance())
                ]
            ];
        } else {
            $props = [
                'userName' => get_string('guest'),
                'userId' => 0,
                'courseName' => get_string('sitehome'),
                'isGuest' => true,
                'capabilities' => []
            ];
        }
        
        // Add environment info for debugging
        if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
            $props['debug'] = true;
            $props['environment'] = [
                'moodleVersion' => $CFG->version,
                'phpVersion' => phpversion(),
                'theme' => $PAGE->theme->name
            ];
        }
        
        // Wrap in a container for better layout control
        echo '<div class="container-fluid mt-3" id="react-integration-container">';
        echo '<div class="row">';
        echo '<div class="col-12">';
        
        // Optional: Add a heading
        if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
            echo '<h2 class="mb-3">' . get_string('React Integration Test', 'local_react') . '</h2>';
        }
        
        // Render the React component with error handling
        if (function_exists('render_react_component')) {
            render_react_component('HelloMoodle', 'react-hello-moodle', $props, [
                'class' => 'react-test-component mb-4'
            ]);
            
            // Add a noscript fallback
            echo '<noscript>';
            echo '<div class="alert alert-warning">';
            echo get_string('javascriptdisabled', 'admin');
            echo '</div>';
            echo '</noscript>';
        } else {
            debugging('render_react_component function not found', DEBUG_DEVELOPER);
        }
        
        echo '</div>';
        echo '</div>';
        echo '</div>';
        
        // Add some inline CSS for development visibility
        if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
            echo '<style>
                #react-integration-container {
                    border: 2px dashed #007bff;
                    padding: 20px;
                    margin: 20px 0;
                    background: rgba(0, 123, 255, 0.05);
                }
                #react-integration-container h2 {
                    color: #007bff;
                }
            </style>';
        }
    }
} catch (Exception $e) {
    // Log the error but don't break the page
    debugging('React integration error: ' . $e->getMessage(), DEBUG_DEVELOPER);
}

// Alternative: Minimal integration without try-catch
// Uncomment this and comment out the above for a simpler approach
/*
if (file_exists($CFG->libdir . '/react_helper.php')) {
    require_once($CFG->libdir . '/react_helper.php');
    
    render_react_component('HelloMoodle', 'react-hello-moodle', [
        'userName' => isloggedin() ? fullname($USER) : get_string('guest'),
        'courseName' => get_string('sitehome')
    ]);
}
*/

// For production, you might want an even simpler integration:
/*
require_once($CFG->libdir . '/react_helper.php');
render_react_component('WelcomeBanner', 'welcome-banner', [
    'siteName' => format_string($SITE->fullname)
]);
*/