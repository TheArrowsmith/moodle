# Course Management API Phase 2 - Test Scenarios

## Overview

This document provides comprehensive test scenarios for all three Phase 2 endpoints. Each scenario includes setup, execution steps, and expected results.

## Test Environment Setup

```bash
# Required test data
- Test users with different roles (admin, teacher, student)
- Test categories with different permission levels
- Existing courses for update/delete operations
- JWT tokens for each test user
```

## 1. POST /course - Create Course Tests

### Test 1.1: Create Course with Minimum Required Fields

**Setup:**
- User with course:create capability
- Valid category ID

**Request:**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Minimum Test Course",
    "shortname": "MIN101",
    "category": 1
  }'
```

**Expected Response (201):**
```json
{
  "id": 15,
  "shortname": "MIN101",
  "fullname": "Minimum Test Course",
  "displayname": "Minimum Test Course",
  "category": 1,
  "visible": true,
  "format": "topics",
  "startdate": 1704326400,
  "enddate": 0,
  "url": "http://localhost:8888/course/view.php?id=15"
}
```

### Test 1.2: Create Course with All Optional Fields

**Request:**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Complete Test Course",
    "shortname": "COMP101",
    "category": 1,
    "summary": "<p>This course covers all aspects of web development including HTML, CSS, JavaScript, and modern frameworks.</p>",
    "format": "weeks",
    "numsections": 16,
    "startdate": 1704326400,
    "enddate": 1719792000,
    "visible": true,
    "options": {
      "showgrades": true,
      "showreports": false,
      "maxbytes": 10485760,
      "enablecompletion": true,
      "lang": "en"
    }
  }'
```

### Test 1.3: Duplicate Shortname (Error Case)

**Setup:**
- Course with shortname "DUP101" already exists

**Request:**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Duplicate Course",
    "shortname": "DUP101",
    "category": 1
  }'
```

**Expected Response (400):**
```json
{
  "error": "A course with shortname 'DUP101' already exists"
}
```

### Test 1.4: Invalid Category (Error Case)

**Request:**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Invalid Category Course",
    "shortname": "INVCAT101",
    "category": 9999
  }'
```

**Expected Response (404):**
```json
{
  "error": "Category with id 9999 not found"
}
```

### Test 1.5: No Permission (Error Case)

**Setup:**
- User without course:create capability

**Request:**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {student_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Unauthorized Course",
    "shortname": "UNAUTH101",
    "category": 1
  }'
```

**Expected Response (403):**
```json
{
  "error": "You do not have permission to create courses"
}
```

### Test 1.6: Missing Required Fields (Error Case)

**Request:**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "shortname": "MISSING101",
    "category": 1
  }'
```

**Expected Response (422):**
```json
{
  "error": "Missing required field: fullname"
}
```

### Test 1.7: Special Characters in Course Name

**Request:**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Advanced C++ & Data Structures (2025)",
    "shortname": "CPP-DS-2025",
    "category": 1,
    "summary": "<p>Learn C++ with <strong>emphasis</strong> on STL & algorithms</p>"
  }'
```

## 2. DELETE /course/{id} - Delete Course Tests

### Test 2.1: Delete Empty Course

**Setup:**
- Course ID 20 with no enrollments

**Request:**
```bash
curl -X DELETE http://localhost:8888/local/courseapi/api/index.php/course/20 \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (204):**
```
(No content)
```

### Test 2.2: Delete Course with Enrollments (No Confirmation)

**Setup:**
- Course ID 21 with 45 active users

**Request:**
```bash
curl -X DELETE http://localhost:8888/local/courseapi/api/index.php/course/21 \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (409):**
```json
{
  "error": "Course has 45 active users. Set confirm=true to force deletion",
  "active_users": 45,
  "requires_confirmation": true
}
```

### Test 2.3: Delete Course with Enrollments (With Confirmation)

**Request:**
```bash
curl -X DELETE "http://localhost:8888/local/courseapi/api/index.php/course/21?confirm=true" \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (204):**
```
(No content)
```

### Test 2.4: Async Deletion

**Setup:**
- Large course ID 22 with lots of content

**Request:**
```bash
curl -X DELETE "http://localhost:8888/local/courseapi/api/index.php/course/22?async=true" \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (202):**
```json
{
  "status": "queued",
  "message": "Course deletion queued for processing"
}
```

### Test 2.5: Delete Non-Existent Course

**Request:**
```bash
curl -X DELETE http://localhost:8888/local/courseapi/api/index.php/course/9999 \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (404):**
```json
{
  "error": "Course with id 9999 not found"
}
```

### Test 2.6: Delete Without Permission

**Setup:**
- User without course:delete capability

**Request:**
```bash
curl -X DELETE http://localhost:8888/local/courseapi/api/index.php/course/23 \
  -H "Authorization: Bearer {teacher_token}"
```

**Expected Response (403):**
```json
{
  "error": "You do not have permission to delete this course"
}
```

