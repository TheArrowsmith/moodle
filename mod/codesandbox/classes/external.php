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
 * External functions for mod_codesandbox
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/codesandbox/lib.php');

/**
 * External functions class for mod_codesandbox
 */
class mod_codesandbox_external extends external_api {
    
    /**
     * Returns description of submit_code parameters
     *
     * @return external_function_parameters
     */
    public static function submit_code_parameters() {
        return new external_function_parameters(
            array(
                'cmid' => new external_value(PARAM_INT, 'Course module ID'),
                'code' => new external_value(PARAM_RAW, 'Code to submit')
            )
        );
    }
    
    /**
     * Submit code for execution and grading
     *
     * @param int $cmid Course module ID
     * @param string $code Code to submit
     * @return array Result
     */
    public static function submit_code($cmid, $code) {
        global $DB, $USER;
        
        // Validate parameters
        $params = self::validate_parameters(self::submit_code_parameters(), array(
            'cmid' => $cmid,
            'code' => $code
        ));
        
        // Get course module and context
        $cm = get_coursemodule_from_id('codesandbox', $params['cmid'], 0, false, MUST_EXIST);
        $context = context_module::instance($cm->id);
        
        // Check capability
        self::validate_context($context);
        require_capability('mod/codesandbox:submit', $context);
        
        // Get codesandbox instance
        $codesandbox = $DB->get_record('codesandbox', array('id' => $cm->instance), '*', MUST_EXIST);
        $codesandbox->coursemodule = $cm->id;
        
        // Process submission
        $submission = codesandbox_process_submission($codesandbox, $USER->id, $params['code']);
        
        $result = array(
            'success' => true,
            'submissionid' => $submission->id
        );
        
        // Add test results if gradable
        if ($codesandbox->is_gradable && !empty($submission->feedback)) {
            $feedback = json_decode($submission->feedback, true);
            if ($feedback) {
                $result['results'] = array(
                    'score' => $submission->score,
                    'total_tests' => count($feedback),
                    'passed_tests' => count(array_filter($feedback, function($r) { return $r['passed']; })),
                    'results' => $feedback
                );
            }
        }
        
        return $result;
    }
    
    /**
     * Returns description of submit_code return value
     *
     * @return external_single_structure
     */
    public static function submit_code_returns() {
        return new external_single_structure(
            array(
                'success' => new external_value(PARAM_BOOL, 'Whether submission was successful'),
                'submissionid' => new external_value(PARAM_INT, 'Submission ID'),
                'results' => new external_single_structure(
                    array(
                        'score' => new external_value(PARAM_FLOAT, 'Score between 0 and 1'),
                        'total_tests' => new external_value(PARAM_INT, 'Total number of tests'),
                        'passed_tests' => new external_value(PARAM_INT, 'Number of passed tests'),
                        'results' => new external_multiple_structure(
                            new external_single_structure(
                                array(
                                    'test_name' => new external_value(PARAM_TEXT, 'Test name'),
                                    'passed' => new external_value(PARAM_BOOL, 'Whether test passed'),
                                    'message' => new external_value(PARAM_RAW, 'Error message if failed', VALUE_OPTIONAL)
                                )
                            )
                        )
                    ), 'Test results', VALUE_OPTIONAL
                )
            )
        );
    }
}