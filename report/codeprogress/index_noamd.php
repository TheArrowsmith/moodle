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
 * Main page for report_codeprogress - Non-AMD version
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

$PAGE->set_url('/report/codeprogress/index_noamd.php', array('course' => $courseid));
$PAGE->set_title(get_string('progressreport', 'report_codeprogress'));
$PAGE->set_heading($course->fullname);
$PAGE->set_pagelayout('report');

// Include Chart.js directly
$PAGE->requires->js(new moodle_url('https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js'), true);

// Include custom CSS
$PAGE->requires->css('/report/codeprogress/styles.css');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('codingprogress', 'report_codeprogress'));

if (!$hasactivities) {
    echo $OUTPUT->notification(get_string('nocodesandboxes', 'report_codeprogress'), 'info');
    echo $OUTPUT->footer();
    die;
}

// Get data via API
require_once($CFG->dirroot . '/local/customapi/classes/external.php');
try {
    $data = local_customapi_external::get_sandbox_grades($courseid);
} catch (Exception $e) {
    echo $OUTPUT->notification('Error loading data: ' . $e->getMessage(), 'error');
    echo $OUTPUT->footer();
    die;
}

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

// Add export button
$exporturl = new moodle_url('/report/codeprogress/index_noamd.php', array(
    'course' => $courseid,
    'format' => 'csv'
));
echo html_writer::div(
    html_writer::link($exporturl, get_string('exportcsv', 'report_codeprogress'), 
                     array('class' => 'btn btn-secondary')),
    'mb-3'
);

// Display the table server-side
echo '<div id="progress-dashboard-container">';
echo '<div id="progress-table-container">';
echo '<div class="table-responsive">';
echo '<table class="table table-striped table-hover" id="progress-table">';
echo '<thead class="thead-light">';
echo '<tr>';
echo '<th class="sticky-col">' . get_string('student', 'report_codeprogress') . '</th>';

foreach ($activities as $activity) {
    echo '<th class="text-center">' . s($activity->name) . '</th>';
}
echo '</tr>';
echo '</thead>';
echo '<tbody>';

// Render student rows
foreach ($students as $student) {
    echo '<tr>';
    echo '<td class="sticky-col">' . s($student->name) . '</td>';
    
    foreach ($activities as $activity) {
        echo '<td class="text-center">';
        if (isset($student->grades[$activity->id]) && $student->grades[$activity->id]->status === 'submitted') {
            $grade = $student->grades[$activity->id]->grade ?: 0;
            $class = '';
            if ($grade >= 80) {
                $class = 'text-success';
            } else if ($grade >= 60) {
                $class = 'text-warning';
            } else {
                $class = 'text-danger';
            }
            echo '<span class="' . $class . '">' . $grade . '%</span>';
        } else {
            echo '<span class="text-muted">-</span>';
        }
        echo '</td>';
    }
    echo '</tr>';
}

// Calculate and display averages
echo '</tbody>';
echo '<tfoot>';
echo '<tr class="font-weight-bold">';
echo '<td class="sticky-col">Average</td>';

foreach ($activities as $activity) {
    $scores = array();
    foreach ($students as $student) {
        if (isset($student->grades[$activity->id]) && 
            $student->grades[$activity->id]->status === 'submitted' && 
            $student->grades[$activity->id]->grade !== null) {
            $scores[] = floatval($student->grades[$activity->id]->grade);
        }
    }
    
    echo '<td class="text-center">';
    if (count($scores) > 0) {
        $avg = round(array_sum($scores) / count($scores), 1);
        $class = '';
        if ($avg >= 80) {
            $class = 'text-success';
        } else if ($avg >= 60) {
            $class = 'text-warning';
        } else {
            $class = 'text-danger';
        }
        echo '<span class="' . $class . '">' . $avg . '%</span>';
    } else {
        echo '-';
    }
    echo '</td>';
}

echo '</tr>';
echo '</tfoot>';
echo '</table>';
echo '</div>';
echo '</div>';

// Summary stats
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

echo '<div id="summary-stats" class="mt-4">';
echo '<div class="row">';

echo '<div class="col-md-3">';
echo '<div class="card text-center">';
echo '<div class="card-body">';
echo '<h5 class="card-title">Total Students</h5>';
echo '<p class="card-text display-4">' . $totalStudents . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-3">';
echo '<div class="card text-center">';
echo '<div class="card-body">';
echo '<h5 class="card-title">Assignments</h5>';
echo '<p class="card-text display-4">' . $totalActivities . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-3">';
echo '<div class="card text-center">';
echo '<div class="card-body">';
echo '<h5 class="card-title">Avg. Score</h5>';
echo '<p class="card-text display-4">' . $overallAverage . '%</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-3">';
echo '<div class="card text-center">';
echo '<div class="card-body">';
echo '<h5 class="card-title">Completion</h5>';
echo '<p class="card-text display-4">' . $completionRate . '%</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';
echo '</div>';

// Simple inline JavaScript for charts (no AMD)
if (!empty($activities) && !empty($students)) {
    $chartLabels = array();
    $chartData = array();
    
    foreach ($activities as $activity) {
        $chartLabels[] = $activity->name;
        $scores = array();
        foreach ($students as $student) {
            if (isset($student->grades[$activity->id]) && 
                $student->grades[$activity->id]->status === 'submitted' && 
                $student->grades[$activity->id]->grade !== null) {
                $scores[] = floatval($student->grades[$activity->id]->grade);
            }
        }
        $chartData[] = count($scores) > 0 ? round(array_sum($scores) / count($scores), 1) : 0;
    }
    
    echo '<div id="chart-container" class="mt-4">';
    echo '<h3>' . get_string('averagescores', 'report_codeprogress') . '</h3>';
    echo '<div class="row">';
    echo '<div class="col-md-6">';
    echo '<canvas id="average-scores-chart"></canvas>';
    echo '</div>';
    echo '<div class="col-md-6">';
    echo '<canvas id="submission-status-chart"></canvas>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    
    // Inline script without AMD
    echo '<script>
    window.addEventListener("load", function() {
        if (typeof Chart !== "undefined") {
            // Average scores chart
            var ctx1 = document.getElementById("average-scores-chart").getContext("2d");
            new Chart(ctx1, {
                type: "bar",
                data: {
                    labels: ' . json_encode($chartLabels) . ',
                    datasets: [{
                        label: "Average Score (%)",
                        data: ' . json_encode($chartData) . ',
                        backgroundColor: "rgba(54, 162, 235, 0.5)",
                        borderColor: "rgba(54, 162, 235, 1)",
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + "%";
                                }
                            }
                        }
                    }
                }
            });
            
            // Submission status chart
            var ctx2 = document.getElementById("submission-status-chart").getContext("2d");
            new Chart(ctx2, {
                type: "doughnut",
                data: {
                    labels: ["Submitted", "Not Submitted"],
                    datasets: [{
                        data: [' . $submittedCount . ', ' . ($totalPossible - $submittedCount) . '],
                        backgroundColor: [
                            "rgba(75, 192, 192, 0.6)",
                            "rgba(255, 99, 132, 0.6)"
                        ],
                        borderColor: [
                            "rgba(75, 192, 192, 1)",
                            "rgba(255, 99, 132, 1)"
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: "bottom"
                        }
                    }
                }
            });
        }
    });
    </script>';
}

echo '</div>'; // Close container

echo $OUTPUT->footer();