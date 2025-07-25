<?php
// This file is part of Moodle - http://moodle.org/

define('AJAX_SCRIPT', true);

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/coursecatlib.php');

// Require login
require_login();

// Validate sesskey if provided
$sesskey = optional_param('sesskey', '', PARAM_RAW);
if (!empty($sesskey) && !confirm_sesskey($sesskey)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid sesskey']);
    exit;
}

// Check capabilities
$systemcontext = context_system::instance();
if (!has_any_capability(['moodle/category:manage', 'moodle/course:create'], $systemcontext)) {
    http_response_code(403);
    echo json_encode(['error' => 'No permission to access courses']);
    exit;
}

// Get parameters
$action = required_param('action', PARAM_ALPHA);
$categoryid = optional_param('categoryid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

header('Content-Type: application/json');

try {
    switch ($action) {
        case 'list':
            // Get courses for a category
            $courses = [];
            $totalcount = 0;
            
            if ($categoryid > 0) {
                $category = coursecat::get($categoryid);
                $courselist = $category->get_courses([
                    'offset' => $page * $perpage,
                    'limit' => $perpage,
                    'sort' => ['fullname' => 1]
                ]);
                $totalcount = $category->get_courses_count();
            } else {
                // Get all courses (careful with this!)
                $courselist = get_courses('all', 'c.fullname ASC', 'c.*', '', $page * $perpage, $perpage);
                $totalcount = $DB->count_records('course') - 1; // Exclude site course
            }
            
            foreach ($courselist as $course) {
                if ($course->id == SITEID) continue; // Skip site course
                
                $courses[] = [
                    'id' => $course->id,
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'idnumber' => $course->idnumber,
                    'summary' => $course->summary,
                    'visible' => $course->visible,
                    'categoryid' => $course->category,
                    'startdate' => $course->startdate,
                    'enddate' => $course->enddate,
                    'timecreated' => $course->timecreated,
                    'timemodified' => $course->timemodified
                ];
            }
            
            echo json_encode([
                'success' => true,
                'courses' => $courses,
                'totalcount' => $totalcount,
                'page' => $page,
                'perpage' => $perpage
            ]);
            break;
            
        case 'info':
            // Get detailed info about a specific course
            $courseid = required_param('courseid', PARAM_INT);
            $course = get_course($courseid);
            
            // Get course context for additional info
            $context = context_course::instance($courseid);
            
            // Get enrollment count
            $enrolledcount = $DB->count_records('user_enrolments', [
                'status' => ENROL_USER_ACTIVE
            ], 'SELECT COUNT(DISTINCT ue.userid) FROM {user_enrolments} ue 
               JOIN {enrol} e ON e.id = ue.enrolid 
               WHERE e.courseid = ? AND ue.status = ?', [$courseid, ENROL_USER_ACTIVE]);
            
            echo json_encode([
                'success' => true,
                'course' => [
                    'id' => $course->id,
                    'fullname' => $course->fullname,
                    'shortname' => $course->shortname,
                    'idnumber' => $course->idnumber,
                    'summary' => $course->summary,
                    'visible' => $course->visible,
                    'categoryid' => $course->category,
                    'format' => $course->format,
                    'startdate' => $course->startdate,
                    'enddate' => $course->enddate,
                    'timecreated' => $course->timecreated,
                    'timemodified' => $course->timemodified,
                    'enrolledcount' => $enrolledcount,
                    'groupmode' => $course->groupmode,
                    'enablecompletion' => $course->enablecompletion,
                    'showgrades' => $course->showgrades
                ]
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>