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
    
    'local_courseapi_create_course' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'create_course',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Create a new course',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:create',
    ],
    
    'local_courseapi_delete_course' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'delete_course',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Delete a course',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:delete',
    ],
    
    'local_courseapi_get_course_details' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_course_details',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get course details',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:view',
    ],
    
    // Category management functions
    'local_courseapi_get_category_tree' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_category_tree',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get full category hierarchy',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> '',
    ],
    
    'local_courseapi_get_category' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_category',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get single category details',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> '',
    ],
    
    'local_courseapi_get_category_courses' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_category_courses',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get courses in category with pagination',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> '',
    ],
    
    'local_courseapi_create_category' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'create_category',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Create a new category',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/category:manage',
    ],
    
    'local_courseapi_update_category' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'update_category',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Update a category',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/category:manage',
    ],
    
    'local_courseapi_delete_category' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'delete_category',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Delete a category',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/category:manage',
    ],
    
    'local_courseapi_toggle_category_visibility' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'toggle_category_visibility',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Toggle category visibility',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/category:manage',
    ],
    
    'local_courseapi_move_category' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'move_category',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Move category up or down',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/category:manage',
    ],
    
    // Course management functions
    'local_courseapi_get_course_list' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_course_list',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get list of courses with filters',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> '',
    ],
    
    'local_courseapi_update_course' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'update_course',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Update course settings',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:update',
    ],
    
    'local_courseapi_toggle_course_visibility' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'toggle_course_visibility',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Toggle course visibility',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:visibility',
    ],
    
    'local_courseapi_move_course_to_category' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'move_course_to_category',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Move course to different category',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:update',
    ],
    
    'local_courseapi_get_course_teachers' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_course_teachers',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Get course teachers and enrollments',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:view',
    ],
    
    // Activity management functions
    'local_courseapi_get_activity_list' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'get_activity_list',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'List all activities for a course',
        'type'        => 'read',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:view',
    ],
    
    'local_courseapi_toggle_activity_visibility' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'toggle_activity_visibility',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Quick visibility toggle for activity',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:manageactivities',
    ],
    
    'local_courseapi_duplicate_activity' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'duplicate_activity',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Duplicate an activity',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:manageactivities',
    ],
    
    // Section management functions
    'local_courseapi_create_section' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'create_section',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Create new section',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:update',
    ],
    
    'local_courseapi_delete_section' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'delete_section',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Delete section',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:update',
    ],
    
    'local_courseapi_toggle_section_visibility' => [
        'classname'   => 'local_courseapi\external',
        'methodname'  => 'toggle_section_visibility',
        'classpath'   => 'local/courseapi/classes/external.php',
        'description' => 'Toggle section visibility',
        'type'        => 'write',
        'ajax'        => true,
        'capabilities'=> 'moodle/course:sectionvisibility',
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
            'local_courseapi_create_course',
            'local_courseapi_delete_course',
            'local_courseapi_get_course_details',
            // Category management
            'local_courseapi_get_category_tree',
            'local_courseapi_get_category',
            'local_courseapi_get_category_courses',
            'local_courseapi_create_category',
            'local_courseapi_update_category',
            'local_courseapi_delete_category',
            'local_courseapi_toggle_category_visibility',
            'local_courseapi_move_category',
            // Course management
            'local_courseapi_get_course_list',
            'local_courseapi_update_course',
            'local_courseapi_toggle_course_visibility',
            'local_courseapi_move_course_to_category',
            'local_courseapi_get_course_teachers',
            // Activity management
            'local_courseapi_get_activity_list',
            'local_courseapi_toggle_activity_visibility',
            'local_courseapi_duplicate_activity',
            // Section management
            'local_courseapi_create_section',
            'local_courseapi_delete_section',
            'local_courseapi_toggle_section_visibility',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'courseapi',
        'downloadfiles' => 0,
        'uploadfiles' => 0
    ]
];