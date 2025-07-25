<?php
// This file is part of Moodle - http://moodle.org/

define('AJAX_SCRIPT', true);

require_once('../../config.php');
require_once($CFG->dirroot.'/lib/coursecatlib.php');

// Require login
require_login();

// Validate sesskey if provided
$sesskey = optional_param('sesskey', '', PARAM_RAW);
if (!empty($sesskey) && !confirm_sesskey($sesskey)) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid sesskey']);
    exit;
}

// Check capabilities
$systemcontext = context_system::instance();
if (!has_any_capability(['moodle/category:manage', 'moodle/course:create'], $systemcontext)) {
    http_response_code(403);
    echo json_encode(['error' => 'No permission to access categories']);
    exit;
}

// Get parameters
$action = required_param('action', PARAM_ALPHA);
$parentid = optional_param('parentid', 0, PARAM_INT);

header('Content-Type: application/json');

try {
    switch ($action) {
        case 'list':
            // Get categories for a parent
            $categories = [];
            
            if ($parentid == 0) {
                // Get top-level categories
                $categorieslist = coursecat::get(0)->get_children();
            } else {
                // Get children of specific category
                $parent = coursecat::get($parentid);
                $categorieslist = $parent->get_children();
            }
            
            foreach ($categorieslist as $cat) {
                $categories[] = [
                    'id' => $cat->id,
                    'name' => $cat->get_formatted_name(),
                    'parent' => $cat->parent,
                    'visible' => $cat->visible,
                    'coursecount' => $cat->get_courses_count(),
                    'childrencount' => $cat->get_children_count(),
                    'description' => $cat->description,
                    'path' => $cat->path
                ];
            }
            
            echo json_encode([
                'success' => true,
                'categories' => $categories
            ]);
            break;
            
        case 'info':
            // Get info about a specific category
            $categoryid = required_param('categoryid', PARAM_INT);
            $category = coursecat::get($categoryid);
            
            echo json_encode([
                'success' => true,
                'category' => [
                    'id' => $category->id,
                    'name' => $category->get_formatted_name(),
                    'parent' => $category->parent,
                    'visible' => $category->visible,
                    'coursecount' => $category->get_courses_count(),
                    'childrencount' => $category->get_children_count(),
                    'description' => $category->description,
                    'path' => $category->path
                ]
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>