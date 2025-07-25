<?php
// Simple page to list available course reports

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');

$id = required_param('id', PARAM_INT); // course id

$course = $DB->get_record('course', array('id'=>$id), '*', MUST_EXIST);

require_login($course);
$context = context_course::instance($course->id);

$PAGE->set_url('/course/reports.php', array('id'=>$id));
$PAGE->set_pagelayout('incourse');
$PAGE->set_title($course->fullname);
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading('Course Reports');

// List available reports
echo '<div class="list-group">';

// Coding Progress Report
if (has_capability('report/codeprogress:view', $context)) {
    $url = new moodle_url('/report/codeprogress/index_noamd.php', array('course' => $course->id));
    echo '<a href="'.$url.'" class="list-group-item list-group-item-action">';
    echo '<h5 class="mb-1">Coding Progress Report</h5>';
    echo '<p class="mb-1">View student progress on coding assignments with visual charts and grade summaries.</p>';
    echo '<small class="text-muted">Using non-JavaScript version for compatibility</small>';
    echo '</a>';
}

// Activity completion
if (has_capability('report/progress:view', $context)) {
    $url = new moodle_url('/report/progress/index.php', array('course' => $course->id));
    echo '<a href="'.$url.'" class="list-group-item list-group-item-action">';
    echo '<h5 class="mb-1">Activity completion</h5>';
    echo '<p class="mb-1">View activity completion progress for students enrolled in this course.</p>';
    echo '</a>';
}

// Course participation
if (has_capability('report/participation:view', $context)) {
    $url = new moodle_url('/report/participation/index.php', array('id' => $course->id));
    echo '<a href="'.$url.'" class="list-group-item list-group-item-action">';
    echo '<h5 class="mb-1">Course participation</h5>';
    echo '<p class="mb-1">View participation report for activities in this course.</p>';
    echo '</a>';
}

// Activity report (outline)
if (has_capability('report/outline:view', $context)) {
    $url = new moodle_url('/report/outline/index.php', array('id' => $course->id));
    echo '<a href="'.$url.'" class="list-group-item list-group-item-action">';
    echo '<h5 class="mb-1">Activity report</h5>';
    echo '<p class="mb-1">View activity report showing what students have been doing.</p>';
    echo '</a>';
}

// Logs
if (has_capability('report/log:view', $context)) {
    $url = new moodle_url('/report/log/index.php', array('id' => $course->id));
    echo '<a href="'.$url.'" class="list-group-item list-group-item-action">';
    echo '<h5 class="mb-1">Logs</h5>';
    echo '<p class="mb-1">View logs of user activity in this course.</p>';
    echo '</a>';
}

// Live logs
if (has_capability('report/loglive:view', $context)) {
    $url = new moodle_url('/report/loglive/index.php', array('id' => $course->id));
    echo '<a href="'.$url.'" class="list-group-item list-group-item-action">';
    echo '<h5 class="mb-1">Live logs</h5>';
    echo '<p class="mb-1">View live stream of user activity in this course.</p>';
    echo '</a>';
}

echo '</div>';

echo $OUTPUT->footer();