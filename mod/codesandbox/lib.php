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
 * Library functions for mod_codesandbox
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/ddllib.php');

/**
 * Add codesandbox instance.
 *
 * @param stdClass $codesandbox
 * @return int new codesandbox instance id
 */
function codesandbox_add_instance($codesandbox) {
    global $DB;
    
    $codesandbox->timecreated = time();
    $codesandbox->timemodified = time();
    
    // Process allowed languages if the field exists
    $dbman = $DB->get_manager();
    $table = new xmldb_table('codesandbox');
    $field = new xmldb_field('allowed_languages');
    if ($dbman->field_exists($table, $field)) {
        if (!empty($codesandbox->allowed_languages) && is_array($codesandbox->allowed_languages)) {
            $codesandbox->allowed_languages = implode(',', $codesandbox->allowed_languages);
        } else if (empty($codesandbox->allowed_languages)) {
            $codesandbox->allowed_languages = 'python,ruby,elixir';
        }
    }
    
    // Process test suite file if uploaded
    if (isset($codesandbox->is_gradable) && $codesandbox->is_gradable && !empty($codesandbox->testsuitefiles)) {
        $codesandbox->test_suite_path = $codesandbox->testsuitefiles;
    }
    
    $codesandbox->id = $DB->insert_record('codesandbox', $codesandbox);
    
    // Save test suite files if any
    if (isset($codesandbox->is_gradable) && $codesandbox->is_gradable && !empty($codesandbox->testsuitefiles)) {
        $context = context_module::instance($codesandbox->coursemodule);
        file_save_draft_area_files($codesandbox->testsuitefiles, $context->id, 'mod_codesandbox', 
                                  'testsuite', 0, array('subdirs' => 0, 'maxfiles' => 1));
    }
    
    codesandbox_grade_item_update($codesandbox);
    
    return $codesandbox->id;
}

/**
 * Update codesandbox instance.
 *
 * @param stdClass $codesandbox
 * @return bool true
 */
function codesandbox_update_instance($codesandbox) {
    global $DB;
    
    $codesandbox->timemodified = time();
    $codesandbox->id = $codesandbox->instance;
    
    // Process allowed languages if the field exists
    $dbman = $DB->get_manager();
    $table = new xmldb_table('codesandbox');
    $field = new xmldb_field('allowed_languages');
    if ($dbman->field_exists($table, $field)) {
        if (!empty($codesandbox->allowed_languages) && is_array($codesandbox->allowed_languages)) {
            $codesandbox->allowed_languages = implode(',', $codesandbox->allowed_languages);
        } else if (empty($codesandbox->allowed_languages)) {
            $codesandbox->allowed_languages = 'python,ruby,elixir';
        }
    }
    
    // Process test suite file if uploaded
    if (isset($codesandbox->is_gradable) && $codesandbox->is_gradable && !empty($codesandbox->testsuitefiles)) {
        $context = context_module::instance($codesandbox->coursemodule);
        file_save_draft_area_files($codesandbox->testsuitefiles, $context->id, 'mod_codesandbox', 
                                  'testsuite', 0, array('subdirs' => 0, 'maxfiles' => 1));
    }
    
    $DB->update_record('codesandbox', $codesandbox);
    
    codesandbox_grade_item_update($codesandbox);
    
    return true;
}

/**
 * Delete codesandbox instance.
 *
 * @param int $id
 * @return bool true
 */
function codesandbox_delete_instance($id) {
    global $DB;
    
    if (!$codesandbox = $DB->get_record('codesandbox', array('id' => $id))) {
        return false;
    }
    
    // Delete all submissions
    $DB->delete_records('codesandbox_submissions', array('codesandboxid' => $id));
    
    // Delete the instance
    $DB->delete_records('codesandbox', array('id' => $id));
    
    // Delete grade item
    codesandbox_grade_item_delete($codesandbox);
    
    return true;
}

/**
 * Create/update grade item for given codesandbox
 *
 * @param stdClass $codesandbox object with extra cmidnumber
 * @param mixed $grades optional array/object of grade(s); 'reset' means reset grades in gradebook
 * @return int 0 if ok
 */
