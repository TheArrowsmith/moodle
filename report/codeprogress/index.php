<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Main page for report_codeprogress
 *
 * @package    report_codeprogress
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->libdir.'/adminlib.php');

$courseid = required_param('course', PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);
require_capability('report/codeprogress:view', $context);

// Check if we have any code sandbox activities
$hasactivities = $DB->record_exists('codesandbox', array('course' => $courseid));

if ($format === 'csv' && $hasactivities) {
    // Export CSV
    require_once($CFG->libdir . '/csvlib.class.php');
    
    $filename = clean_filename('codeprogress_' . $course->shortname . '_' . date('Y-m-d'));
    $csvexport = new csv_export_writer();
    $csvexport->set_filename($filename);
    
    // Get data via API
    require_once($CFG->dirroot . '/local/customapi/classes/external.php');
    $data = local_customapi_external::get_sandbox_grades($courseid);
    
    // Organize data
    $students = array();
    $activities = array();
    
    foreach ($data as $record) {
        if (!isset($students[$record->userid])) {
            $students[$record->userid] = array(
                'name' => $record->fullname,
                'grades' => array()
            );
        }
        $students[$record->userid]['grades'][$record->activityid] = $record->grade;
        $activities[$record->activityid] = $record->activityname;
    }
    
    // Write headers
    $headers = array('Student');
    foreach ($activities as $name) {
        $headers[] = $name;
    }
    $csvexport->add_data($headers);
    
    // Write data
    foreach ($students as $student) {
        $row = array($student['name']);
        foreach (array_keys($activities) as $actid) {
            $row[] = isset($student['grades'][$actid]) ? $student['grades'][$actid] : '-';
        }
        $csvexport->add_data($row);
    }
    
    $csvexport->download_file();
    die;
}

$PAGE->set_url('/report/codeprogress/index.php', array('course' => $courseid));
$PAGE->set_title(get_string('progressreport', 'report_codeprogress'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('report');

// Include Chart.js
$PAGE->requires->js(new moodle_url('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js'), true);

// Get data via API directly for initial load
require_once($CFG->dirroot . '/local/customapi/classes/external.php');
try {
    $data = local_customapi_external::get_sandbox_grades($courseid);
    $datajson = json_encode($data);
    
    // Debug: Check if we have data
    if (debugging() && !empty($data)) {
        debugging('Loaded ' . count($data) . ' grade records', DEBUG_DEVELOPER);
    }
} catch (Exception $e) {
    $datajson = json_encode(array());
    debugging('Error loading grades: ' . $e->getMessage(), DEBUG_DEVELOPER);
}

// Include custom CSS
$PAGE->requires->css('/report/codeprogress/styles.css');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('codingprogress', 'report_codeprogress'));

if (!$hasactivities) {
    echo $OUTPUT->notification(get_string('nocodesandboxes', 'report_codeprogress'), 'info');
    echo $OUTPUT->footer();
    die;
}

// Add export button
$exporturl = new moodle_url('/report/codeprogress/index.php', array(
    'course' => $courseid,
    'format' => 'csv'
));
echo html_writer::div(
    html_writer::link($exporturl, get_string('exportcsv', 'report_codeprogress'), 
                     array('class' => 'btn btn-secondary')),
    'mb-3'
);

// Process data server-side
$students = array();
$activities = array();

foreach ($data as $record) {
    if (!isset($students[$record->userid])) {
        $students[$record->userid] = (object)array(
            'id' => $record->userid,
            'name' => $record->fullname,
            'grades' => array()
        );
    }
    
    if (!isset($activities[$record->activityid])) {
        $activities[$record->activityid] = (object)array(
            'id' => $record->activityid,
            'name' => $record->activityname
        );
    }
    
    $students[$record->userid]->grades[$record->activityid] = (object)array(
        'grade' => $record->grade,
        'status' => $record->submissionstatus
    );
}

// Calculate statistics
$totalStudents = count($students);
$totalActivities = count($activities);
$allScores = array();
$submittedCount = 0;
$totalPossible = $totalStudents * $totalActivities;

foreach ($students as $student) {
    foreach ($activities as $activity) {
        if (isset($student->grades[$activity->id])) {
            if ($student->grades[$activity->id]->status === 'submitted') {
                $submittedCount++;
                if ($student->grades[$activity->id]->grade !== null) {
                    $allScores[] = floatval($student->grades[$activity->id]->grade);
                }
            }
        }
    }
}

$overallAverage = count($allScores) > 0 ? round(array_sum($allScores) / count($allScores), 1) : 0;
$completionRate = $totalPossible > 0 ? round(($submittedCount / $totalPossible) * 100, 1) : 0;

// Render server-side with inline JavaScript
include(__DIR__ . '/index_inline.php');

echo $OUTPUT->footer();