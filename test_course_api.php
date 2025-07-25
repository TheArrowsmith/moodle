<?php
/**
 * Test script for Course Management API endpoints
 */

// API base URL
$baseUrl = 'http://localhost:8888/local/courseapi/api/index.php';

// Test credentials
$username = 'admin';
$password = 'Admin123!';

// Colors for output
$green = "\033[0;32m";
$red = "\033[0;31m";
$yellow = "\033[0;33m";
$reset = "\033[0m";

// Function to make API requests
function apiRequest($url, $method = 'GET', $data = null, $token = null) {
    $ch = curl_init();
    
    $options = [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $method,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            $token ? 'Authorization: Bearer ' . $token : ''
        ]
    ];
    
    if ($data && in_array($method, ['POST', 'PUT'])) {
        $options[CURLOPT_POSTFIELDS] = json_encode($data);
    }
    
    curl_setopt_array($ch, $options);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'code' => $httpCode,
        'body' => json_decode($response, true) ?: $response
    ];
}

// Test results
$results = [];

echo "Testing Course Management API Endpoints\n";
echo "======================================\n\n";

// 1. Get authentication token
echo "1. Authenticating...\n";
$response = apiRequest($baseUrl . '/auth/token', 'POST', [
    'username' => $username,
    'password' => $password
]);

if ($response['code'] === 200 && isset($response['body']['token'])) {
    $token = $response['body']['token'];
    echo $green . "✓ Authentication successful" . $reset . "\n\n";
} else {
    echo $red . "✗ Authentication failed: " . json_encode($response['body']) . $reset . "\n";
    exit(1);
}

// 2. Test Category endpoints
echo "2. Testing Category Endpoints\n";
echo "-----------------------------\n";

// Get category tree
echo "  - GET /category/tree: ";
$response = apiRequest($baseUrl . '/category/tree', 'GET', null, $token);
$results[] = ['endpoint' => 'GET /category/tree', 'success' => $response['code'] === 200];
echo ($response['code'] === 200 ? $green . "✓" : $red . "✗") . " (HTTP {$response['code']})" . $reset . "\n";

// Get specific category
echo "  - GET /category/1: ";
$response = apiRequest($baseUrl . '/category/1', 'GET', null, $token);
$results[] = ['endpoint' => 'GET /category/1', 'success' => $response['code'] === 200];
echo ($response['code'] === 200 ? $green . "✓" : $red . "✗") . " (HTTP {$response['code']})" . $reset . "\n";

// Get category courses
echo "  - GET /category/1/courses: ";
$response = apiRequest($baseUrl . '/category/1/courses?page=0&perpage=10', 'GET', null, $token);
$results[] = ['endpoint' => 'GET /category/1/courses', 'success' => $response['code'] === 200];
echo ($response['code'] === 200 ? $green . "✓" : $red . "✗") . " (HTTP {$response['code']})" . $reset . "\n";

// Create category (if has permissions)
echo "  - POST /category: ";
$response = apiRequest($baseUrl . '/category', 'POST', [
    'name' => 'Test Category ' . time(),
    'parent' => 0,
    'description' => 'Test category created by API'
], $token);
$results[] = ['endpoint' => 'POST /category', 'success' => in_array($response['code'], [201, 403])];
if ($response['code'] === 201) {
    $testCategoryId = $response['body']['id'];
    echo $green . "✓" . $reset . " (Created ID: {$testCategoryId})\n";
} else if ($response['code'] === 403) {
    echo $yellow . "⚠" . $reset . " (No permission)\n";
} else {
    echo $red . "✗" . " (HTTP {$response['code']})" . $reset . "\n";
}

echo "\n";

// 3. Test Course endpoints
echo "3. Testing Course Endpoints\n";
echo "---------------------------\n";

// Get course list
echo "  - GET /course/list: ";
$response = apiRequest($baseUrl . '/course/list?category=0&page=0&perpage=10', 'GET', null, $token);
$results[] = ['endpoint' => 'GET /course/list', 'success' => $response['code'] === 200];
echo ($response['code'] === 200 ? $green . "✓" : $red . "✗") . " (HTTP {$response['code']})" . $reset . "\n";

