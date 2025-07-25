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
 * REST API router for local_courseapi
 *
 * @package    local_courseapi
 * @copyright  2025 Course Management API
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('NO_DEBUG_DISPLAY', true);
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');
require_once($CFG->dirroot . '/local/courseapi/classes/external.php');

use local_courseapi\jwt;
use local_courseapi\external;

// Set JSON response headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, Content-Type');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Get request path
$request_uri = $_SERVER['REQUEST_URI'];
$parsed_path = parse_url($request_uri, PHP_URL_PATH);

// Remove base path and index.php if present
$base_path = '/local/courseapi/api';
$path = str_replace($base_path, '', $parsed_path);
$path = str_replace('/index.php', '', $path);
$path = trim($path, '/');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Parse request body for JSON
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

/**
 * Send JSON response
 */
function send_response($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data);
    exit;
}

/**
 * Send error response
 */
function send_error($message, $code = 400) {
    send_response(['error' => $message], $code);
}

/**
 * Authenticate request using JWT
 */
function authenticate_request() {
    global $USER, $SESSION;
    
    // Skip auth for token endpoint
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER['REQUEST_URI'], '/auth/token') !== false) {
        return;
    }
    
    // Get authorization header (handle different server configs)
    $auth = null;
    if (function_exists('getallheaders')) {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? null;
    }
    
    // Fallback for servers where getallheaders() doesn't work
    if (!$auth && isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['HTTP_AUTHORIZATION'];
    }
    
    // Another fallback for some server configurations
    if (!$auth && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
        $auth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    
    if (!$auth) {
        send_error('Authentication token is missing', 401);
    }
    if (!preg_match('/Bearer\s+(\S+)/', $auth, $matches)) {
        send_error('Invalid authorization header', 401);
    }
    
    $token = $matches[1];
    
    try {
        // Verify token and get user
        $user = jwt::get_user_from_token($token);
        
        // Set up user session
        $USER = $user;
        $SESSION = new stdClass();
        
    } catch (Exception $e) {
        send_error('Invalid or expired authentication token', 401);
    }
}

// Authenticate all requests except token generation
authenticate_request();

