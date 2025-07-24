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
 * Language strings for mod_codesandbox
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['modulename'] = 'Code Sandbox';
$string['modulenameplural'] = 'Code Sandboxes';
$string['modulename_help'] = 'The Code Sandbox activity allows students to write and execute code in Python, Ruby, or Elixir directly in the browser.';
$string['codesandbox:addinstance'] = 'Add a new Code Sandbox';
$string['codesandbox:view'] = 'View Code Sandbox';
$string['codesandbox:submit'] = 'Submit code to Code Sandbox';
$string['codesandbox:grade'] = 'Grade Code Sandbox submissions';
$string['pluginadministration'] = 'Code Sandbox administration';
$string['pluginname'] = 'Code Sandbox';

// Form strings
$string['name'] = 'Name';
$string['languagesettings'] = 'Language Settings';
$string['defaultlanguage'] = 'Default language';
$string['defaultlanguage_help'] = 'The default programming language for this activity.';
$string['allowedlanguages'] = 'Allowed languages';
$string['allowedlanguages_help'] = 'Select which programming languages students can use. They will be able to switch between these languages.';
$string['language_python'] = 'Python';
$string['language_ruby'] = 'Ruby';
$string['language_elixir'] = 'Elixir';
$string['startercode'] = 'Starter code';
$string['startercode_help'] = 'Initial code that will be displayed in the editor when students first open the activity.';
$string['gradingsettings'] = 'Grading settings';
$string['enablegrading'] = 'Enable automatic grading';
$string['testsuite'] = 'Test suite file';
$string['testsuite_help'] = 'Upload a Python unittest file to automatically grade student submissions.';
$string['maximumgrade'] = 'Maximum grade';

// View page strings
$string['codeeditor'] = 'Code Editor';
$string['runcode'] = 'Run Code';
$string['clearoutput'] = 'Clear Output';
$string['output'] = 'Output';
$string['executing'] = 'Executing code...';
$string['executionerror'] = 'Execution Error';
$string['language'] = 'Language';
$string['selectlanguage'] = 'Select Language';

// Error messages
$string['invalidcodesandboxid'] = 'Invalid Code Sandbox ID';
$string['couldnotconnecttoapi'] = 'Could not connect to code execution API';