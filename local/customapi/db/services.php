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
 * Web service definitions for local_customapi
 *
 * @package    local_customapi
 * @copyright  2024 Your Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
    'local_customapi_get_sandbox_grades' => array(
        'classname'   => 'local_customapi_external',
        'methodname'  => 'get_sandbox_grades',
        'classpath'   => 'local/customapi/classes/external.php',
        'description' => 'Get grades for code sandbox activities in a course',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'moodle/grade:viewall',
        'services'    => array(MOODLE_OFFICIAL_MOBILE_SERVICE, 'customapi')
    )
);

$services = array(
    'Custom API Service' => array(
        'functions' => array('local_customapi_get_sandbox_grades'),
        'restrictedusers' => 1,
        'enabled' => 1,
        'shortname' => 'customapi'
    )
);