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
 * External API class for local_customapi
 *
 * @package    local_customapi
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');

/**
 * External API class
 */
class local_customapi_external extends external_api {
    
    /**
     * Returns description of get_sandbox_grades parameters
     *
     * @return external_function_parameters
     */
    public static function get_sandbox_grades_parameters() {
        return new external_function_parameters(array(
            'courseid' => new external_value(PARAM_INT, 'Course ID')
        ));
    }
    
    /**
     * Get grades for code sandbox activities
     *
     * @param int $courseid Course ID
     * @return array
     */
    public static function get_sandbox_grades($courseid) {
        global $DB, $USER;
        
        // Parameter validation
        $params = self::validate_parameters(self::get_sandbox_grades_parameters(), array(
            'courseid' => $courseid
        ));
        
        // Context and capability checks
        $context = context_course::instance($params['courseid']);
        self::validate_context($context);
        require_capability('moodle/grade:viewall', $context);
        
        // Build SQL query
        $sql = "SELECT 
                    u.id as userid,
                    u.username,
                    u.firstname,
                    u.lastname,
                    cs.id as activityid,
                    cs.name as activityname,
                    cm.id as cmid,
                    COALESCE(sub.score * cs.grade_max, -1) as grade,
                    CASE 
                        WHEN sub.id IS NOT NULL THEN 'submitted'
                        ELSE 'notsubmitted'
                    END as submissionstatus,
                    sub.timesubmitted
                FROM {user} u
                CROSS JOIN {codesandbox} cs
                INNER JOIN {course_modules} cm ON cm.instance = cs.id AND cm.module = 
                    (SELECT id FROM {modules} WHERE name = 'codesandbox')
                LEFT JOIN {codesandbox_submissions} sub ON sub.codesandboxid = cs.id 
                    AND sub.userid = u.id
                WHERE cs.course = :courseid
                    AND u.id IN (
                        SELECT DISTINCT ue.userid
                        FROM {user_enrolments} ue
                        JOIN {enrol} e ON e.id = ue.enrolid
                        WHERE e.courseid = :courseid2
                            AND ue.status = 0
                            AND e.status = 0
                    )
                    AND u.deleted = 0
                    AND u.suspended = 0
                    AND cm.visible = 1
                ORDER BY u.lastname, u.firstname, cs.name";
        
        $params = array(
            'courseid' => $params['courseid'],
            'courseid2' => $params['courseid']
        );
        
        $records = $DB->get_records_sql($sql, $params);
        
        // Format results
        $results = array();
        foreach ($records as $record) {
            $result = new stdClass();
            $result->userid = $record->userid;
            $result->username = $record->username;
            $result->fullname = fullname($record);
            $result->activityid = $record->activityid;
            $result->activityname = $record->activityname;
            $result->cmid = $record->cmid;
            $result->grade = ($record->grade >= 0) ? round($record->grade, 2) : null;
            $result->submissionstatus = $record->submissionstatus;
            $result->timesubmitted = $record->timesubmitted;
            
            $results[] = $result;
        }
        
        return $results;
    }
    
    /**
     * Returns description of get_sandbox_grades return value
     *
     * @return external_multiple_structure
     */
    public static function get_sandbox_grades_returns() {
        return new external_multiple_structure(
            new external_single_structure(array(
                'userid' => new external_value(PARAM_INT, 'User ID'),
                'username' => new external_value(PARAM_TEXT, 'Username'),
                'fullname' => new external_value(PARAM_TEXT, 'User full name'),
                'activityid' => new external_value(PARAM_INT, 'Activity ID'),
                'activityname' => new external_value(PARAM_TEXT, 'Activity name'),
                'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                'grade' => new external_value(PARAM_FLOAT, 'Grade value', VALUE_OPTIONAL),
                'submissionstatus' => new external_value(PARAM_TEXT, 'Submission status'),
                'timesubmitted' => new external_value(PARAM_INT, 'Time submitted', VALUE_OPTIONAL)
            ))
        );
    }
}