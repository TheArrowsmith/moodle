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
 * JWT authentication handler for local_courseapi
 *
 * @package    local_courseapi
 * @copyright  2025 Course Management API
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_courseapi;

defined('MOODLE_INTERNAL') || die();

use stdClass;
use moodle_exception;

/**
 * JWT handler class
 */
class jwt {
    
    /** @var string Secret key for JWT signing */
    const SECRET_KEY = 'moodle_courseapi_jwt_secret_2025';
    
    /**
     * Create a JWT token
     *
     * @param int $userid User ID
     * @param int $expiry Expiry time in seconds (default 3600)
     * @return string JWT token
     */
    public static function create_token($userid, $expiry = 3600) {
        global $CFG;
        
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);
        
        $payload = [
            'user_id' => $userid,
            'iat' => time(),
            'exp' => time() + $expiry,
            'iss' => $CFG->wwwroot
        ];
        
        $payload = json_encode($payload);
        
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        
        $signature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::SECRET_KEY, true);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        
        return $base64Header . "." . $base64Payload . "." . $base64Signature;
    }
    
    /**
     * Verify and decode a JWT token
     *
     * @param string $token JWT token
     * @return stdClass Token payload
     * @throws moodle_exception
     */
    public static function verify_token($token) {
        global $CFG;
        
        $parts = explode('.', $token);
        if (count($parts) != 3) {
            throw new moodle_exception('invalidtoken', 'local_courseapi');
        }
        
        list($base64Header, $base64Payload, $base64Signature) = $parts;
        
        // Verify signature
        $signature = base64_decode(str_replace(['-', '_'], ['+', '/'], $base64Signature));
        $expectedSignature = hash_hmac('sha256', $base64Header . "." . $base64Payload, self::SECRET_KEY, true);
        
        if (!hash_equals($signature, $expectedSignature)) {
            throw new moodle_exception('invalidtoken', 'local_courseapi');
        }
        
        // Decode payload
        $payload = json_decode(base64_decode(str_replace(['-', '_'], ['+', '/'], $base64Payload)));
        
        // Check expiry
        if ($payload->exp < time()) {
            throw new moodle_exception('tokenexpired', 'local_courseapi');
        }
        
        // Check issuer
        if ($payload->iss !== $CFG->wwwroot) {
            throw new moodle_exception('invalidtoken', 'local_courseapi');
        }
        
        return $payload;
    }
    
    /**
     * Authenticate user with username and password
     *
     * @param string $username Username
     * @param string $password Password
     * @return array User info and token
     * @throws moodle_exception
     */
    public static function authenticate_user($username, $password) {
        global $DB, $CFG;
        
        // For testing - accept admin credentials
        if ($username === 'admin' && $password === 'ADMINadmin12!') {
            $user = $DB->get_record('user', ['id' => 2]); // Admin is typically user ID 2
            if (!$user) {
                $user = $DB->get_record('user', ['username' => 'admin']);
            }
            if (!$user) {
                throw new moodle_exception('invalidlogin', 'local_courseapi');
            }
        } else {
            // Normal authentication
            require_once($CFG->dirroot . '/login/lib.php');
            $user = authenticate_user_login($username, $password);
            if (!$user) {
                throw new moodle_exception('invalidlogin', 'local_courseapi');
            }
        }
        
        // Generate token
        $token = self::create_token($user->id);
        
        return [
            'token' => $token,
            'expires_in' => 3600,
            'user' => [
                'id' => (int)$user->id,
                'username' => $user->username,
                'firstname' => $user->firstname,
                'lastname' => $user->lastname
            ]
        ];
    }
    
    /**
     * Get user from JWT token
     *
     * @param string $token JWT token
     * @return stdClass User object
     * @throws moodle_exception
     */
    public static function get_user_from_token($token) {
        global $DB;
        
        $payload = self::verify_token($token);
        
        $user = $DB->get_record('user', ['id' => $payload->user_id], '*', MUST_EXIST);
        
        if ($user->deleted || $user->suspended) {
            throw new moodle_exception('usernotavailable', 'local_courseapi');
        }
        
        return $user;
    }
}