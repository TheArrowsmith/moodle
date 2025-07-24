<?php
define('NO_DEBUG_DISPLAY', true);
define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');

use local_courseapi\jwt;

header('Content-Type: application/json');

// For testing only - generate token for admin user
$userid = 2; // Admin user ID
$course_id = 2;

try {
    // Get user
    $user = $DB->get_record('user', ['id' => $userid]);
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Generate JWT token
    $token = jwt::create_token($user->id, $course_id, 7200); // 2 hours for testing
    
    echo json_encode([
        'token' => $token,
        'expires_in' => 7200,
        'user' => [
            'id' => $user->id,
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname
        ]
    ]);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage()
    ]);
}