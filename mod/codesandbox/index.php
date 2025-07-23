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
 * Index page listing all codesandbox instances in a course
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');
require_once($CFG->dirroot.'/mod/codesandbox/lib.php');

$id = required_param('id', PARAM_INT); // Course ID

if (!$course = $DB->get_record('course', array('id' => $id))) {
    print_error('invalidcourseid');
}

require_course_login($course);

$PAGE->set_url('/mod/codesandbox/index.php', array('id' => $id));
$PAGE->set_title(format_string($course->fullname));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context(context_course::instance($course->id));

echo $OUTPUT->header();

if (!$codesandboxes = get_all_instances_in_course('codesandbox', $course)) {
    notice(get_string('nocodesandboxes', 'codesandbox'), new moodle_url('/course/view.php', array('id' => $course->id)));
}

$table = new html_table();
$table->attributes['class'] = 'generaltable mod_index';

if ($course->format == 'weeks') {
    $table->head = array(get_string('week'), get_string('name'));
    $table->align = array('center', 'left');
} else if ($course->format == 'topics') {
    $table->head = array(get_string('topic'), get_string('name'));
    $table->align = array('center', 'left');
} else {
    $table->head = array(get_string('name'));
    $table->align = array('left');
}

foreach ($codesandboxes as $codesandbox) {
    if (!$codesandbox->visible) {
        $link = html_writer::link(
            new moodle_url('/mod/codesandbox/view.php', array('id' => $codesandbox->coursemodule)),
            format_string($codesandbox->name, true),
            array('class' => 'dimmed'));
    } else {
        $link = html_writer::link(
            new moodle_url('/mod/codesandbox/view.php', array('id' => $codesandbox->coursemodule)),
            format_string($codesandbox->name, true));
    }
    
    if ($course->format == 'weeks' or $course->format == 'topics') {
        $table->data[] = array($codesandbox->section, $link);
    } else {
        $table->data[] = array($link);
    }
}

echo $OUTPUT->heading(get_string('modulenameplural', 'codesandbox'), 2);
echo html_writer::table($table);
echo $OUTPUT->footer();