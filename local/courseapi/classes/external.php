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
 * External API class for local_courseapi
 *
 * @package    local_courseapi
 * @copyright  2025 Course Management API
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_courseapi;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');

use external_api;
use external_function_parameters;
use external_value;
use external_single_structure;
use external_multiple_structure;
use context_course;
use context_module;
use stdClass;
use moodle_exception;

/**
 * External API class
 */
class external extends external_api {
    
    /**
     * Returns description of get_course_management_data parameters
     *
     * @return external_function_parameters
     */
    public static function get_course_management_data_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID')
        ]);
    }
    
    /**
     * Get course management data including all sections and activities
     *
     * @param int $courseid Course ID
     * @return array
     */
    public static function get_course_management_data($courseid) {
        global $DB;
        
        // Parameter validation
        $params = self::validate_parameters(self::get_course_management_data_parameters(), [
            'courseid' => $courseid
        ]);
        
        // Context and capability checks
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('moodle/course:update', $context);
        
        // Get course
        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
        
        // Get all sections
        $sections = $DB->get_records('course_sections', ['course' => $params['courseid']], 'section ASC');
        
        $sectionsdata = [];
        foreach ($sections as $section) {
            $sectiondata = [
                'id' => (int)$section->id,
                'name' => $section->name ?? get_string('section') . ' ' . $section->section,
                'visible' => (bool)$section->visible,
                'summary' => format_text($section->summary, $section->summaryformat, ['context' => $context]),
                'activities' => []
            ];
            
            // Get activities in this section
            if (!empty($section->sequence)) {
                $modsequence = explode(',', $section->sequence);
                foreach ($modsequence as $cmid) {
                    $cm = get_coursemodule_from_id('', $cmid, $params['courseid']);
                    if ($cm) {
                        $modinfo = get_fast_modinfo($course);
                        $mod = $modinfo->get_cm($cmid);
                        
                        $sectiondata['activities'][] = [
                            'id' => (int)$cm->id,
                            'name' => $cm->name,
                            'modname' => $cm->modname,
                            'modicon' => $mod->get_icon_url()->out(false),
                            'visible' => (bool)$cm->visible
                        ];
                    }
                }
            }
            
            $sectionsdata[] = $sectiondata;
        }
        
        return [
            'course_name' => $course->fullname,
            'sections' => $sectionsdata
        ];
    }
    
    /**
     * Returns description of get_course_management_data return value
     *
     * @return external_single_structure
     */
    public static function get_course_management_data_returns() {
        return new external_single_structure([
            'course_name' => new external_value(PARAM_TEXT, 'Course full name'),
            'sections' => new external_multiple_structure(
                new external_single_structure([
                    'id' => new external_value(PARAM_INT, 'Section ID'),
                    'name' => new external_value(PARAM_TEXT, 'Section name'),
                    'visible' => new external_value(PARAM_BOOL, 'Section visibility'),
                    'summary' => new external_value(PARAM_RAW, 'Section summary'),
                    'activities' => new external_multiple_structure(
                        new external_single_structure([
                            'id' => new external_value(PARAM_INT, 'Activity ID'),
                            'name' => new external_value(PARAM_TEXT, 'Activity name'),
                            'modname' => new external_value(PARAM_TEXT, 'Module name'),
                            'modicon' => new external_value(PARAM_URL, 'Module icon URL'),
                            'visible' => new external_value(PARAM_BOOL, 'Activity visibility')
                        ])
                    )
                ])
            )
        ]);
    }
    
    /**
     * Returns description of update_activity parameters
     *
     * @return external_function_parameters
     */
    public static function update_activity_parameters() {
        return new external_function_parameters([
            'activityid' => new external_value(PARAM_INT, 'Activity course module ID'),
            'name' => new external_value(PARAM_TEXT, 'Activity name', VALUE_OPTIONAL),
            'visible' => new external_value(PARAM_BOOL, 'Activity visibility', VALUE_OPTIONAL)
        ]);
    }
    
    /**
     * Update activity properties
     *
     * @param int $activityid Activity course module ID
     * @param string $name Optional new name
     * @param bool $visible Optional visibility
     * @return array
     */
    public static function update_activity($activityid, $name = null, $visible = null) {
        global $DB;
        
        // Parameter validation
        $params = self::validate_parameters(self::update_activity_parameters(), [
            'activityid' => $activityid,
            'name' => $name,
            'visible' => $visible
        ]);
        
        // Get course module
        $cm = get_coursemodule_from_id('', $params['activityid'], 0, false, MUST_EXIST);
        
        // Context and capability checks
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);
        
        // Update visibility if provided
        if ($params['visible'] !== null) {
            set_coursemodule_visible($cm->id, $params['visible']);
        }
        
        // Update name if provided
        if ($params['name'] !== null) {
            $module = $DB->get_record($cm->modname, ['id' => $cm->instance], '*', MUST_EXIST);
            $module->name = $params['name'];
            $DB->update_record($cm->modname, $module);
            
            // Update course module name
            $cm->name = $params['name'];
            $DB->update_record('course_modules', $cm);
        }
        
        // Get updated module info
        $course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
        $modinfo = get_fast_modinfo($course);
        $mod = $modinfo->get_cm($cm->id);
        
        return [
            'id' => (int)$cm->id,
            'name' => $mod->name,
            'modname' => $cm->modname,
            'modicon' => $mod->get_icon_url()->out(false),
            'visible' => (bool)$mod->visible
        ];
    }
    
    /**
     * Returns description of update_activity return value
     *
     * @return external_single_structure
     */
    public static function update_activity_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Activity ID'),
            'name' => new external_value(PARAM_TEXT, 'Activity name'),
            'modname' => new external_value(PARAM_TEXT, 'Module name'),
            'modicon' => new external_value(PARAM_URL, 'Module icon URL'),
            'visible' => new external_value(PARAM_BOOL, 'Activity visibility')
        ]);
    }
    
    /**
     * Returns description of update_section parameters
     *
     * @return external_function_parameters
     */
    public static function update_section_parameters() {
        return new external_function_parameters([
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'name' => new external_value(PARAM_TEXT, 'Section name', VALUE_OPTIONAL),
            'visible' => new external_value(PARAM_BOOL, 'Section visibility', VALUE_OPTIONAL),
            'summary' => new external_value(PARAM_RAW, 'Section summary', VALUE_OPTIONAL)
        ]);
    }
    
    /**
     * Update section properties
     *
     * @param int $sectionid Section ID
     * @param string $name Optional new name
     * @param bool $visible Optional visibility
     * @param string $summary Optional summary
     * @return array
     */
    public static function update_section($sectionid, $name = null, $visible = null, $summary = null) {
        global $DB;
        
        // Parameter validation
        $params = self::validate_parameters(self::update_section_parameters(), [
            'sectionid' => $sectionid,
            'name' => $name,
            'visible' => $visible,
            'summary' => $summary
        ]);
        
        // Get section
        $section = $DB->get_record('course_sections', ['id' => $params['sectionid']], '*', MUST_EXIST);
        
        // Context and capability checks
        $context = context_course::instance($section->course);
        self::validate_context($context);
        require_capability('moodle/course:update', $context);
        
        // Update section
        $update = new stdClass();
        $update->id = $section->id;
        
        if ($params['name'] !== null) {
            $update->name = $params['name'];
        }
        
        if ($params['visible'] !== null) {
            $update->visible = $params['visible'] ? 1 : 0;
        }
        
        if ($params['summary'] !== null) {
            $update->summary = $params['summary'];
            $update->summaryformat = FORMAT_HTML;
        }
        
        $DB->update_record('course_sections', $update);
        
        // Get updated section
        $section = $DB->get_record('course_sections', ['id' => $params['sectionid']], '*', MUST_EXIST);
        
        return [
            'id' => (int)$section->id,
            'name' => $section->name ?? get_string('section') . ' ' . $section->section,
            'visible' => (bool)$section->visible,
            'summary' => format_text($section->summary, $section->summaryformat, ['context' => $context])
        ];
    }
    
    /**
     * Returns description of update_section return value
     *
     * @return external_single_structure
     */
    public static function update_section_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Section ID'),
            'name' => new external_value(PARAM_TEXT, 'Section name'),
            'visible' => new external_value(PARAM_BOOL, 'Section visibility'),
            'summary' => new external_value(PARAM_RAW, 'Section summary')
        ]);
    }
    
    /**
     * Returns description of reorder_section_activities parameters
     *
     * @return external_function_parameters
     */
    public static function reorder_section_activities_parameters() {
        return new external_function_parameters([
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'activity_ids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'Activity course module ID')
            )
        ]);
    }
    
    /**
     * Reorder activities within a section
     *
     * @param int $sectionid Section ID
     * @param array $activity_ids Array of activity IDs in new order
     * @return array
     */
    public static function reorder_section_activities($sectionid, $activity_ids) {
        global $DB;
        
        // Parameter validation
        $params = self::validate_parameters(self::reorder_section_activities_parameters(), [
            'sectionid' => $sectionid,
            'activity_ids' => $activity_ids
        ]);
        
        // Get section
        $section = $DB->get_record('course_sections', ['id' => $params['sectionid']], '*', MUST_EXIST);
        
        // Context and capability checks
        $context = context_course::instance($section->course);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);
        
        // Validate all activities belong to this section
        $current_sequence = explode(',', $section->sequence);
        foreach ($params['activity_ids'] as $cmid) {
            if (!in_array($cmid, $current_sequence)) {
                throw new moodle_exception('invalidactivity', 'local_courseapi');
            }
        }
        
        // Update sequence
        $section->sequence = implode(',', $params['activity_ids']);
        $DB->update_record('course_sections', $section);
        
        // Clear cache
        rebuild_course_cache($section->course, true);
        
        return [
            'status' => 'success',
            'message' => 'Activities in section ' . $params['sectionid'] . ' reordered.'
        ];
    }
    
    /**
     * Returns description of reorder_section_activities return value
     *
     * @return external_single_structure
     */
    public static function reorder_section_activities_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status'),
            'message' => new external_value(PARAM_TEXT, 'Message')
        ]);
    }
    
    /**
     * Returns description of delete_activity parameters
     *
     * @return external_function_parameters
     */
    public static function delete_activity_parameters() {
        return new external_function_parameters([
            'activityid' => new external_value(PARAM_INT, 'Activity course module ID')
        ]);
    }
    
    /**
     * Delete an activity
     *
     * @param int $activityid Activity course module ID
     * @return array
     */
    public static function delete_activity($activityid) {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        
        // Parameter validation
        $params = self::validate_parameters(self::delete_activity_parameters(), [
            'activityid' => $activityid
        ]);
        
        // Get course module
        $cm = get_coursemodule_from_id('', $params['activityid'], 0, false, MUST_EXIST);
        
        // Context and capability checks
        $context = context_module::instance($cm->id);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);
        
        // Delete the module
        course_delete_module($cm->id);
        
        return [];
    }
    
    /**
     * Returns description of delete_activity return value
     *
     * @return null
     */
    public static function delete_activity_returns() {
        return null;
    }
    
    /**
     * Returns description of move_activity_to_section parameters
     *
     * @return external_function_parameters
     */
    public static function move_activity_to_section_parameters() {
        return new external_function_parameters([
            'sectionid' => new external_value(PARAM_INT, 'Target section ID'),
            'activityid' => new external_value(PARAM_INT, 'Activity course module ID'),
            'position' => new external_value(PARAM_INT, 'Position in section (0-based)')
        ]);
    }
    
    /**
     * Move an activity to a different section
     *
     * @param int $sectionid Target section ID
     * @param int $activityid Activity course module ID
     * @param int $position Position in section
     * @return array
     */
    public static function move_activity_to_section($sectionid, $activityid, $position) {
        global $DB;
        
        // Parameter validation
        $params = self::validate_parameters(self::move_activity_to_section_parameters(), [
            'sectionid' => $sectionid,
            'activityid' => $activityid,
            'position' => $position
        ]);
        
        // Get course module and target section
        $cm = get_coursemodule_from_id('', $params['activityid'], 0, false, MUST_EXIST);
        $target_section = $DB->get_record('course_sections', ['id' => $params['sectionid']], '*', MUST_EXIST);
        
        // Check they're in the same course
        if ($cm->course != $target_section->course) {
            throw new moodle_exception('invalidcourse', 'local_courseapi');
        }
        
        // Context and capability checks
        $context = context_course::instance($cm->course);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);
        
        // Get current section
        $current_section = $DB->get_record('course_sections', ['id' => $cm->section], '*', MUST_EXIST);
        
        // Remove from current section
        $current_sequence = explode(',', $current_section->sequence);
        $current_sequence = array_diff($current_sequence, [$params['activityid']]);
        $current_section->sequence = implode(',', $current_sequence);
        $DB->update_record('course_sections', $current_section);
        
        // Add to target section
        $target_sequence = explode(',', $target_section->sequence);
        array_splice($target_sequence, $params['position'], 0, $params['activityid']);
        $target_section->sequence = implode(',', array_filter($target_sequence));
        $DB->update_record('course_sections', $target_section);
        
        // Update course module section
        $cm->section = $target_section->id;
        $DB->update_record('course_modules', $cm);
        
        // Clear cache
        rebuild_course_cache($cm->course, true);
        
        return [
            'status' => 'success',
            'message' => 'Activity moved successfully'
        ];
    }
    
    /**
     * Returns description of move_activity_to_section return value
     *
     * @return external_single_structure
     */
    public static function move_activity_to_section_returns() {
        return new external_single_structure([
            'status' => new external_value(PARAM_TEXT, 'Status'),
            'message' => new external_value(PARAM_TEXT, 'Message')
        ]);
    }
    
    /**
     * Returns description of create_activity parameters
     *
     * @return external_function_parameters
     */
    public static function create_activity_parameters() {
        return new external_function_parameters([
            'courseid' => new external_value(PARAM_INT, 'Course ID'),
            'sectionid' => new external_value(PARAM_INT, 'Section ID'),
            'modname' => new external_value(PARAM_TEXT, 'Module type'),
            'name' => new external_value(PARAM_TEXT, 'Activity name'),
            'intro' => new external_value(PARAM_RAW, 'Activity description'),
            'visible' => new external_value(PARAM_BOOL, 'Activity visibility', VALUE_DEFAULT, true)
        ]);
    }
    
    /**
     * Create a new activity
     *
     * @param int $courseid Course ID
     * @param int $sectionid Section ID
     * @param string $modname Module type
     * @param string $name Activity name
     * @param string $intro Activity description
     * @param bool $visible Activity visibility
     * @return array
     */
    public static function create_activity($courseid, $sectionid, $modname, $name, $intro, $visible = true) {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/course/modlib.php');
        
        // Parameter validation
        $params = self::validate_parameters(self::create_activity_parameters(), [
            'courseid' => $courseid,
            'sectionid' => $sectionid,
            'modname' => $modname,
            'name' => $name,
            'intro' => $intro,
            'visible' => $visible
        ]);
        
        // Context and capability checks
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);
        
        // Get course and section
        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
        $section = $DB->get_record('course_sections', ['id' => $params['sectionid'], 'course' => $params['courseid']], '*', MUST_EXIST);
        
        // Get module info to check it exists
        $module = $DB->get_record('modules', ['name' => $params['modname'], 'visible' => 1], '*', MUST_EXIST);
        
        // Prepare module data
        $moduleinfo = new stdClass();
        $moduleinfo->modulename = $params['modname'];
        $moduleinfo->module = $module->id;
        $moduleinfo->course = $params['courseid'];
        $moduleinfo->section = $section->section;  // This is the section number (0-based), not the ID
        $moduleinfo->name = $params['name'];
        $moduleinfo->intro = $params['intro'];
        $moduleinfo->introformat = FORMAT_HTML;
        $moduleinfo->visible = $params['visible'] ? 1 : 0;
        $moduleinfo->visibleoncoursepage = 1;
        $moduleinfo->cmidnumber = '';
        $moduleinfo->groupmode = 0;
        $moduleinfo->groupingid = 0;
        $moduleinfo->availability = null;
        $moduleinfo->completion = 0;
        $moduleinfo->completionview = 0;
        $moduleinfo->completionexpected = 0;
        $moduleinfo->completiongradeitemnumber = null;
        $moduleinfo->showdescription = 0;
        
        // Add module-specific default values
        switch ($params['modname']) {
            case 'assign':
                // Date fields
                $moduleinfo->duedate = 0;
                $moduleinfo->allowsubmissionsfromdate = 0;
                $moduleinfo->cutoffdate = 0;
                $moduleinfo->gradingduedate = 0;
                
                // Grade settings
                $moduleinfo->grade = 100;
                
                // Submission settings
                $moduleinfo->submissiondrafts = 0;
                $moduleinfo->requiresubmissionstatement = 0;
                $moduleinfo->attemptreopenmethod = 'none';
                $moduleinfo->maxattempts = -1;
                
                // Notification settings
                $moduleinfo->sendnotifications = 0;
                $moduleinfo->sendlatenotifications = 0;
                $moduleinfo->sendstudentnotifications = 1;
                
                // Team submission settings
                $moduleinfo->teamsubmission = 0;
                $moduleinfo->requireallteammemberssubmit = 0;
                $moduleinfo->teamsubmissiongroupingid = 0;
                $moduleinfo->preventsubmissionnotingroup = 0;
                
                // Blind marking settings
                $moduleinfo->blindmarking = 0;
                $moduleinfo->revealidentities = 0;
                
                // Marking workflow settings
                $moduleinfo->markingworkflow = 0;
                $moduleinfo->markingallocation = 0;
                
                // Other settings
                $moduleinfo->alwaysshowdescription = 0;
                $moduleinfo->nosubmissions = 0;
                $moduleinfo->completionsubmit = 0;
                
                // Submission plugins
                $moduleinfo->assignsubmission_onlinetext_enabled = 1;
                $moduleinfo->assignsubmission_file_enabled = 0;
                $moduleinfo->assignfeedback_comments_enabled = 1;
                break;
            case 'quiz':
                $moduleinfo->grade = 100;
                $moduleinfo->grademethod = 1;
                $moduleinfo->attempts = 0;
                $moduleinfo->timeopen = 0;
                $moduleinfo->timeclose = 0;
                $moduleinfo->timelimit = 0;
                $moduleinfo->preferredbehaviour = 'deferredfeedback';
                $moduleinfo->questionsperpage = 0;
                $moduleinfo->shufflequestions = 0;
                $moduleinfo->shuffleanswers = 1;
                $moduleinfo->sumgrades = 0;
                $moduleinfo->gradecat = 0;
                $moduleinfo->decimalpoints = 2;
                $moduleinfo->questiondecimalpoints = -1;
                $moduleinfo->reviewattempt = 0x11110;
                $moduleinfo->reviewcorrectness = 0x10000;
                $moduleinfo->reviewmarks = 0x11110;
                $moduleinfo->reviewspecificfeedback = 0x10000;
                $moduleinfo->reviewgeneralfeedback = 0x01000;
                $moduleinfo->reviewrightanswer = 0x00100;
                $moduleinfo->reviewoverallfeedback = 0x01000;
                $moduleinfo->quizpassword = '';  // Required field
                $moduleinfo->subnet = '';
                $moduleinfo->browsersecurity = '-';
                $moduleinfo->delay1 = 0;
                $moduleinfo->delay2 = 0;
                $moduleinfo->showuserpicture = 0;
                $moduleinfo->showblocks = 0;
                $moduleinfo->completionpass = 0;
                break;
            case 'forum':
                $moduleinfo->type = 'general';
                $moduleinfo->forcesubscribe = 0;
                $moduleinfo->assessed = 0;
                $moduleinfo->scale = 0;
                $moduleinfo->assesstimestart = 0;
                $moduleinfo->assesstimefinish = 0;
                $moduleinfo->maxbytes = 0;
                $moduleinfo->maxattachments = 9;
                $moduleinfo->displaywordcount = 0;
                $moduleinfo->rsstype = 0;
                $moduleinfo->rssarticles = 0;
                $moduleinfo->trackingtype = 1;
                $moduleinfo->lockdiscussionafter = 0;
                $moduleinfo->blockperiod = 0;
                $moduleinfo->blockafter = 0;
                $moduleinfo->warnafter = 0;
                break;
            case 'resource':
                $moduleinfo->display = 0;
                $moduleinfo->showsize = 0;
                $moduleinfo->showtype = 0;
                $moduleinfo->showdate = 0;
                $moduleinfo->printintro = 1;
                $moduleinfo->files = 0;  // Required field
                break;
            case 'page':
                $moduleinfo->display = 0;
                $moduleinfo->printintro = 0;
                $moduleinfo->content = '';
                $moduleinfo->contentformat = FORMAT_HTML;
                break;
            case 'url':
                $moduleinfo->display = 0;
                $moduleinfo->externalurl = 'https://example.com';
                $moduleinfo->printintro = 1;
                break;
            case 'label':
                // Labels don't need many extra fields
                break;
        }
        
        // Create the module
        try {
            $moduleinfo = add_moduleinfo($moduleinfo, $course);
        } catch (\Exception $e) {
            // Log the actual error for debugging
            error_log('Activity creation failed: ' . $e->getMessage());
            error_log('Module data: ' . print_r($moduleinfo, true));
            
            // For API testing, return more specific error
            if (strpos($e->getMessage(), 'Invalid section number') !== false) {
                throw new moodle_exception('invalidsection', 'error');
            }
            throw new moodle_exception('errorwritingtodatabase', 'error', '', $e->getMessage());
        }
        
        // Get module info
        $modinfo = get_fast_modinfo($course);
        $mod = $modinfo->get_cm($moduleinfo->coursemodule);
        
        return [
            'id' => (int)$moduleinfo->coursemodule,
            'name' => $mod->name,
            'modname' => $mod->modname,
            'modicon' => $mod->get_icon_url()->out(false),
            'visible' => (bool)$mod->visible
        ];
    }
    
    /**
     * Returns description of create_activity return value
     *
     * @return external_single_structure
     */
    public static function create_activity_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Activity ID'),
            'name' => new external_value(PARAM_TEXT, 'Activity name'),
            'modname' => new external_value(PARAM_TEXT, 'Module name'),
            'modicon' => new external_value(PARAM_URL, 'Module icon URL'),
            'visible' => new external_value(PARAM_BOOL, 'Activity visibility')
        ]);
    }
    
    /**
     * Returns description of get_user_info parameters
     *
     * @return external_function_parameters
     */
    public static function get_user_info_parameters() {
        return new external_function_parameters([]);
    }
    
    /**
     * Get information about the authenticated user
     *
     * @return array
     */
    public static function get_user_info() {
        global $USER;
        
        // Must be logged in
        if (!isloggedin() || isguestuser()) {
            throw new moodle_exception('notloggedin');
        }
        
        return [
            'id' => (int)$USER->id,
            'username' => $USER->username,
            'firstname' => $USER->firstname,
            'lastname' => $USER->lastname
        ];
    }
    
    /**
     * Returns description of get_user_info return value
     *
     * @return external_single_structure
     */
    public static function get_user_info_returns() {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'User ID'),
            'username' => new external_value(PARAM_TEXT, 'Username'),
            'firstname' => new external_value(PARAM_TEXT, 'First name'),
            'lastname' => new external_value(PARAM_TEXT, 'Last name')
        ]);
    }
}