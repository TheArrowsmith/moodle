<?php
// This file is part of Moodle - http://moodle.org/
//
// Example of how to integrate React Course Management components
// into the existing course/management.php page

require_once('../config.php');
require_once($CFG->dirroot.'/lib/coursecatlib.php');

// Load the React helper if it exists
if (file_exists($CFG->libdir . '/react_helper.php')) {
    require_once($CFG->libdir . '/react_helper.php');
}

$categoryid = optional_param('categoryid', null, PARAM_INT);
$courseid = optional_param('courseid', null, PARAM_INT);
$viewmode = optional_param('view', 'default', PARAM_ALPHA);

// Security and context setup (same as original management.php)
require_login();

if (!coursecat::has_capability_on_any(array('moodle/category:manage', 'moodle/course:create'))) {
    redirect(new moodle_url('/course/index.php'));
}

$systemcontext = context_system::instance();
$PAGE->set_context($systemcontext);
$PAGE->set_url(new moodle_url('/course/management_react_example.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('coursecatmanagement'));
$PAGE->set_heading(format_string($SITE->fullname, true, array('context' => $systemcontext)));

// Get user capabilities for React components
$capabilities = array(
    'moodle/category:manage' => has_capability('moodle/category:manage', $systemcontext),
    'moodle/course:create' => has_capability('moodle/course:create', $systemcontext),
    'moodle/course:update' => has_capability('moodle/course:update', $systemcontext),
    'moodle/course:delete' => has_capability('moodle/course:delete', $systemcontext),
    'moodle/course:visibility' => has_capability('moodle/course:visibility', $systemcontext),
    'moodle/category:viewhiddencategories' => has_capability('moodle/category:viewhiddencategories', $systemcontext)
);

// Include React bundle - check if in debug mode for dev server
if ($CFG->debug && $CFG->debugdeveloper) {
    // Development mode - load from Vite dev server
    $PAGE->requires->js_module('http://localhost:5173/@vite/client');
    $PAGE->requires->js_module('http://localhost:5173/src/main.jsx');
} else {
    // Production mode - load built bundle
    $PAGE->requires->js('/react-dist/moodle-react.iife.js');
    $PAGE->requires->css('/react-dist/style.css');
}

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('coursecatmanagement'));

?>

<div class="course-category-listings columns-3">
    
    <!-- Category Panel - Replace with React -->
    <div class="category-listing" id="category-management-panel">
        <div class="loading-placeholder">
            <h3><?php echo get_string('categories'); ?></h3>
            <p>Loading categories...</p>
        </div>
    </div>
    
    <!-- Course Panel - Replace with React -->
    <div class="course-listing" id="course-management-panel">
        <div class="loading-placeholder">
            <h3>Courses</h3>
            <p>Loading courses...</p>
        </div>
    </div>
    
    <!-- Course Detail Panel - Replace with React (if course selected) -->
    <?php if ($courseid): ?>
    <div class="course-detail" id="course-detail-panel">
        <div class="loading-placeholder">
            <h3>Course Details</h3>
            <p>Loading course details...</p>
        </div>
    </div>
    <?php endif; ?>
    
</div>

<script>
// Wait for MoodleReact to be available, then mount components
document.addEventListener('DOMContentLoaded', function() {
    function initReactComponents() {
        if (typeof window.MoodleReact === 'undefined') {
            // Retry in 100ms if MoodleReact not ready
            setTimeout(initReactComponents, 100);
            return;
        }
        
        // Mount Category Management Panel
        window.MoodleReact.mount('CategoryManagementPanel', '#category-management-panel', {
            initialCategoryId: <?php echo json_encode($categoryid ?: 0); ?>,
            selectedCategoryId: <?php echo json_encode($categoryid); ?>,
            capabilities: <?php echo json_encode($capabilities); ?>,
            onCategorySelect: function(category) {
                // Update URL and load courses for selected category
                const url = new URL(window.location);
                url.searchParams.set('categoryid', category.id);
                if (url.searchParams.has('courseid')) {
                    url.searchParams.delete('courseid');
                }
                window.history.pushState({}, '', url);
                
                // Reload course panel
                if (window.courseManagementInstance) {
                    window.courseManagementInstance.updateProps({
                        categoryId: category.id,
                        selectedCourseId: null
                    });
                }
            }
        });
        
        // Mount Course Management Panel
        window.courseManagementInstance = window.MoodleReact.mount('CourseManagementPanel', '#course-management-panel', {
            categoryId: <?php echo json_encode($categoryid ?: 0); ?>,
            selectedCourseId: <?php echo json_encode($courseid); ?>,
            viewMode: <?php echo json_encode($viewmode); ?>,
            capabilities: <?php echo json_encode($capabilities); ?>,
            onCourseSelect: function(course) {
                // Update URL and show course details
                const url = new URL(window.location);
                url.searchParams.set('courseid', course.id);
                window.history.pushState({}, '', url);
                
                // Mount or update course detail panel
                const detailContainer = document.getElementById('course-detail-panel');
                if (!detailContainer) {
                    // Create detail panel if it doesn't exist
                    const newPanel = document.createElement('div');
                    newPanel.id = 'course-detail-panel';
                    newPanel.className = 'course-detail';
                    document.querySelector('.course-category-listings').appendChild(newPanel);
                    document.querySelector('.course-category-listings').className = 'course-category-listings columns-3 course-selected';
                }
                
                window.MoodleReact.mount('CourseDetailPanel', '#course-detail-panel', {
                    courseId: course.id,
                    capabilities: <?php echo json_encode($capabilities); ?>,
                    onClose: function() {
                        // Remove course selection
                        const url = new URL(window.location);
                        url.searchParams.delete('courseid');
                        window.history.pushState({}, '', url);
                        
                        // Hide detail panel
                        const detailPanel = document.getElementById('course-detail-panel');
                        if (detailPanel) {
                            detailPanel.remove();
                            document.querySelector('.course-category-listings').className = 'course-category-listings columns-2';
                        }
                    }
                });
            }
        });
        
        <?php if ($courseid): ?>
        // Mount Course Detail Panel if course is pre-selected
        window.MoodleReact.mount('CourseDetailPanel', '#course-detail-panel', {
            courseId: <?php echo json_encode($courseid); ?>,
            capabilities: <?php echo json_encode($capabilities); ?>,
            onClose: function() {
                const url = new URL(window.location);
                url.searchParams.delete('courseid');
                window.history.pushState({}, '', url);
                location.reload(); // Simple reload for now
            }
        });
        <?php endif; ?>
    }
    
    initReactComponents();
});
</script>

<style>
/* Layout styles to match original management.php */
.course-category-listings {
    display: grid;
    gap: 1rem;
    margin: 1rem 0;
    min-height: 600px;
}

.course-category-listings.columns-2 {
    grid-template-columns: 300px 1fr;
}

.course-category-listings.columns-3 {
    grid-template-columns: 300px 1fr 350px;
}

.category-listing,
.course-listing,
.course-detail {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    overflow: hidden;
}

.loading-placeholder {
    padding: 2rem;
    text-align: center;
    color: #6c757d;
}

.loading-placeholder h3 {
    margin: 0 0 1rem 0;
}

/* Responsive layout */
@media (max-width: 1200px) {
    .course-category-listings.columns-3 {
        grid-template-columns: 250px 1fr 300px;
    }
}

@media (max-width: 768px) {
    .course-category-listings {
        grid-template-columns: 1fr !important;
    }
    
    .course-detail {
        order: -1; /* Show course details first on mobile */
    }
}
</style>

<?php
echo $OUTPUT->footer();
?>