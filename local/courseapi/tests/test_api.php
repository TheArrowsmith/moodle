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
 * Test script for Course Management API
 *
 * Run this from command line: php test_api.php
 *
 * @package    local_courseapi
 * @copyright  2025 Course Management API
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');

// Test configuration
$base_url = $CFG->wwwroot . '/local/courseapi/api';
$test_username = 'teacher1';
$test_password = 'Teacher123!';
$test_course_id = 2;

/**
 * Make API request
 */
function make_request($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    
    $headers = ['Content-Type: application/json'];
    if ($token) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $http_code,
        'response' => json_decode($response, true)
    ];
}

echo "Course Management API Test Script\n";
echo "================================\n\n";

// Test 1: Authenticate and get token
echo "Test 1: POST /auth/token\n";
$result = make_request(
    $base_url . '/auth/token',
    'POST',
    [
        'username' => $test_username,
        'password' => $test_password,
        'course_id' => $test_course_id
    ]
);

if ($result['code'] === 200) {
    echo "✅ Authentication successful\n";
    $token = $result['response']['token'];
    echo "Token: " . substr($token, 0, 20) . "...\n";
    echo "User: " . $result['response']['user']['firstname'] . " " . $result['response']['user']['lastname'] . "\n";
} else {
    echo "❌ Authentication failed: " . json_encode($result['response']) . "\n";
    exit(1);
}

echo "\n";

// Test 2: Get user info
echo "Test 2: GET /user/me\n";
$result = make_request(
    $base_url . '/user/me',
    'GET',
    null,
    $token
);

if ($result['code'] === 200) {
    echo "✅ Get user info successful\n";
    echo "User ID: " . $result['response']['id'] . "\n";
    echo "Username: " . $result['response']['username'] . "\n";
} else {
    echo "❌ Get user info failed: " . json_encode($result['response']) . "\n";
}

echo "\n";

// Test 3: Get course management data
echo "Test 3: GET /course/{$test_course_id}/management_data\n";
$result = make_request(
    $base_url . "/course/{$test_course_id}/management_data",
    'GET',
    null,
    $token
);

if ($result['code'] === 200) {
    echo "✅ Get course data successful\n";
    echo "Course: " . $result['response']['course_name'] . "\n";
    echo "Sections: " . count($result['response']['sections']) . "\n";
    
    // Store first activity ID for later tests
    $first_activity_id = null;
    $first_section_id = null;
    foreach ($result['response']['sections'] as $section) {
        if ($first_section_id === null) {
            $first_section_id = $section['id'];
        }
        if (!empty($section['activities'])) {
            $first_activity_id = $section['activities'][0]['id'];
            break;
        }
    }
} else {
    echo "❌ Get course data failed: " . json_encode($result['response']) . "\n";
}

echo "\n";

// Test 4: Update activity
if ($first_activity_id) {
    echo "Test 4: PUT /activity/{$first_activity_id}\n";
    $result = make_request(
        $base_url . "/activity/{$first_activity_id}",
        'PUT',
        [
            'name' => 'Updated Activity Name ' . time(),
            'visible' => true
        ],
        $token
    );
    
    if ($result['code'] === 200) {
        echo "✅ Update activity successful\n";
        echo "Updated name: " . $result['response']['name'] . "\n";
    } else {
        echo "❌ Update activity failed: " . json_encode($result['response']) . "\n";
    }
} else {
    echo "Test 4: Skipped (no activity found)\n";
}

echo "\n";

// Test 5: Update section
if ($first_section_id) {
    echo "Test 5: PUT /section/{$first_section_id}\n";
    $result = make_request(
        $base_url . "/section/{$first_section_id}",
        'PUT',
        [
            'name' => 'Updated Section ' . time(),
            'summary' => '<p>This section has been updated via API</p>'
        ],
        $token
    );
    
    if ($result['code'] === 200) {
        echo "✅ Update section successful\n";
        echo "Updated name: " . $result['response']['name'] . "\n";
    } else {
        echo "❌ Update section failed: " . json_encode($result['response']) . "\n";
    }
} else {
    echo "Test 5: Skipped (no section found)\n";
}

echo "\n";

// Test 6: Create new activity
if ($first_section_id) {
    echo "Test 6: POST /activity\n";
    $result = make_request(
        $base_url . "/activity",
        'POST',
        [
            'courseid' => $test_course_id,
            'sectionid' => $first_section_id,
            'modname' => 'assign',
            'name' => 'API Test Assignment ' . time(),
            'intro' => 'This assignment was created via the Course Management API',
            'visible' => true
        ],
        $token
    );
    
    if ($result['code'] === 200) {
        echo "✅ Create activity successful\n";
        echo "New activity ID: " . $result['response']['id'] . "\n";
        echo "Activity name: " . $result['response']['name'] . "\n";
        
        // Store for deletion test
        $created_activity_id = $result['response']['id'];
    } else {
        echo "❌ Create activity failed: " . json_encode($result['response']) . "\n";
    }
} else {
    echo "Test 6: Skipped (no section found)\n";
}

echo "\n";

// Test 7: Delete activity
if (isset($created_activity_id)) {
    echo "Test 7: DELETE /activity/{$created_activity_id}\n";
    $result = make_request(
        $base_url . "/activity/{$created_activity_id}",
        'DELETE',
        null,
        $token
    );
    
    if ($result['code'] === 204) {
        echo "✅ Delete activity successful\n";
    } else {
        echo "❌ Delete activity failed: " . json_encode($result['response']) . "\n";
    }
} else {
    echo "Test 7: Skipped (no activity to delete)\n";
}

echo "\n";
echo "Test completed!\n";