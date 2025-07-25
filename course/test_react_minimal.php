<?php
require_once('../config.php');
require_login();

// Minimal page setup
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/course/test_react_minimal.php');
$PAGE->set_pagelayout('embedded'); // Use minimal layout
$PAGE->set_title('Minimal React Test');

// Only load our React bundle, nothing else
echo '<!DOCTYPE html>
<html>
<head>
    <title>Minimal React Test</title>
    <link rel="stylesheet" href="' . $CFG->wwwroot . '/react-dist/style.css">
</head>
<body>
    <h1>Minimal React Components Test</h1>
    
    <div id="category-test" style="border: 1px solid #ccc; padding: 20px; margin: 20px;">
        Loading categories...
    </div>
    
    <div id="course-test" style="border: 1px solid #ccc; padding: 20px; margin: 20px;">
        Loading courses...
    </div>
    
    <script>
        // Set up minimal M object
        window.M = {
            cfg: {
                sesskey: "' . sesskey() . '",
                wwwroot: "' . $CFG->wwwroot . '"
            }
        };
        console.log("M.cfg setup:", window.M.cfg);
    </script>
    
    <script src="' . $CFG->wwwroot . '/react-dist/moodle-react.iife.js"></script>
    
    <script>
        // Wait for MoodleReact and mount components
        function initComponents() {
            if (typeof window.MoodleReact !== "undefined") {
                console.log("MoodleReact available, mounting components...");
                
                try {
                    window.MoodleReact.mount("CategoryManagementPanel", "#category-test", {
                        initialCategoryId: 0,
                        capabilities: {
                            "moodle/category:manage": true,
                            "moodle/course:create": true
                        }
                    });
                    console.log("Category panel mounted");
                } catch (e) {
                    console.error("Category mount error:", e);
                }
                
                try {
                    window.MoodleReact.mount("CourseManagementPanel", "#course-test", {
                        categoryId: 0,
                        capabilities: {
                            "moodle/course:update": true,
                            "moodle/course:visibility": true
                        }
                    });
                    console.log("Course panel mounted");
                } catch (e) {
                    console.error("Course mount error:", e);
                }
            } else {
                setTimeout(initComponents, 100);
            }
        }
        
        initComponents();
    </script>
</body>
</html>';
?>