### Test 2.7: Delete Site Course (Error Case)

**Setup:**
- Site course ID is typically 1

**Request:**
```bash
curl -X DELETE http://localhost:8888/local/courseapi/api/index.php/course/1 \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (400):**
```json
{
  "error": "Cannot delete the site course"
}
```

## 3. GET /course/{id} - Get Course Details Tests

### Test 3.1: Basic Details as Enrolled Student

**Setup:**
- Student enrolled in course ID 2

**Request:**
```bash
curl -X GET http://localhost:8888/local/courseapi/api/index.php/course/2 \
  -H "Authorization: Bearer {student_token}"
```

**Expected Response (200):**
```json
{
  "id": 2,
  "shortname": "PROG101",
  "fullname": "Introduction to Programming",
  "displayname": "Introduction to Programming",
  "summary": "<p>Learn programming fundamentals using Python...</p>",
  "summaryformat": 1,
  "format": "topics",
  "startdate": 1704067200,
  "enddate": 0,
  "visible": true,
  "category": {
    "id": 1,
    "name": "Miscellaneous",
    "path": "/1"
  },
  "timecreated": 1703980800,
  "timemodified": 1704153600,
  "url": "http://localhost:8888/course/view.php?id=2",
  "enrollmentcount": 156,
  "sectioncount": 12,
  "activitycount": 48,
  "completionenabled": true,
  "user_enrollment": {
    "enrolled": true,
    "roles": ["student"],
    "timeenrolled": 1704240000,
    "progress": 67,
    "lastaccess": 1704326400
  }
}
```

### Test 3.2: Details with All Includes

**Request:**
```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/2?include=enrollmentmethods,completion&userinfo=true" \
  -H "Authorization: Bearer {teacher_token}"
```

**Expected Response (200):**
```json
{
  "id": 2,
  "shortname": "PROG101",
  "fullname": "Introduction to Programming",
  "displayname": "Introduction to Programming",
  "summary": "<p>Learn programming fundamentals using Python...</p>",
  "summaryformat": 1,
  "format": "topics",
  "startdate": 1704067200,
  "enddate": 0,
  "visible": true,
  "category": {
    "id": 1,
    "name": "Miscellaneous",
    "path": "/1"
  },
  "timecreated": 1703980800,
  "timemodified": 1704153600,
  "url": "http://localhost:8888/course/view.php?id=2",
  "enrollmentcount": 156,
  "sectioncount": 12,
  "activitycount": 48,
  "completionenabled": true,
  "user_enrollment": {
    "enrolled": true,
    "roles": ["editingteacher"],
    "timeenrolled": 1703980800,
    "progress": 100,
    "lastaccess": 1704326400
  },
  "enrollment_methods": [
    {
      "type": "manual",
      "enabled": true,
      "name": "Manual enrollments"
    },
    {
      "type": "self",
      "enabled": true,
      "name": "Self enrollment (Student)",
      "password_required": false,
      "enrollment_key": ""
    }
  ],
  "completion": {
    "enabled": true,
    "criteria_count": 15,
    "user_completed": 15,
    "user_completion_percentage": 100
  }
}
```

### Test 3.3: Details as Admin (Not Enrolled)

**Setup:**
- Admin not enrolled in course

**Request:**
```bash
curl -X GET http://localhost:8888/local/courseapi/api/index.php/course/3 \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (200):**
```json
{
  "id": 3,
  "shortname": "ADV-WEB",
  "fullname": "Advanced Web Development",
  "user_enrollment": {
    "enrolled": false,
    "roles": [],
    "timeenrolled": 0,
    "progress": 0,
    "lastaccess": 0
  }
  // ... other course details
}
```

### Test 3.4: Details Without Enrollment or Permission

**Setup:**
- User not enrolled and without view capability

**Request:**
```bash
curl -X GET http://localhost:8888/local/courseapi/api/index.php/course/4 \
  -H "Authorization: Bearer {student_token}"
```

**Expected Response (403):**
```json
{
  "error": "You do not have permission to view this course"
}
```

### Test 3.5: Non-Existent Course

**Request:**
```bash
curl -X GET http://localhost:8888/local/courseapi/api/index.php/course/9999 \
  -H "Authorization: Bearer {admin_token}"
```

**Expected Response (404):**
```json
{
  "error": "Course with id 9999 not found"
}
```

### Test 3.6: Hidden Course Visibility

**Setup:**
- Course ID 5 with visible=false
- Teacher enrolled, student not enrolled

**Teacher Request:**
```bash
curl -X GET http://localhost:8888/local/courseapi/api/index.php/course/5 \
  -H "Authorization: Bearer {teacher_token}"
```
**Expected: 200 OK with course details**

**Student Request:**
```bash
curl -X GET http://localhost:8888/local/courseapi/api/index.php/course/5 \
  -H "Authorization: Bearer {student_token}"
```
**Expected: 403 Forbidden**

### Test 3.7: Without User Info