// Route requests
try {
    // Parse path segments
    $segments = explode('/', $path);
    
    switch ($method) {
        case 'GET':
            // Category endpoints
            if ($path === 'category/tree') {
                // GET /category/tree
                $parent = isset($_GET['parent']) ? (int)$_GET['parent'] : 0;
                $includeHidden = isset($_GET['includeHidden']) ? filter_var($_GET['includeHidden'], FILTER_VALIDATE_BOOLEAN) : false;
                
                $result = external::get_category_tree($parent, $includeHidden);
                send_response($result);
                
            } else if (preg_match('/^category\/(\d+)\/courses$/', $path, $matches)) {
                // GET /category/{id}/courses
                $categoryid = (int)$matches[1];
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
                $perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;
                $sort = isset($_GET['sort']) ? $_GET['sort'] : 'fullname';
                $direction = isset($_GET['direction']) ? $_GET['direction'] : 'asc';
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                
                $result = external::get_category_courses($categoryid, $page, $perpage, $sort, $direction, $search);
                send_response($result);
                
            } else if (preg_match('/^category\/(\d+)$/', $path, $matches)) {
                // GET /category/{id}
                $categoryid = (int)$matches[1];
                $result = external::get_category($categoryid);
                send_response($result);
                
            // Course endpoints
            } else if ($path === 'course/list') {
                // GET /course/list
                $category = isset($_GET['category']) ? (int)$_GET['category'] : 0;
                $search = isset($_GET['search']) ? $_GET['search'] : '';
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
                $perpage = isset($_GET['perpage']) ? (int)$_GET['perpage'] : 20;
                $sort = isset($_GET['sort']) ? $_GET['sort'] : 'fullname';
                $direction = isset($_GET['direction']) ? $_GET['direction'] : 'asc';
                
                $result = external::get_course_list($category, $search, $page, $perpage, $sort, $direction);
                send_response($result);
                
            } else if (preg_match('/^course\/(\d+)\/teachers$/', $path, $matches)) {
                // GET /course/{id}/teachers
                $courseid = (int)$matches[1];
                $result = external::get_course_teachers($courseid);
                send_response($result);
                
            } else if (preg_match('/^course\/(\d+)\/management_data$/', $path, $matches)) {
                // GET /course/{courseId}/management_data
                $courseid = (int)$matches[1];
                $result = external::get_course_management_data($courseid);
                send_response($result);
                
            } else if (preg_match('/^course\/(\d+)$/', $path, $matches)) {
                // GET /course/{id}
                $courseid = (int)$matches[1];
                
                // Parse query parameters
                $include = isset($_GET['include']) ? explode(',', $_GET['include']) : [];
                $userinfo = isset($_GET['userinfo']) ? filter_var($_GET['userinfo'], FILTER_VALIDATE_BOOLEAN) : true;
                
                $result = external::get_course_details($courseid, $include, $userinfo);
                send_response($result);
                
            // Activity endpoints
            } else if ($path === 'activity/list') {
                // GET /activity/list
                $courseid = isset($_GET['courseid']) ? (int)$_GET['courseid'] : 0;
                if (!$courseid) {
                    send_error('Missing courseid parameter', 422);
                }
                
                $result = external::get_activity_list($courseid);
                send_response($result);
                
            // User endpoints
            } else if ($path === 'user/me') {
                // GET /user/me
                $result = external::get_user_info();
                send_response($result);
                
            } else {
                send_error('Endpoint not found', 404);
            }
            break;
            
        case 'PUT':
            // Category endpoints
            if (preg_match('/^category\/(\d+)$/', $path, $matches)) {
                // PUT /category/{id}
                $categoryid = (int)$matches[1];
                $name = $input['name'] ?? null;
                $description = $input['description'] ?? null;
                $visible = isset($input['visible']) ? (bool)$input['visible'] : null;
                
                $result = external::update_category($categoryid, $name, $description, $visible);
                send_response($result);
                
            // Course endpoints
            } else if (preg_match('/^course\/(\d+)$/', $path, $matches)) {
                // PUT /course/{id}
                $courseid = (int)$matches[1];
                $fullname = $input['fullname'] ?? null;
                $shortname = $input['shortname'] ?? null;
                $summary = $input['summary'] ?? null;
                $visible = isset($input['visible']) ? (bool)$input['visible'] : null;
                $startdate = $input['startdate'] ?? null;
                $enddate = $input['enddate'] ?? null;
                
                $result = external::update_course($courseid, $fullname, $shortname, $summary, $visible, $startdate, $enddate);
                send_response($result);
                
            // Activity endpoints
            } else if (preg_match('/^activity\/(\d+)$/', $path, $matches)) {
                // PUT /activity/{activityId}
                $activityid = (int)$matches[1];
                $name = $input['name'] ?? null;
                $visible = isset($input['visible']) ? (bool)$input['visible'] : null;
                
                $result = external::update_activity($activityid, $name, $visible);
                send_response($result);
                
            // Section endpoints
            } else if (preg_match('/^section\/(\d+)$/', $path, $matches)) {
                // PUT /section/{sectionId}
                $sectionid = (int)$matches[1];
                $name = $input['name'] ?? null;
                $visible = isset($input['visible']) ? (bool)$input['visible'] : null;
                $summary = $input['summary'] ?? null;
                
                $result = external::update_section($sectionid, $name, $visible, $summary);
                send_response($result);
                
            } else {
                send_error('Endpoint not found', 404);
            }
            break;
            
        case 'POST':
            // Category endpoints
            if ($path === 'category') {
                // POST /category
                $name = $input['name'] ?? null;
                $parent = $input['parent'] ?? 0;
                $description = $input['description'] ?? '';
                $visible = $input['visible'] ?? true;
                
                if (!$name) {
                    send_error('Missing required field: name', 422);
                }
                
                $result = external::create_category($name, $parent, $description, $visible);
                send_response($result, 201);
                
            } else if (preg_match('/^category\/(\d+)\/move$/', $path, $matches)) {
                // POST /category/{id}/move
                $categoryid = (int)$matches[1];
                $direction = $input['direction'] ?? null;
                
                if (!$direction) {
                    send_error('Missing direction parameter', 422);
                }
                
                $result = external::move_category($categoryid, $direction);
                send_response($result);
                
            } else if (preg_match('/^category\/(\d+)\/visibility$/', $path, $matches)) {
                // POST /category/{id}/visibility
                $categoryid = (int)$matches[1];
                $result = external::toggle_category_visibility($categoryid);
                send_response($result);
                
            // Course endpoints
            } else if ($path === 'course') {
                // POST /course
                $fullname = $input['fullname'] ?? null;
                $shortname = $input['shortname'] ?? null;
                $category = $input['category'] ?? null;
                $summary = $input['summary'] ?? '';
                $format = $input['format'] ?? 'topics';
                $numsections = $input['numsections'] ?? 10;
                $startdate = $input['startdate'] ?? time();
                $enddate = $input['enddate'] ?? 0;
                $visible = $input['visible'] ?? true;
                $options = $input['options'] ?? [];
                
                if (!$fullname || !$shortname || !$category) {
                    send_error('Missing required field: ' . (!$fullname ? 'fullname' : (!$shortname ? 'shortname' : 'category')), 422);
                }
                
                $result = external::create_course($fullname, $shortname, $category, $summary, $format, $numsections, $startdate, $enddate, $visible, $options);
                send_response($result, 201);
                
            } else if (preg_match('/^course\/(\d+)\/visibility$/', $path, $matches)) {
                // POST /course/{id}/visibility
                $courseid = (int)$matches[1];
                $result = external::toggle_course_visibility($courseid);
                send_response($result);
                
            } else if (preg_match('/^course\/(\d+)\/move$/', $path, $matches)) {
                // POST /course/{id}/move
                $courseid = (int)$matches[1];
                $categoryid = $input['categoryid'] ?? null;
                
                if (!$categoryid) {
                    send_error('Missing categoryid', 422);
                }
                
                $result = external::move_course_to_category($courseid, $categoryid);
                send_response($result);
                
            // Activity endpoints
            } else if ($path === 'activity') {
                // POST /activity
                $courseid = $input['courseid'] ?? null;
                $sectionid = $input['sectionid'] ?? null;
                $modname = $input['modname'] ?? null;
                $name = $input['name'] ?? null;
                $intro = $input['intro'] ?? '';
                $visible = $input['visible'] ?? true;
                
                if (!$courseid || !$sectionid || !$modname || !$name) {
                    send_error('Missing required fields', 422);
                }
                
                $result = external::create_activity($courseid, $sectionid, $modname, $name, $intro, $visible);
                send_response($result);
                
            } else if (preg_match('/^activity\/(\d+)\/visibility$/', $path, $matches)) {
                // POST /activity/{id}/visibility
                $activityid = (int)$matches[1];
                $result = external::toggle_activity_visibility($activityid);
                send_response($result);
                
            } else if (preg_match('/^activity\/(\d+)\/duplicate$/', $path, $matches)) {
                // POST /activity/{id}/duplicate
                $activityid = (int)$matches[1];
                $result = external::duplicate_activity($activityid);
                send_response($result, 201);
                
            // Section endpoints
            } else if ($path === 'section') {
                // POST /section
                $courseid = $input['courseid'] ?? null;
                $name = $input['name'] ?? '';
                $summary = $input['summary'] ?? '';
                $visible = $input['visible'] ?? true;
                
                if (!$courseid) {
                    send_error('Missing required field: courseid', 422);
                }
                
                $result = external::create_section($courseid, $name, $summary, $visible);
                send_response($result, 201);
                
            } else if (preg_match('/^section\/(\d+)\/visibility$/', $path, $matches)) {
                // POST /section/{id}/visibility
                $sectionid = (int)$matches[1];
                $result = external::toggle_section_visibility($sectionid);
                send_response($result);
                
            } else if (preg_match('/^section\/(\d+)\/reorder_activities$/', $path, $matches)) {
                // POST /section/{sectionId}/reorder_activities
                $sectionid = (int)$matches[1];
                $activity_ids = $input['activity_ids'] ?? [];
                
                if (empty($activity_ids)) {
                    send_error('Missing activity_ids', 422);
                }
                
                $result = external::reorder_section_activities($sectionid, $activity_ids);
                send_response($result);
                
            } else if (preg_match('/^section\/(\d+)\/move_activity$/', $path, $matches)) {
                // POST /section/{sectionId}/move_activity
                $sectionid = (int)$matches[1];
                $activityid = $input['activityid'] ?? null;
                $position = $input['position'] ?? 0;
                
                if (!$activityid) {
                    send_error('Missing activityid', 422);
                }
                
                $result = external::move_activity_to_section($sectionid, $activityid, $position);
                send_response($result);
                
            // Authentication endpoints
            } else if ($path === 'auth/token') {
                // POST /auth/token
                
                $username = isset($input['username']) ? $input['username'] : null;
                $password = isset($input['password']) ? $input['password'] : null;
                
                if (!$username || !$password) {
                    send_error('Missing username or password', 422);
                }
                
                try {
                    $result = jwt::authenticate_user($username, $password);
                    send_response($result);
                } catch (Exception $e) {
                    send_error('Invalid username or password', 401);
                }
                
            } else {
                send_error('Endpoint not found', 404);
            }
            break;
            
        case 'DELETE':
            // Category endpoints
            if (preg_match('/^category\/(\d+)$/', $path, $matches)) {
                // DELETE /category/{id}
                $categoryid = (int)$matches[1];
                $recursive = isset($_GET['recursive']) ? filter_var($_GET['recursive'], FILTER_VALIDATE_BOOLEAN) : false;
                
                external::delete_category($categoryid, $recursive);
                send_response(null, 204);
                
            // Course endpoints
            } else if (preg_match('/^course\/(\d+)$/', $path, $matches)) {
                // DELETE /course/{id}
                $courseid = (int)$matches[1];
                
                // Parse query parameters
                $async = isset($_GET['async']) ? filter_var($_GET['async'], FILTER_VALIDATE_BOOLEAN) : false;
                $confirm = isset($_GET['confirm']) ? filter_var($_GET['confirm'], FILTER_VALIDATE_BOOLEAN) : false;
                
                external::delete_course($courseid, $async, $confirm);
                send_response(null, 204);
                
            // Activity endpoints
            } else if (preg_match('/^activity\/(\d+)$/', $path, $matches)) {
                // DELETE /activity/{activityId}
                $activityid = (int)$matches[1];
                
                external::delete_activity($activityid);
                send_response(null, 204);
                
            // Section endpoints
            } else if (preg_match('/^section\/(\d+)$/', $path, $matches)) {
                // DELETE /section/{id}
                $sectionid = (int)$matches[1];
                
                external::delete_section($sectionid);
                send_response(null, 204);
                
            } else {
                send_error('Endpoint not found', 404);
            }
            break;
            
        default:
            send_error('Method not allowed', 405);
    }
    
} catch (Exception $e) {
    // Log error for debugging
    error_log('Course API Error: ' . $e->getMessage());
    
    // Send appropriate error response
    if ($e instanceof moodle_exception) {
        $code = 400;
        if ($e->errorcode === 'invalidtoken' || $e->errorcode === 'notloggedin') {
            $code = 401;
        } else if ($e->errorcode === 'nopermissions') {
            $code = 403;
        } else if ($e->errorcode === 'invalidrecord') {
            $code = 404;
        }
        // For Moodle exceptions, get the localized message
        $message = $e->errorcode;
        if ($e->module) {
            $message = $e->module . '/' . $e->errorcode;
        }
        // In development, add more details
        if (defined('DEBUG_DEVELOPER') && DEBUG_DEVELOPER) {
            $message .= ' - ' . $e->getMessage();
        }
        send_error($message, $code);
    } else {
        // For development, show actual error
        if (defined('DEBUG_DEVELOPER') && DEBUG_DEVELOPER) {
            send_error('Internal server error: ' . $e->getMessage(), 500);
        } else {
            send_error('Internal server error', 500);
        }
    }
}