function codesandbox_grade_item_update($codesandbox, $grades = null) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    
    $params = array('itemname' => $codesandbox->name);
    
    if (isset($codesandbox->is_gradable) && $codesandbox->is_gradable) {
        $params['gradetype'] = GRADE_TYPE_VALUE;
        $params['grademax']  = $codesandbox->grade_max;
        $params['grademin']  = 0;
    } else {
        $params['gradetype'] = GRADE_TYPE_NONE;
    }
    
    if ($grades === 'reset') {
        $params['reset'] = true;
        $grades = null;
    }
    
    return grade_update('mod/codesandbox', $codesandbox->course, 'mod', 'codesandbox', 
                       $codesandbox->id, 0, $grades, $params);
}

/**
 * Delete grade item for given codesandbox
 *
 * @param stdClass $codesandbox object
 * @return int 0 if ok
 */
function codesandbox_grade_item_delete($codesandbox) {
    global $CFG;
    require_once($CFG->libdir.'/gradelib.php');
    
    return grade_update('mod/codesandbox', $codesandbox->course, 'mod', 'codesandbox',
                       $codesandbox->id, 0, null, array('deleted' => 1));
}

/**
 * Update codesandbox grades in the gradebook
 *
 * @param stdClass $codesandbox
 * @param int $userid specific user only, 0 means all participants
 */
function codesandbox_update_grades($codesandbox, $userid = 0) {
    global $CFG, $DB;
    require_once($CFG->libdir.'/gradelib.php');
    
    if (!isset($codesandbox->is_gradable) || !$codesandbox->is_gradable) {
        return codesandbox_grade_item_update($codesandbox);
    }
    
    $grades = array();
    
    if ($userid) {
        $submission = $DB->get_record('codesandbox_submissions', 
            array('codesandboxid' => $codesandbox->id, 'userid' => $userid), 
            'userid, score', IGNORE_MULTIPLE);
        
        if ($submission) {
            $grades[$userid] = new stdClass();
            $grades[$userid]->userid = $userid;
            $grades[$userid]->rawgrade = $submission->score * $codesandbox->grade_max;
        }
    } else {
        $submissions = $DB->get_records('codesandbox_submissions', 
            array('codesandboxid' => $codesandbox->id), '', 'userid, score');
        
        foreach ($submissions as $submission) {
            $grades[$submission->userid] = new stdClass();
            $grades[$submission->userid]->userid = $submission->userid;
            $grades[$submission->userid]->rawgrade = $submission->score * $codesandbox->grade_max;
        }
    }
    
    return codesandbox_grade_item_update($codesandbox, $grades);
}

/**
 * Process code submission
 *
 * @param stdClass $codesandbox
 * @param int $userid
 * @param string $code
 * @return stdClass submission record
 */
function codesandbox_process_submission($codesandbox, $userid, $code, $language = 'python') {
    global $CFG, $DB;
    
    // Save submission
    $submission = new stdClass();
    $submission->codesandboxid = $codesandbox->id;
    $submission->userid = $userid;
    $submission->code = $code;
    $submission->timesubmitted = time();
    
    // Only set language if the field exists in the database
    $dbman = $DB->get_manager();
    $table = new xmldb_table('codesandbox_submissions');
    $field = new xmldb_field('language');
    if ($dbman->field_exists($table, $field)) {
        $submission->language = $language;
    }
    
    if (isset($codesandbox->is_gradable) && $codesandbox->is_gradable) {
        // Get test suite content
        $context = context_module::instance($codesandbox->coursemodule);
        $fs = get_file_storage();
        $files = $fs->get_area_files($context->id, 'mod_codesandbox', 'testsuite', 0, 
                                     'itemid, filepath, filename', false);
        
        $testcode = '';
        foreach ($files as $file) {
            $testcode = $file->get_content();
            break;
        }
        
        if ($testcode) {
            // Call grading microservice
            $response = codesandbox_call_grading_api($code, $testcode);
            
            if ($response) {
                // Update submission with results
                $submission->score = $response->score;
                $submission->feedback = json_encode($response->results);
                
                // Update gradebook
                $grade = new stdClass();
                $grade->userid = $userid;
                $grade->rawgrade = $response->score * $codesandbox->grade_max;
                $grade->feedback = codesandbox_format_test_results($response->results);
                $grade->feedbackformat = FORMAT_HTML;
                $grade->datesubmitted = time();
                $grade->dategraded = time();
                
                codesandbox_grade_item_update($codesandbox, array($userid => $grade));
            }
        }
    }
    
    // Check for existing submission
    $existing = $DB->get_record('codesandbox_submissions', 
                               array('codesandboxid' => $codesandbox->id, 'userid' => $userid));
    
    if ($existing) {
        $submission->id = $existing->id;
        $DB->update_record('codesandbox_submissions', $submission);
    } else {
        $submission->id = $DB->insert_record('codesandbox_submissions', $submission);
    }
    
    return $submission;
}

