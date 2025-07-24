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
 * External services definition for local_courseapi
 *
 * @package    local_courseapi
 * @copyright  2025 Course Management API
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_courseapi_get_course_management_data' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_course_management_data',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get course management data including sections and activities',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:update',
    ],
    
    'local_courseapi_update_activity' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'update_activity',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Update activity properties',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:manageactivities',
    ],
    
    'local_courseapi_update_section' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'update_section',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Update section properties',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:update',
    ],
    
    'local_courseapi_reorder_section_activities' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'reorder_section_activities',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Reorder activities within a section',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:manageactivities',
    ],
    
    'local_courseapi_delete_activity' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'delete_activity',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Delete an activity',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:manageactivities',
    ],
    
    'local_courseapi_move_activity_to_section' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'move_activity_to_section',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Move an activity to a different section',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:manageactivities',
    ],
    
    'local_courseapi_create_activity' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'create_activity',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Create a new activity',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:manageactivities',
    ],
    
    'local_courseapi_get_user_info' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_user_info',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get information about the authenticated user',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> '',
    ],
];

$services = [
    'Course Management API' => [
        'functions' => [
            'local_courseapi_get_course_management_data',
            'local_courseapi_update_activity',
            'local_courseapi_update_section',
            'local_courseapi_reorder_section_activities',
            'local_courseapi_delete_activity',
            'local_courseapi_move_activity_to_section',
            'local_courseapi_create_activity',
            'local_courseapi_get_user_info',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'courseapi',
        'downloadfiles' => 0,
        'uploadfiles' => 0
    ]
];