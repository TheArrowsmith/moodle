<?php
require_once('config.php');
require_login();

echo "<h2>Debug Course Data</h2>";

// Check categories
echo "<h3>Categories:</h3>";
$categories = $DB->get_records('course_categories', null, 'sortorder');
foreach ($categories as $cat) {
    echo "ID: {$cat->id}, Name: {$cat->name}, Parent: {$cat->parent}, Visible: {$cat->visible}<br>";
}

// Check courses
echo "<h3>Courses:</h3>";
$courses = $DB->get_records('course', null, 'category, sortorder');
foreach ($courses as $course) {
    echo "ID: {$course->id}, Name: {$course->fullname}, Short: {$course->shortname}, Category: {$course->category}, Visible: {$course->visible}<br>";
}

// Test API call
echo "<h3>API Test:</h3>";
require_once($CFG->dirroot . '/local/courseapi/classes/jwt.php');
$token = local_courseapi\jwt::create_token($USER->id);
echo "Token: " . substr($token, 0, 50) . "...<br>";

// Test category API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot . '/local/courseapi/api/index.php/category/tree');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
));
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Category API Response (HTTP $http_code):<br>";
echo "<pre>" . htmlspecialchars($result) . "</pre>";

// Test course list API
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $CFG->wwwroot . '/local/courseapi/api/index.php/course/list?category=1&page=0&perpage=20');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $token,
    'Content-Type: application/json'
));
$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Course List API Response for category 1 (HTTP $http_code):<br>";
echo "<pre>" . htmlspecialchars($result) . "</pre>";
?>