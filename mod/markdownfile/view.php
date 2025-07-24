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
 * Prints a particular instance of markdownfile
 *
 * @package    mod_markdownfile
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = optional_param('id', 0, PARAM_INT); // Course_module ID
$n  = optional_param('n', 0, PARAM_INT);  // markdownfile instance ID

if ($id) {
    $cm         = get_coursemodule_from_id('markdownfile', $id, 0, false, MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $markdownfile = $DB->get_record('markdownfile', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($n) {
    $markdownfile = $DB->get_record('markdownfile', array('id' => $n), '*', MUST_EXIST);
    $course     = $DB->get_record('course', array('id' => $markdownfile->course), '*', MUST_EXIST);
    $cm         = get_coursemodule_from_instance('markdownfile', $markdownfile->id, $course->id, false, MUST_EXIST);
} else {
    error('You must specify a course_module ID or an instance ID');
}

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

// Check capabilities
require_capability('mod/markdownfile:view', $context);

// Log this view
$params = array(
    'context' => $context,
    'objectid' => $markdownfile->id
);
$event = \mod_markdownfile\event\course_module_viewed::create($params);
$event->add_record_snapshot('markdownfile', $markdownfile);
$event->trigger();

// Mark viewed by user (if required)
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Page setup
$PAGE->set_url('/mod/markdownfile/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($markdownfile->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($context);

// Output starts here
echo $OUTPUT->header();

// Conditions to show the intro can change to look for own settings or whatever
if ($markdownfile->intro) {
    echo $OUTPUT->box(format_module_intro('markdownfile', $markdownfile, $cm->id), 'generalbox mod_introbox', 'markdownfileintro');
}

// Get the markdown content
$content = '';

// Check if content is stored in database
if (!empty($markdownfile->content)) {
    $content = $markdownfile->content;
} else {
    // Try to load from file
    $fs = get_file_storage();
    $files = $fs->get_area_files($context->id, 'mod_markdownfile', 'content', 0, 'sortorder DESC, id ASC', false);
    
    if (count($files) > 0) {
        $file = reset($files);
        $content = $file->get_content();
    }
}

// Display the content
if (!empty($content)) {
    // Format options
    $formatoptions = new stdClass;
    $formatoptions->noclean = true;
    $formatoptions->overflowdiv = true;
    $formatoptions->context = $context;
    
    // Convert markdown to HTML using Moodle's built-in markdown parser
    $html = format_text($content, FORMAT_MARKDOWN, $formatoptions);
    
    // Display based on display settings
    if ($markdownfile->display == 0 || $markdownfile->display == 1) {
        // Auto or embed - display the content
        echo $OUTPUT->box_start('generalbox center clearfix');
        
        // Check if modern frontend is available and enabled
        if (file_exists(__DIR__ . '/../../lib/react_helper.php')) {
            require_once(__DIR__ . '/../../lib/react_helper.php');
            
            // Create unique element ID for this instance
            $elementId = 'markdown-content-' . $markdownfile->id;
            
            // Render with React enhancement
            render_react_component('MarkdownRenderer', $elementId, [
                'htmlContent' => $html,
                'theme' => 'github' // Could be made configurable
            ], [
                'class' => 'enhanced-markdown-content'
            ]);
        } else {
            // Fallback to standard HTML output
            echo $html;
        }
        
        echo $OUTPUT->box_end();
    } else if ($markdownfile->display == 2) {
        // Download only - provide download link
        $files = $fs->get_area_files($context->id, 'mod_markdownfile', 'content', 0, 'sortorder DESC, id ASC', false);
        if (count($files) > 0) {
            $file = reset($files);
            $fileurl = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), 
                                                       $file->get_filearea(), $file->get_itemid(), 
                                                       $file->get_filepath(), $file->get_filename(), true);
            echo $OUTPUT->box_start('generalbox center clearfix');
            echo html_writer::tag('p', get_string('clicktodownload', 'markdownfile'));
            echo html_writer::link($fileurl, $file->get_filename(), array('class' => 'btn btn-primary'));
            echo $OUTPUT->box_end();
        }
    }
} else {
    echo $OUTPUT->notification(get_string('nocontent', 'markdownfile'), 'notifyproblem');
}

// Finish the page
echo $OUTPUT->footer();