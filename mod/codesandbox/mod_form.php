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
        
        // Language settings section
        $mform->addElement('header', 'languagesettings', get_string('languagesettings', 'codesandbox'));
        $mform->setExpanded('languagesettings');
        
        // Default language selection
        $languages = array(
            'python' => get_string('language_python', 'codesandbox'),
            'ruby' => get_string('language_ruby', 'codesandbox'),
            'elixir' => get_string('language_elixir', 'codesandbox')
        );
        $mform->addElement('select', 'language', get_string('defaultlanguage', 'codesandbox'), $languages);
        $mform->setDefault('language', 'python');
        $mform->addHelpButton('language', 'defaultlanguage', 'codesandbox');
        
        // Allowed languages (multiselect)
        $select = $mform->addElement('select', 'allowed_languages', get_string('allowedlanguages', 'codesandbox'), $languages);
        $select->setMultiple(true);
        $mform->setDefault('allowed_languages', array('python', 'ruby', 'elixir'));
        $mform->addHelpButton('allowed_languages', 'allowedlanguages', 'codesandbox');
        
        // Starter code
        $mform->addElement('textarea', 'starter_code', get_string('startercode', 'codesandbox'), 
                          array('rows' => 10, 'cols' => 80));
        $mform->setType('starter_code', PARAM_RAW);
        $mform->addHelpButton('starter_code', 'startercode', 'codesandbox');
        $mform->setDefault('starter_code', "# Welcome to Code Sandbox!\n# Write your code below:\n\n");
        
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
        
        // Prepare allowed languages
        if (!empty($default_values['allowed_languages'])) {
            $default_values['allowed_languages'] = explode(',', $default_values['allowed_languages']);
        }
    }
    
}