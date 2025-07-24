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
 * View page for mod_codesandbox
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once('lib.php');

$id = required_param('id', PARAM_INT); // Course module ID

if (!$cm = get_coursemodule_from_id('codesandbox', $id)) {
    print_error('invalidcoursemodule');
}

if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
    print_error('coursemissingshort');
}

if (!$codesandbox = $DB->get_record('codesandbox', array('id' => $cm->instance))) {
    print_error('invalidcodesandboxid', 'codesandbox');
}

require_login($course, true, $cm);
$context = context_module::instance($cm->id);
require_capability('mod/codesandbox:view', $context);

// Mark viewed
codesandbox_view($codesandbox, $course, $cm, $context);

$PAGE->set_url('/mod/codesandbox/view.php', array('id' => $id));
$PAGE->set_title(format_string($codesandbox->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Include CodeMirror CSS
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css'));
$PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css'));

// Include CodeMirror JS
$PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js'), true);
$PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/python/python.min.js'), true);
$PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/ruby/ruby.min.js'), true);
$PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/erlang/erlang.min.js'), true);

// Include custom CSS
$PAGE->requires->css('/mod/codesandbox/styles.css');

// Get API URL from config or use default
$apiurl = get_config('mod_codesandbox', 'apiurl');
if (!$apiurl) {
    $apiurl = 'http://localhost:8080';
}

// Include our JavaScript module
$PAGE->requires->js_call_amd('mod_codesandbox/editor', 'init', array(
    $cm->id,
    $codesandbox->starter_code,
    $apiurl,
    (bool)$codesandbox->is_gradable,
    $current_language
));

echo $OUTPUT->header();
echo $OUTPUT->heading($codesandbox->name);

if ($codesandbox->intro) {
    echo $OUTPUT->box(format_module_intro('codesandbox', $codesandbox, $cm->id), 
                     'generalbox mod_introbox', 'codesandboxintro');
}

// Get previous submission if exists
$submission = null;
if (has_capability('mod/codesandbox:submit', $context)) {
    $submission = $DB->get_record('codesandbox_submissions', 
                                 array('codesandboxid' => $codesandbox->id, 'userid' => $USER->id));
}

// Prepare languages for template
// Check if language fields exist (for backward compatibility)
$allowed_languages = array('python', 'ruby', 'elixir');
if (property_exists($codesandbox, 'allowed_languages') && !empty($codesandbox->allowed_languages)) {
    $allowed_languages = explode(',', $codesandbox->allowed_languages);
}

$language_options = array();
$current_language = 'python'; // Default

// Determine current language with backward compatibility
if ($submission && property_exists($submission, 'language') && !empty($submission->language)) {
    $current_language = $submission->language;
} else if (property_exists($codesandbox, 'language') && !empty($codesandbox->language)) {
    $current_language = $codesandbox->language;
}

foreach ($allowed_languages as $lang) {
    $language_options[] = array(
        'value' => $lang,
        'name' => get_string('language_' . $lang, 'mod_codesandbox'),
        'selected' => ($lang == $current_language)
    );
}

// Render the template
$templatecontext = array(
    'id' => $cm->id,
    'hassubmission' => !empty($submission),
    'previouscode' => $submission ? $submission->code : '',
    'isgradable' => $codesandbox->is_gradable,
    'cansubmit' => has_capability('mod/codesandbox:submit', $context),
    'sesskey' => sesskey(),
    'languages' => $language_options
);

echo $OUTPUT->render_from_template('mod_codesandbox/view', $templatecontext);

echo $OUTPUT->footer();