**Request:**
```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/2?userinfo=false" \
  -H "Authorization: Bearer {student_token}"
```

**Expected Response (200):**
```json
{
  "id": 2,
  "shortname": "PROG101",
  "fullname": "Introduction to Programming",
  // ... other fields but NO user_enrollment field
}
```

## Performance Tests

### Test P1: Bulk Course Creation

**Scenario:** Create 100 courses in rapid succession to test rate limiting

```bash
for i in {1..100}; do
  curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
    -H "Authorization: Bearer {admin_token}" \
    -H "Content-Type: application/json" \
    -d "{
      \"fullname\": \"Performance Test Course $i\",
      \"shortname\": \"PERF$i\",
      \"category\": 1
    }" &
done
```

**Expected:** Rate limiting kicks in after X requests

### Test P2: Large Course Deletion

**Setup:** Course with 1000+ users and 500+ activities

```bash
time curl -X DELETE "http://localhost:8888/local/courseapi/api/index.php/course/100?confirm=true&async=true" \
  -H "Authorization: Bearer {admin_token}"
```

**Expected:** Async queue response within 2 seconds

### Test P3: Concurrent Course Details

**Scenario:** 50 concurrent requests for course details

```bash
for i in {1..50}; do
  curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/2?include=enrollmentmethods,completion" \
    -H "Authorization: Bearer {token_$i}" &
done
```

**Expected:** All responses within 5 seconds

## Edge Cases

### Test E1: Maximum Field Lengths

```bash
# 255 character shortname
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Maximum Length Test",
    "shortname": "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA",
    "category": 1
  }'
```

### Test E2: Unicode and Special Characters

```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Unicode Test 测试 🎓 Course",
    "shortname": "UNI-测试-2025",
    "category": 1,
    "summary": "<p>Testing émojis 🚀 and spëcial çharacters ñ</p>"
  }'
```

### Test E3: Null and Empty Values

```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "",
    "shortname": null,
    "category": 0
  }'
```

## Security Tests

### Test S1: SQL Injection Attempts

```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Test'; DROP TABLE mdl_course; --",
    "shortname": "SQL-INJ",
    "category": 1
  }'
```

### Test S2: XSS Attempts

```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "XSS Test",
    "shortname": "XSS101",
    "category": 1,
    "summary": "<script>alert(\"XSS\")</script><img src=x onerror=alert(1)>"
  }'
```

### Test S3: Path Traversal

```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/../../../config.php" \
  -H "Authorization: Bearer {admin_token}"
```

## Integration Test Workflow

### Complete Course Lifecycle Test

1. **Create a course**
```bash
COURSE_ID=$(curl -s -X POST http://localhost:8888/local/courseapi/api/index.php/course \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "fullname": "Lifecycle Test Course",
    "shortname": "LIFE101",
    "category": 1
  }' | jq -r '.id')
```

2. **Get course details**
```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/$COURSE_ID" \
  -H "Authorization: Bearer {admin_token}"
```

3. **Add activities (using existing endpoints)**
```bash
curl -X POST http://localhost:8888/local/courseapi/api/index.php/activity \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d "{
    \"courseid\": $COURSE_ID,
    \"sectionid\": 1,
    \"modname\": \"forum\",
    \"name\": \"General Discussion\"
  }"
```

4. **Delete the course**
```bash
curl -X DELETE "http://localhost:8888/local/courseapi/api/index.php/course/$COURSE_ID?confirm=true" \
  -H "Authorization: Bearer {admin_token}"
```

## Automated Test Script

```bash
#!/bin/bash
# test-phase2-api.sh

BASE_URL="http://localhost:8888/local/courseapi/api/index.php"
ADMIN_TOKEN="your-admin-token"
TEACHER_TOKEN="your-teacher-token"
STUDENT_TOKEN="your-student-token"

# Color codes
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m'

# Test counter
PASSED=0
FAILED=0

# Test function
run_test() {
    local test_name=$1
    local expected_code=$2
    local actual_code=$3
    
    if [ "$expected_code" == "$actual_code" ]; then
        echo -e "${GREEN}✓ $test_name${NC}"
        ((PASSED++))
    else
        echo -e "${RED}✗ $test_name (Expected: $expected_code, Got: $actual_code)${NC}"
        ((FAILED++))
    fi
}

# Run all tests
echo "Running Phase 2 API Tests..."

# Test 1.1: Create with minimum fields
response=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/course" \
    -H "Authorization: Bearer $ADMIN_TOKEN" \
    -H "Content-Type: application/json" \
    -d '{
        "fullname": "Test Course",
        "shortname": "TEST'$(date +%s)'",
        "category": 1
    }')
http_code=$(echo "$response" | tail -n 1)
run_test "Create course with minimum fields" "201" "$http_code"

# ... add more tests

echo -e "\n${GREEN}Passed: $PASSED${NC}"
echo -e "${RED}Failed: $FAILED${NC}"

exit $FAILED
```