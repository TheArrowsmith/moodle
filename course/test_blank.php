<?php
require_once('../config.php');
require_login();

// Use the most minimal page setup possible
$PAGE->set_context(context_system::instance());
$PAGE->set_url('/course/test_blank.php');
$PAGE->set_pagelayout('embedded'); // Minimal layout
$PAGE->set_title('Blank Test');

// Don't load any JavaScript except what we need
$PAGE->requires->js('/react-dist/moodle-react.iife.js', true);

echo $OUTPUT->header();
?>

<h1>Blank Page Test</h1>
<p>This page should have no errors. If you see "Web service is not available", it's from Moodle itself.</p>

<?php
echo $OUTPUT->footer();
?>