// Get specific course
echo "  - GET /course/2: ";
$response = apiRequest($baseUrl . '/course/2', 'GET', null, $token);
$results[] = ['endpoint' => 'GET /course/2', 'success' => in_array($response['code'], [200, 404])];
echo ($response['code'] === 200 ? $green . "✓" : ($response['code'] === 404 ? $yellow . "⚠ (Course not found)" : $red . "✗")) . " (HTTP {$response['code']})" . $reset . "\n";

// Get course teachers
echo "  - GET /course/2/teachers: ";
$response = apiRequest($baseUrl . '/course/2/teachers', 'GET', null, $token);
$results[] = ['endpoint' => 'GET /course/2/teachers', 'success' => in_array($response['code'], [200, 404])];
echo ($response['code'] === 200 ? $green . "✓" : ($response['code'] === 404 ? $yellow . "⚠ (Course not found)" : $red . "✗")) . " (HTTP {$response['code']})" . $reset . "\n";

// Create course
echo "  - POST /course: ";
$response = apiRequest($baseUrl . '/course', 'POST', [
    'fullname' => 'Test Course ' . time(),
    'shortname' => 'TEST' . time(),
    'category' => 1,
    'summary' => 'Test course created by API'
], $token);
$results[] = ['endpoint' => 'POST /course', 'success' => in_array($response['code'], [201, 403])];
if ($response['code'] === 201) {
    $testCourseId = $response['body']['id'];
    echo $green . "✓" . $reset . " (Created ID: {$testCourseId})\n";
} else if ($response['code'] === 403) {
    echo $yellow . "⚠" . $reset . " (No permission)\n";
} else {
    echo $red . "✗" . " (HTTP {$response['code']})" . $reset . "\n";
}

echo "\n";

// 4. Test Activity endpoints
echo "4. Testing Activity Endpoints\n";
echo "-----------------------------\n";

// Get activity list
echo "  - GET /activity/list?courseid=2: ";
$response = apiRequest($baseUrl . '/activity/list?courseid=2', 'GET', null, $token);
$results[] = ['endpoint' => 'GET /activity/list', 'success' => in_array($response['code'], [200, 404])];
echo ($response['code'] === 200 ? $green . "✓" : ($response['code'] === 404 ? $yellow . "⚠ (Course not found)" : $red . "✗")) . " (HTTP {$response['code']})" . $reset . "\n";

echo "\n";

// 5. Test Section endpoints
echo "5. Testing Section Endpoints\n";
echo "----------------------------\n";

// Create section (if we created a test course)
if (isset($testCourseId)) {
    echo "  - POST /section: ";
    $response = apiRequest($baseUrl . '/section', 'POST', [
        'courseid' => $testCourseId,
        'name' => 'Test Section',
        'summary' => 'Test section created by API'
    ], $token);
    $results[] = ['endpoint' => 'POST /section', 'success' => $response['code'] === 201];
    if ($response['code'] === 201) {
        $testSectionId = $response['body']['id'];
        echo $green . "✓" . $reset . " (Created ID: {$testSectionId})\n";
    } else {
        echo $red . "✗" . " (HTTP {$response['code']})" . $reset . "\n";
    }
}

echo "\n";

// Summary
echo "Summary\n";
echo "=======\n";
$total = count($results);
$successful = count(array_filter($results, function($r) { return $r['success']; }));
$percentage = round(($successful / $total) * 100);

echo "Total endpoints tested: $total\n";
echo "Successful: $successful\n";
echo "Failed: " . ($total - $successful) . "\n";
echo "Success rate: {$percentage}%\n\n";

if ($percentage === 100) {
    echo $green . "All tests passed!" . $reset . "\n";
} else if ($percentage >= 80) {
    echo $yellow . "Most tests passed, but some issues detected." . $reset . "\n";
} else {
    echo $red . "Several tests failed. Please check the implementation." . $reset . "\n";
}

// Cleanup (delete test data if created)
if (isset($testCategoryId)) {
    echo "\nCleaning up test data...\n";
    apiRequest($baseUrl . '/category/' . $testCategoryId, 'DELETE', null, $token);
}
if (isset($testCourseId)) {
    apiRequest($baseUrl . '/course/' . $testCourseId . '?confirm=true', 'DELETE', null, $token);
}

echo "\nTest completed.\n";