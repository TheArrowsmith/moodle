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
 * The main markdownfile configuration form
 *
 * @package    mod_markdownfile
 * @copyright  2025 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form
 */
class mod_markdownfile_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are showed.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('markdownfilename', 'markdownfile'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $mform->addHelpButton('name', 'markdownfilename', 'markdownfile');

        // Adding the standard "intro" and "introformat" fields.
        $this->standard_intro_elements();

        // Content section
        $mform->addElement('header', 'contentsection', get_string('contentheader', 'markdownfile'));

        // Choice between uploading a file or entering markdown content directly
        $options = array();
        $options[] = $mform->createElement('radio', 'contenttype', '', get_string('uploadfile', 'markdownfile'), 'file');
        $options[] = $mform->createElement('radio', 'contenttype', '', get_string('entermarkdown', 'markdownfile'), 'text');
        $mform->addGroup($options, 'contenttypegrp', get_string('contenttype', 'markdownfile'), array(' '), false);
        $mform->setDefault('contenttype', 'text');

        // File upload
        $mform->addElement('filemanager', 'markdownfilefile', get_string('selectfile', 'markdownfile'), null,
                          array('maxbytes' => $CFG->maxbytes, 'subdirs' => 0, 'maxfiles' => 1,
                                'accepted_types' => array('.md', '.markdown', '.txt')));
        $mform->disabledIf('markdownfilefile', 'contenttype', 'eq', 'text');

        // Markdown text editor
        $mform->addElement('textarea', 'content', get_string('content', 'markdownfile'), array('rows' => 20, 'cols' => 80));
        $mform->setType('content', PARAM_RAW);
        $mform->disabledIf('content', 'contenttype', 'eq', 'file');
        $mform->addHelpButton('content', 'content', 'markdownfile');

        // Display settings
        $mform->addElement('header', 'displaysettings', get_string('displaysettings', 'markdownfile'));

        // Display options
        $mform->addElement('select', 'display', get_string('displayselect', 'markdownfile'),
            array(0 => get_string('displayauto', 'markdownfile'),
                  1 => get_string('displayembed', 'markdownfile'),
                  2 => get_string('displaydownload', 'markdownfile')));
        $mform->setDefault('display', 0);
        $mform->addHelpButton('display', 'displayselect', 'markdownfile');

        // Standard course module elements.
        $this->standard_coursemodule_elements();

        // Standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        
        // Check that either content or file is provided
        if ($data['contenttype'] == 'text' && empty($data['content'])) {
            $errors['content'] = get_string('required');
        }
        
        return $errors;
    }

    /**
     * Modify data before form display
     *
     * @param array $default_values
     */
    public function data_preprocessing(&$default_values) {
        if ($this->current->instance) {
            // Editing existing instance
            $context = context_module::instance($this->current->coursemodule);
            
            // Prepare file manager
            $draftitemid = file_get_submitted_draft_itemid('markdownfilefile');
            file_prepare_draft_area($draftitemid, $context->id, 'mod_markdownfile', 'content', 0,
                                    array('subdirs' => 0, 'maxfiles' => 1));
            $default_values['markdownfilefile'] = $draftitemid;
            
            // Determine content type
            if (!empty($default_values['content'])) {
                $default_values['contenttype'] = 'text';
            } else {
                $default_values['contenttype'] = 'file';
            }
        }
    }
}