/**
 * Call the grading API
 *
 * @param string $studentcode
 * @param string $testcode
 * @return stdClass|false
 */
function codesandbox_call_grading_api($studentcode, $testcode) {
    global $CFG;
    
    $apiurl = get_config('mod_codesandbox', 'apiurl');
    if (!$apiurl) {
        $apiurl = 'http://localhost:8000';
    }
    $apiurl .= '/grade';
    
    $postdata = json_encode(array(
        'student_code' => $studentcode,
        'test_code' => $testcode
    ));
    
    $ch = curl_init($apiurl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpcode == 200 && $response) {
        return json_decode($response);
    }
    
    return false;
}

/**
 * Format test results for display
 *
 * @param array $results
 * @return string HTML formatted results
 */
function codesandbox_format_test_results($results) {
    if (!is_array($results)) {
        $results = json_decode($results, true);
    }
    
    $html = '<div class="test-results">';
    
    foreach ($results as $result) {
        $class = $result['passed'] ? 'text-success' : 'text-danger';
        $icon = $result['passed'] ? '✓' : '✗';
        $html .= '<div class="test-result ' . $class . '">';
        $html .= $icon . ' ' . $result['test_name'];
        if (!$result['passed'] && !empty($result['message'])) {
            $html .= '<pre class="test-error">' . htmlspecialchars($result['message']) . '</pre>';
        }
        $html .= '</div>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Returns all other caps used in the module
 *
 * @return array
 */
function codesandbox_get_extra_capabilities() {
    return array('moodle/site:accessallgroups');
}

/**
 * List of view style log actions
 *
 * @return array
 */
function codesandbox_get_view_actions() {
    return array('view', 'view all');
}

/**
 * List of update style log actions
 *
 * @return array
 */
function codesandbox_get_post_actions() {
    return array('submit', 'update');
}

/**
 * Mark the activity completed (if required) and trigger the course_module_viewed event.
 *
 * @param stdClass $codesandbox codesandbox object
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 */
function codesandbox_view($codesandbox, $course, $cm, $context) {
    // Trigger course_module_viewed event.
    $params = array(
        'context' => $context,
        'objectid' => $codesandbox->id
    );
    
    $event = \mod_codesandbox\event\course_module_viewed::create($params);
    $event->add_record_snapshot('course_modules', $cm);
    $event->add_record_snapshot('course', $course);
    $event->add_record_snapshot('codesandbox', $codesandbox);
    $event->trigger();
    
    // Completion.
    $completion = new completion_info($course);
    $completion->set_module_viewed($cm);
}

/**
 * Supports feature
 *
 * @param string $feature FEATURE_xx constant for requested feature
 * @return mixed true if module supports feature, false if not, null if doesn't know
 */
function codesandbox_supports($feature) {
    switch($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        case FEATURE_SHOW_DESCRIPTION:
            return true;
        case FEATURE_GRADE_HAS_GRADE:
            return true;
        case FEATURE_BACKUP_MOODLE2:
            return true;
        case FEATURE_COMPLETION_TRACKS_VIEWS:
            return true;
        case FEATURE_COMPLETION_HAS_RULES:
            return false;
        case FEATURE_GROUPS:
            return true;
        case FEATURE_GROUPINGS:
            return true;
        default:
            return null;
    }
}