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
$base_path = '/local/courseapi/api';
$path = str_replace($base_path, '', parse_url($request_uri, PHP_URL_PATH));
$path = trim($path, '/');

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Parse request body for JSON
$input = json_decode(file_get_contents('php://input'), true);

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
    
    // Get authorization header
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        send_error('Missing authorization header', 401);
    }
    
    // Extract token
    $auth = $headers['Authorization'];
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
        send_error('Invalid or expired token', 401);
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
            if (preg_match('/^course\/(\d+)\/management_data$/', $path, $matches)) {
                // GET /course/{courseId}/management_data
                $courseid = (int)$matches[1];
                $result = external::get_course_management_data($courseid);
                send_response($result);
                
            } else if ($path === 'user/me') {
                // GET /user/me
                $result = external::get_user_info();
                send_response($result);
                
            } else {
                send_error('Endpoint not found', 404);
            }
            break;
            
        case 'PUT':
            if (preg_match('/^activity\/(\d+)$/', $path, $matches)) {
                // PUT /activity/{activityId}
                $activityid = (int)$matches[1];
                $name = $input['name'] ?? null;
                $visible = isset($input['visible']) ? (bool)$input['visible'] : null;
                
                $result = external::update_activity($activityid, $name, $visible);
                send_response($result);
                
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
            if (preg_match('/^section\/(\d+)\/reorder_activities$/', $path, $matches)) {
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
                
            } else if ($path === 'auth/token') {
                // POST /auth/token
                $username = $input['username'] ?? null;
                $password = $input['password'] ?? null;
                $course_id = $input['course_id'] ?? null;
                
                if (!$username || !$password) {
                    send_error('Missing username or password', 422);
                }
                
                try {
                    $result = jwt::authenticate_user($username, $password, $course_id);
                    send_response($result);
                } catch (Exception $e) {
                    send_error('Invalid username or password', 401);
                }
                
            } else {
                send_error('Endpoint not found', 404);
            }
            break;
            
        case 'DELETE':
            if (preg_match('/^activity\/(\d+)$/', $path, $matches)) {
                // DELETE /activity/{activityId}
                $activityid = (int)$matches[1];
                
                external::delete_activity($activityid);
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
        send_error($e->getMessage(), $code);
    } else {
        send_error('Internal server error', 500);
    }
}