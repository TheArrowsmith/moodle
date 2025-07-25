<?php
require_once('../config.php');
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');

require_login();

$PAGE->set_context(context_system::instance());
$PAGE->set_url('/course/test_react.php');
$PAGE->set_pagelayout('admin');
$PAGE->set_title('Test React Components');

// Generate JWT token for the current user
$token = local_courseapi\jwt::create_token($USER->id);

// Include React bundle
$PAGE->requires->js('/react-dist/moodle-react.iife.js');
$PAGE->requires->css('/react-dist/style.css');

// Ensure M object is available with sesskey
$PAGE->requires->js_init_code('
    if (!window.M) window.M = {};
    if (!window.M.cfg) window.M.cfg = {};
    window.M.cfg.sesskey = "' . sesskey() . '";
    window.M.cfg.wwwroot = "' . $CFG->wwwroot . '";
');

echo $OUTPUT->header();
?>

<h1>Test React Components</h1>

<div style="border: 1px solid #eee; padding: 1rem; margin: 1rem 0; background: #f9f9f9;">
    <h3>Debug Information</h3>
    <p><strong>JWT Token Generated:</strong> <?php echo substr($token, 0, 50); ?>...</p>
    <p><strong>User:</strong> <?php echo fullname($USER); ?> (ID: <?php echo $USER->id; ?>)</p>
    <p><strong>API Base URL:</strong> <?php echo $CFG->wwwroot; ?>/local/courseapi/api/</p>
</div>

<div style="border: 1px solid #ccc; padding: 1rem; margin: 1rem 0;">
    <h3>Category Panel Test</h3>
    <div id="category-test" style="height: 400px; background: #f9f9f9;">
        Loading category panel...
    </div>
</div>

<div style="border: 1px solid #ccc; padding: 1rem; margin: 1rem 0;">
    <h3>Course Panel Test</h3>
    <div id="course-test" style="height: 400px; background: #f9f9f9;">
        Loading course panel...
    </div>
</div>

<script>
console.log('Page loaded, checking for MoodleReact...');
console.log('M.cfg available?', typeof window.M !== 'undefined' && window.M.cfg);
console.log('M.cfg.wwwroot:', window.M?.cfg?.wwwroot);
console.log('M.cfg.sesskey:', window.M?.cfg?.sesskey);

function waitForMoodleReact() {
    if (typeof window.MoodleReact !== 'undefined') {
        console.log('MoodleReact available:', window.MoodleReact);
        console.log('Available components:', Object.keys(window.MoodleReact.components));
        
        // Test Category Panel
        try {
            window.MoodleReact.mount('CategoryManagementPanel', '#category-test', {
                initialCategoryId: 0,
                capabilities: {
                    'moodle/category:manage': true,
                    'moodle/course:create': true
                },
                token: '<?php echo $token; ?>'
            });
            console.log('Category panel mounted successfully');
        } catch (e) {
            console.error('Failed to mount category panel:', e);
        }
        
        // Test Course Panel
        try {
            window.MoodleReact.mount('CourseManagementPanel', '#course-test', {
                categoryId: 0,
                capabilities: {
                    'moodle/course:update': true,
                    'moodle/course:visibility': true
                },
                token: '<?php echo $token; ?>'
            });
            console.log('Course panel mounted successfully');
        } catch (e) {
            console.error('Failed to mount course panel:', e);
        }
    } else {
        console.log('MoodleReact not ready yet, retrying...');
        setTimeout(waitForMoodleReact, 100);
    }
}

document.addEventListener('DOMContentLoaded', waitForMoodleReact);
</script>

<?php
echo $OUTPUT->footer();
?>