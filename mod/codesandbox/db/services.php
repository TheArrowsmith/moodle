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
 * Web service definitions for mod_codesandbox
 *
 * @package    mod_codesandbox
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'mod_codesandbox_submit_code' => array(
        'classname'   => 'mod_codesandbox_external',
        'methodname'  => 'submit_code',
        'classpath'   => 'mod/codesandbox/classes/external.php',
        'description' => 'Submit code for execution and grading',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'mod/codesandbox:submit'
    )
);

$services = array(
    'Code Sandbox Service' => array(
        'functions' => array('mod_codesandbox_submit_code'),
        'restrictedusers' => 0,
        'enabled' => 1
    )
);