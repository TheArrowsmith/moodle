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
 * Library of interface functions and constants for module markdownfile
 *
 * @package    mod_markdownfile
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Returns the information on whether the module supports a feature
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if the feature is supported, null if unknown
 */
function markdownfile_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return false;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_GROUPS:
            return false;
        case FEATURE_GROUPINGS:
            return false;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the markdownfile into the database
 *
 * @param stdClass $markdownfile An object from the form in mod_form.php
 * @param mod_markdownfile_mod_form $mform
 * @return int The id of the newly inserted markdownfile record
 */
function markdownfile_add_instance(stdClass $markdownfile, mod_markdownfile_mod_form $mform = null) {
    global $DB, $CFG, $USER;
    require_once("$CFG->libdir/filelib.php");

    $markdownfile->timemodified = time();

    if (!isset($markdownfile->display)) {
        $markdownfile->display = 0;
    }
    if (!isset($markdownfile->displayoptions)) {
        $markdownfile->displayoptions = serialize(array());
    }

    // Process file upload if present
    if ($mform && !empty($markdownfile->markdownfilefile)) {
        $markdownfile->content = '';
        $markdownfile->contentformat = FORMAT_MARKDOWN;
    }

    $markdownfile->id = $DB->insert_record('markdownfile', $markdownfile);

    // Save any uploaded files
    if ($mform) {
        $context = context_module::instance($markdownfile->coursemodule);
        if (!empty($markdownfile->markdownfilefile)) {
            file_save_draft_area_files($markdownfile->markdownfilefile, $context->id, 'mod_markdownfile', 'content', 0,
                                      array('subdirs' => 0, 'maxfiles' => 1));
        }
    }

    return $markdownfile->id;
}

/**
 * Updates an instance of the markdownfile in the database
 *
 * @param stdClass $markdownfile An object from the form in mod_form.php
 * @param mod_markdownfile_mod_form $mform
 * @return boolean Success/Fail
 */
function markdownfile_update_instance(stdClass $markdownfile, mod_markdownfile_mod_form $mform = null) {
    global $DB, $CFG;
    require_once("$CFG->libdir/filelib.php");

    $markdownfile->timemodified = time();
    $markdownfile->id = $markdownfile->instance;

    if (!isset($markdownfile->display)) {
        $markdownfile->display = 0;
    }
    if (!isset($markdownfile->displayoptions)) {
        $markdownfile->displayoptions = serialize(array());
    }

    // Process file upload if present
    if ($mform && !empty($markdownfile->markdownfilefile)) {
        $context = context_module::instance($markdownfile->coursemodule);
        $draftitemid = $markdownfile->markdownfilefile;
        file_save_draft_area_files($draftitemid, $context->id, 'mod_markdownfile', 'content', 0, array('subdirs' => 0, 'maxfiles' => 1));
    }

    return $DB->update_record('markdownfile', $markdownfile);
}

/**
 * Removes an instance of the markdownfile from the database
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 */
function markdownfile_delete_instance($id) {
    global $DB;

    if (!$markdownfile = $DB->get_record('markdownfile', array('id' => $id))) {
        return false;
    }

    // Delete any dependent records here
    $DB->delete_records('markdownfile', array('id' => $markdownfile->id));

    return true;
}

/**
 * Returns a small object with summary information about what a
 * user has done with a given particular instance of this module
 *
 * @param stdClass $course The course record
 * @param stdClass $user The user record
 * @param cm_info|stdClass $mod The course module info object or record
 * @param stdClass $markdownfile The markdownfile instance record
 * @return stdClass|null
 */
function markdownfile_user_outline($course, $user, $mod, $markdownfile) {
    $return = new stdClass();
    $return->time = 0;
    $return->info = '';
    return $return;
}

/**
 * Prints a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @param stdClass $course the current course record
 * @param stdClass $user the record of the user we are generating report for
 * @param cm_info $mod course module info
 * @param stdClass $markdownfile the module instance record
 * @return void, is supposed to echo directly
 */
function markdownfile_user_complete($course, $user, $mod, $markdownfile) {
}

