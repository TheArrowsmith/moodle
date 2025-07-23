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
 * Display information about all the mod_markdownfile modules in the requested course
 *
 * @package    mod_markdownfile
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(__FILE__).'/lib.php');

$id = required_param('id', PARAM_INT); // Course

$course = $DB->get_record('course', array('id' => $id), '*', MUST_EXIST);

require_course_login($course);

$coursecontext = context_course::instance($course->id);

// Log this request
$params = array(
    'context' => $coursecontext
);
$event = \mod_markdownfile\event\course_module_instance_list_viewed::create($params);
$event->add_record_snapshot('course', $course);
$event->trigger();

// Page setup
$PAGE->set_url('/mod/markdownfile/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($coursecontext);

echo $OUTPUT->header();

$modulenameplural = get_string('modulenameplural', 'markdownfile');
echo $OUTPUT->heading($modulenameplural);

// Get all the appropriate data
if (!$markdownfiles = get_all_instances_in_course('markdownfile', $course)) {
    notice(get_string('nomarkdownfiles', 'markdownfile'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

// Print the table
$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($course->format == 'weeks') {
    $table->head  = array(get_string('week'), get_string('name'));
    $table->align = array('center', 'left');
} else if ($course->format == 'topics') {
    $table->head  = array(get_string('topic'), get_string('name'));
    $table->align = array('center', 'left');
} else {
    $table->head  = array(get_string('name'));
    $table->align = array('left');
}

foreach ($markdownfiles as $markdownfile) {
    $attributes = array();
    if (!$markdownfile->visible) {
        $attributes['class'] = 'dimmed';
    }
    $link = html_writer::link(
        new moodle_url('/mod/markdownfile/view.php', array('id' => $markdownfile->coursemodule)),
        format_string($markdownfile->name, true),
        $attributes);

    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($markdownfile->section, $link);
    } else {
        $table->data[] = array($link);
    }
}

echo html_writer::table($table);
echo $OUTPUT->footer();