<?php
// Minimal Moodle bootstrap - no theme, no AMD
require_once('../../config.php');

$courseid = required_param('course', PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);

$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);
require_capability('report/codeprogress:view', $context);

// Check if we have any code sandbox activities
$hasactivities = $DB->record_exists('codesandbox', array('course' => $courseid));

// Handle CSV export
if ($format === 'csv' && $hasactivities) {
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

// Get data
require_once($CFG->dirroot . '/local/customapi/classes/external.php');
try {
    $data = local_customapi_external::get_sandbox_grades($courseid);
} catch (Exception $e) {
    $data = array();
    $error = $e->getMessage();
}

// Process data
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

// OUTPUT STARTS HERE - NO MOODLE THEME
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Coding Progress Report - <?php echo s($course->fullname); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .sticky-col {
            position: sticky;
            left: 0;
            background-color: white;
            z-index: 5;
        }
        thead .sticky-col {
            background-color: #f8f9fa;
            z-index: 10;
        }
        .text-success { color: #28a745 !important; }
        .text-warning { color: #ffc107 !important; }
        .text-danger { color: #dc3545 !important; }
        .table-responsive {
            max-height: 600px;
            overflow: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1>Coding Progress Report</h1>
                <h3 class="text-muted"><?php echo s($course->fullname); ?></h3>
            </div>
            <div>
                <a href="<?php echo $CFG->wwwroot . '/course/view.php?id=' . $courseid; ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Course
                </a>
                <a href="?course=<?php echo $courseid; ?>&format=csv" class="btn btn-primary">
                    <i class="fas fa-download"></i> Export CSV
                </a>
            </div>
        </div>

        <?php if (!$hasactivities): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No code sandbox activities found in this course.
            </div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> Error loading data: <?php echo s($error); ?>
            </div>
        <?php else: ?>
            
            <!-- Summary Statistics -->
            <?php
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
            ?>
            
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Total Students</h5>
                            <p class="card-text display-4"><?php echo $totalStudents; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Assignments</h5>
                            <p class="card-text display-4"><?php echo $totalActivities; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Avg. Score</h5>
                            <p class="card-text display-4"><?php echo $overallAverage; ?>%</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">Completion</h5>
                            <p class="card-text display-4"><?php echo $completionRate; ?>%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Progress Table -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th class="sticky-col">Student</th>
                            <?php foreach ($activities as $activity): ?>
                                <th class="text-center"><?php echo s($activity->name); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td class="sticky-col"><?php echo s($student->name); ?></td>
                                <?php foreach ($activities as $activity): ?>
                                    <td class="text-center">
                                        <?php
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
                                            echo '<span class="' . $class . ' font-weight-bold">' . $grade . '%</span>';
                                        } else {
                                            echo '<span class="text-muted">-</span>';
                                        }
                                        ?>
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td class="sticky-col">Average</td>
                            <?php foreach ($activities as $activity): ?>
                                <?php
                                $scores = array();
                                foreach ($students as $student) {
                                    if (isset($student->grades[$activity->id]) && 
                                        $student->grades[$activity->id]->status === 'submitted' && 
                                        $student->grades[$activity->id]->grade !== null) {
                                        $scores[] = floatval($student->grades[$activity->id]->grade);
                                    }
                                }
                                ?>
                                <td class="text-center">
                                    <?php if (count($scores) > 0): ?>
                                        <?php
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
                                        ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Charts -->
            <?php
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
            ?>
            
            <div class="row mt-5">
                <div class="col-md-6">
                    <h4>Average Scores by Assignment</h4>
                    <canvas id="avgChart" width="400" height="200"></canvas>
                </div>
                <div class="col-md-6">
                    <h4>Submission Status</h4>
                    <canvas id="pieChart" width="400" height="200"></canvas>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    <script>
    // Charts only if we have data
    <?php if ($hasactivities && !isset($error) && !empty($activities)): ?>
    window.onload = function() {
        // Bar chart
        var ctx1 = document.getElementById('avgChart').getContext('2d');
        new Chart(ctx1, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_values($chartLabels)); ?>,
                datasets: [{
                    label: 'Average Score (%)',
                    data: <?php echo json_encode(array_values($chartData)); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
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
                                return value + '%';
                            }
                        }
                    }
                }
            }
        });

        // Pie chart
        var ctx2 = document.getElementById('pieChart').getContext('2d');
        new Chart(ctx2, {
            type: 'doughnut',
            data: {
                labels: ['Submitted', 'Not Submitted'],
                datasets: [{
                    data: [<?php echo $submittedCount; ?>, <?php echo ($totalPossible - $submittedCount); ?>],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    };
    <?php endif; ?>
    </script>
</body>
</html>