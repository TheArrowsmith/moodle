<?php
// Inline rendering to bypass AMD issues
?>

<div id="progress-dashboard-container">
    <!-- Summary Statistics -->
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
    <div id="progress-table-container">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="progress-table">
                <thead class="thead-light">
                    <tr>
                        <th class="sticky-col"><?php echo get_string('student', 'report_codeprogress'); ?></th>
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
    </div>

    <!-- Charts -->
    <div id="chart-container" class="mt-4">
        <h3><?php echo get_string('charttitle', 'report_codeprogress'); ?></h3>
        <div class="row">
            <div class="col-md-6">
                <h4>Average Scores by Assignment</h4>
                <canvas id="average-scores-chart"></canvas>
            </div>
            <div class="col-md-6">
                <h4>Submission Status</h4>
                <canvas id="submission-status-chart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Inline JavaScript to bypass AMD -->
<script>
(function() {
    // Wait for page load
    window.addEventListener('load', function() {
        // Only proceed if Chart.js is available
        if (typeof Chart === 'undefined') {
            console.error('Chart.js not loaded');
            return;
        }

        // Prepare chart data
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

        // Bar chart
        var ctx1 = document.getElementById('average-scores-chart');
        if (ctx1) {
            new Chart(ctx1.getContext('2d'), {
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
        }

        // Pie chart
        var ctx2 = document.getElementById('submission-status-chart');
        if (ctx2) {
            new Chart(ctx2.getContext('2d'), {
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
        }
    });
})();
</script>

<style>
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
.table-responsive {
    max-height: 600px;
    overflow: auto;
}
</style>