/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in markdownfile activities and print it out.
 *
 * @param stdClass $course The course record
 * @param bool $viewfullnames Should we display full names
 * @param int $timestart Print activity since this timestamp
 * @return boolean True if anything was printed, otherwise false
 */
function markdownfile_print_recent_activity($course, $viewfullnames, $timestart) {
    return false;
}

/**
 * Function to be run periodically according to the moodle cron
 *
 * @return boolean
 */
function markdownfile_cron () {
    return true;
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function markdownfile_get_extra_capabilities() {
    return array();
}

/**
 * Is a given scale used by the instance of markdownfile?
 *
 * @param int $markdownfileid ID of an instance of this module
 * @param int $scaleid ID of the scale
 * @return bool true if the scale is used by the given markdownfile instance
 */
function markdownfile_scale_used($markdownfileid, $scaleid) {
    return false;
}

/**
 * Checks if scale is being used by any instance of markdownfile.
 *
 * @param int $scaleid ID of the scale
 * @return boolean true if the scale is used by any markdownfile instance
 */
function markdownfile_scale_used_anywhere($scaleid) {
    return false;
}

/**
 * Creates or updates grade item for the given markdownfile instance
 *
 * @param stdClass $markdownfile instance object with extra cmidnumber and modname property
 * @param mixed optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return void
 */
function markdownfile_grade_item_update(stdClass $markdownfile, $grades=null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');

    $item = array();
    $item['itemname'] = clean_param($markdownfile->name, PARAM_NOTAGS);
    $item['gradetype'] = GRADE_TYPE_NONE;

    grade_update('mod/markdownfile', $markdownfile->course, 'mod', 'markdownfile',
            $markdownfile->id, 0, null, $item);
}

/**
 * Update markdownfile grades in the gradebook
 *
 * @param stdClass $markdownfile instance object with extra cmidnumber and modname property
 * @param int $userid update grade of specific user only, 0 means all participants
 * @param bool $nullifnone return null if grade does not exist
 * @return void
 */
function markdownfile_update_grades(stdClass $markdownfile, $userid = 0, $nullifnone = true) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');

    if ($markdownfile->grade == 0) {
        markdownfile_grade_item_update($markdownfile);

    } else if ($grades = markdownfile_get_user_grades($markdownfile, $userid)) {
        markdownfile_grade_item_update($markdownfile, $grades);

    } else if ($userid && $nullifnone) {
        $grade = new stdClass();
        $grade->userid = $userid;
        $grade->rawgrade = null;
        markdownfile_grade_item_update($markdownfile, $grade);

    } else {
        markdownfile_grade_item_update($markdownfile);
    }
}

/**
 * Get markdownfile grades in the gradebook
 *
 * @param stdClass $markdownfile instance object
 * @param int $userid optional user id, 0 means all users
 * @return array array of grades, false if none
 */
function markdownfile_get_user_grades($markdownfile, $userid = 0) {
    return false;
}

/**
 * Serves the files from the markdownfile file areas
 *
 * @param stdClass $course the course object
 * @param stdClass $cm the course module object
 * @param stdClass $context the context
 * @param string $filearea the name of the file area
 * @param array $args extra arguments (itemid, path)
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool false if the file not found, just send the file otherwise and do not return anything
 */
function markdownfile_pluginfile($course, $cm, $context, $filearea, array $args, $forcedownload, array $options=array()) {
    global $DB, $CFG;

    if ($context->contextlevel != CONTEXT_MODULE) {
        send_file_not_found();
    }

    require_login($course, true, $cm);

    if (!has_capability('mod/markdownfile:view', $context)) {
        return false;
    }

    if ($filearea !== 'content') {
        return false;
    }

    $itemid = array_shift($args);

    $filename = array_pop($args);
    if (!$args) {
        $filepath = '/';
    } else {
        $filepath = '/'.implode('/', $args).'/';
    }

    // Retrieve the file from the Files API.
    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'mod_markdownfile', $filearea, $itemid, $filepath, $filename);
    if (!$file) {
        return false; // The file does not exist.
    }

    // We can now send the file back to the browser - in this case with a cache lifetime of 1 day and no filtering.
    send_stored_file($file, 86400, 0, $forcedownload, $options);
}