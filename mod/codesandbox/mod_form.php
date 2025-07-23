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
 * Code Sandbox configuration form
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_codesandbox_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;
        
        $mform = $this->_form;
        
        // General section
        $mform->addElement('header', 'general', get_string('general', 'form'));
        
        // Adding the "name" field
        $mform->addElement('text', 'name', get_string('name'), array('size' => '64'));
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        
        // Adding the standard "intro" and "introformat" fields
        $this->standard_intro_elements();
        
        // Starter code
        $mform->addElement('textarea', 'starter_code', get_string('startercode', 'codesandbox'), 
                          array('rows' => 10, 'cols' => 80));
        $mform->setType('starter_code', PARAM_RAW);
        $mform->addHelpButton('starter_code', 'startercode', 'codesandbox');
        $mform->setDefault('starter_code', "# Welcome to Python Code Sandbox!\n# Write your code below:\n\n");
        
        // Grading settings section
        $mform->addElement('header', 'gradingsettings', get_string('gradingsettings', 'codesandbox'));
        $mform->setExpanded('gradingsettings');
        
        // Enable grading checkbox
        $mform->addElement('checkbox', 'is_gradable', get_string('enablegrading', 'codesandbox'));
        $mform->setDefault('is_gradable', 0);
        
        // Test suite file upload
        $mform->addElement('filemanager', 'testsuitefiles', get_string('testsuite', 'codesandbox'), null,
                          array('subdirs' => 0, 
                                'maxbytes' => $CFG->maxbytes, 
                                'maxfiles' => 1, 
                                'accepted_types' => array('.py')));
        $mform->addHelpButton('testsuitefiles', 'testsuite', 'codesandbox');
        $mform->disabledIf('testsuitefiles', 'is_gradable');
        
        // Maximum grade
        $mform->addElement('text', 'grade_max', get_string('maximumgrade'), array('size' => 5));
        $mform->setType('grade_max', PARAM_FLOAT);
        $mform->setDefault('grade_max', 100);
        $mform->disabledIf('grade_max', 'is_gradable');
        
        // Add standard elements
        $this->standard_coursemodule_elements();
        
        // Add standard buttons
        $this->add_action_buttons();
    }
    
    /**
     * Prepare data for the form
     *
     * @param array $default_values
     */
    function data_preprocessing(&$default_values) {
        parent::data_preprocessing($default_values);
        
        // Prepare test suite files for form
        if ($this->current->instance && !empty($this->current->id)) {
            $draftitemid = file_get_submitted_draft_itemid('testsuitefiles');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_codesandbox', 
                                   'testsuite', 0, array('subdirs' => 0, 'maxfiles' => 1));
            $default_values['testsuitefiles'] = $draftitemid;
        }
    }
    
    /**
     * Validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        if (!empty($data['is_gradable']) && empty($data['testsuitefiles'])) {
            // Check if files are being uploaded
            $draftitemid = $data['testsuitefiles'];
            $info = file_get_draft_area_info($draftitemid);
            if ($info['filecount'] == 0) {
                $errors['testsuitefiles'] = get_string('required');
            }
        }
        
        if (!empty($data['grade_max']) && $data['grade_max'] <= 0) {
            $errors['grade_max'] = get_string('err_numeric', 'form');
        }
        
        return $errors;
    }
}