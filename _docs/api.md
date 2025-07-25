# Course Management API Documentation

## Overview

The Course Management API is a RESTful web service that provides programmatic access to Moodle course management features. It uses JWT (JSON Web Token) authentication and supports full CRUD operations on courses, categories, sections, and activities.

## Base URL

All API endpoints are accessed through:
```
{moodle_url}/local/courseapi/api/index.php
```

For example, if your Moodle installation is at `http://localhost:8888`, the API base URL would be:
```
http://localhost:8888/local/courseapi/api/index.php
```

## Authentication

### Obtaining a Token

Before making any API requests, you must obtain a JWT token by authenticating with your Moodle credentials.

**Endpoint:** `POST /auth/token`

**Request Body:**
```json
{
  "username": "your_username",
  "password": "your_password"
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8888/local/courseapi/api/index.php/auth/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "ADMINadmin12!"
  }'
```

**Success Response (200 OK):**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "expires_in": 3600,
  "user": {
    "id": 2,
    "username": "admin",
    "firstname": "Admin",
    "lastname": "User"
  }
}
```

**Error Responses:**
- `401 Unauthorized` - Invalid username or password
- `422 Unprocessable Entity` - Missing username or password

### Using the Token

Include the JWT token in the `Authorization` header for all subsequent requests:

```
Authorization: Bearer {your_token}
```

**Example:**
```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/user/me" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
```

### Token Expiration

- Tokens expire after 1 hour (3600 seconds) by default
- When a token expires, you'll receive a `401 Unauthorized` response
- Simply request a new token using the `/auth/token` endpoint

## User Endpoints

### Get Current User

Retrieve information about the authenticated user.

**Endpoint:** `GET /user/me`

**Headers Required:**
- `Authorization: Bearer {token}`

**Example Request:**
```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/user/me" \
  -H "Authorization: Bearer {token}"
```

**Success Response (200 OK):**
```json
{
  "id": 2,
  "username": "admin",
  "firstname": "Admin",
  "lastname": "User"
}
```

## Category Management Endpoints

### Get Category Tree
```
GET /category/tree?parent={id}&includeHidden={bool}
```
Returns the full category hierarchy starting from the specified parent (0 for top level).

**Query Parameters:**
- `parent` (optional): Parent category ID (default: 0)
- `includeHidden` (optional): Include hidden categories (default: false)

**Response:**
```json
{
  "categories": [
    {
      "id": 1,
      "name": "Miscellaneous",
      "parent": 0,
      "visible": true,
      "coursecount": 5,
      "depth": 1,
      "path": "/1",
      "children": [],
      "can_edit": true,
      "can_delete": false,
      "can_move": true,
      "can_create_course": true
    }
  ]
}
```

### Get Single Category
```
GET /category/{id}
```
Returns details for a specific category.

### Get Category Courses
```
GET /category/{id}/courses?page={n}&perpage={n}&sort={field}&direction={asc|desc}&search={query}
```
Returns paginated list of courses in a category.

**Query Parameters:**
- `page`: Page number (default: 0)
- `perpage`: Items per page (default: 20)
- `sort`: Sort field - fullname, shortname, idnumber, timecreated, timemodified, sortorder (default: fullname)
- `direction`: Sort direction - asc or desc (default: asc)
- `search`: Search query to filter courses

### Create Category
```
POST /category
{
  "name": "New Category",
  "parent": 0,
  "description": "Category description",
  "visible": true
}
```

### Update Category
```
PUT /category/{id}
{
  "name": "Updated Name",
  "description": "Updated description",
  "visible": false
}
```

### Delete Category
```
DELETE /category/{id}?recursive={bool}
```
**Query Parameters:**
- `recursive`: Delete subcategories as well (default: false)

### Toggle Category Visibility
```
POST /category/{id}/visibility
```
Toggles the visibility status of a category.

### Move Category
```
POST /category/{id}/move
{
  "direction": "up" | "down"
}
```
Moves category up or down in the sort order.

## Course Endpoints

### Get Course Management Data

Retrieve complete course structure including all sections and activities.

**Endpoint:** `GET /course/{courseId}/management_data`

**URL Parameters:**
- `courseId` (integer, required) - The Moodle course ID

**Headers Required:**
- `Authorization: Bearer {token}`

**Example Request:**
```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/2/management_data" \
  -H "Authorization: Bearer {token}"
```

**Success Response (200 OK):**
```json
{
  "course_name": "Introduction to Programming",
  "sections": [
    {
      "id": 1,
      "name": "General",
      "visible": true,
      "summary": "<p>Welcome to the course!</p>",
      "activities": [
        {
          "id": 1,
          "name": "Course Forum",
          "modname": "forum",
          "modicon": "http://localhost:8888/theme/image.php/boost/forum/icon",
          "visible": true
        },
        {
          "id": 2,
          "name": "Assignment 1",
          "modname": "assign",
          "modicon": "http://localhost:8888/theme/image.php/boost/assign/icon",
          "visible": true
        }
      ]
    },
    {
      "id": 2,
      "name": "Week 1: Introduction",
      "visible": true,
      "summary": "<p>This week covers basic concepts.</p>",
      "activities": []
    }
  ]
}
```

**Error Responses:**
- `403 Forbidden` - User doesn't have permission to manage this course
- `404 Not Found` - Course doesn't exist

### List All Courses
```
GET /course/list?category={id}&search={query}&page={n}&perpage={n}&sort={field}&direction={asc|desc}
```
Returns a filtered, paginated list of courses.

**Query Parameters:**
- `category`: Filter by category ID (0 for all)
- `search`: Search in fullname, shortname, or idnumber
- `page`: Page number (default: 0)
- `perpage`: Items per page (default: 20)
- `sort`: Sort field (default: fullname)
- `direction`: Sort direction (default: asc)

### Update Course
```
PUT /course/{id}
{
  "fullname": "Updated Course Name",
  "shortname": "UPDATED",
  "summary": "Updated summary",
  "visible": true,
  "startdate": 1704067200,
  "enddate": 1735689600
}
```

### Toggle Course Visibility
```
POST /course/{id}/visibility
```
Toggles the visibility status of a course.

### Move Course to Category
```
POST /course/{id}/move
{
  "categoryid": 5
}
```
Moves a course to a different category.

### Get Course Teachers
```
GET /course/{id}/teachers
```
Returns list of teachers and enrollment information for a course.

**Response:**
```json
{
  "teachers": [
    {
      "id": 2,
      "fullname": "John Doe",
      "email": "john@example.com",
      "role": "editingteacher",
      "picture": ""
    }
  ],
  "enrollments": [
    {
      "method": "manual",
      "name": "Manual enrolments",
      "count": 45,
      "enabled": true
    }
  ],
  "total_enrolled": 45
}
```

## Activity Endpoints

### Create Activity

Add a new activity to a course section.

**Endpoint:** `POST /activity`

**Headers Required:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
  "courseid": 2,
  "sectionid": 1,
  "modname": "assign",
  "name": "New Assignment",
  "intro": "Complete the following tasks...",
  "visible": true
}
```

**Supported Module Types (modname):**
- `assign` - Assignment
- `quiz` - Quiz
- `forum` - Forum
- `resource` - File resource
- `page` - Page
- `url` - URL
- `label` - Label

**Example Request:**
```bash
curl -X POST "http://localhost:8888/local/courseapi/api/index.php/activity" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "courseid": 2,
    "sectionid": 1,
    "modname": "assign",
    "name": "Week 2 Assignment",
    "intro": "Write a program that...",
    "visible": true
  }'
```

**Success Response (200 OK):**
```json
{
  "id": 15,
  "name": "Week 2 Assignment",
  "modname": "assign",
  "modicon": "http://localhost:8888/theme/image.php/boost/assign/icon",
  "visible": true
}
```

**Error Responses:**
- `403 Forbidden` - User lacks permission to create activities
- `404 Not Found` - Course or section doesn't exist
- `422 Unprocessable Entity` - Missing required fields
- `400 Bad Request` - Invalid module type or database error

### Update Activity

Modify an existing activity's properties.

**Endpoint:** `PUT /activity/{activityId}`

**URL Parameters:**
- `activityId` (integer, required) - The activity's course module ID

**Headers Required:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body (all fields optional):**
```json
{
  "name": "Updated Activity Name",
  "visible": false
}
```

**Example Request:**
```bash
curl -X PUT "http://localhost:8888/local/courseapi/api/index.php/activity/15" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Week 2 Assignment (Updated)",
    "visible": true
  }'
```

**Success Response (200 OK):**
```json
{
  "id": 15,
  "name": "Week 2 Assignment (Updated)",
  "modname": "assign",
  "modicon": "http://localhost:8888/theme/image.php/boost/assign/icon",
  "visible": true
}
```

**Error Responses:**
- `403 Forbidden` - User lacks permission to update this activity
- `404 Not Found` - Activity doesn't exist

### Delete Activity

Remove an activity from the course.

**Endpoint:** `DELETE /activity/{activityId}`

**URL Parameters:**
- `activityId` (integer, required) - The activity's course module ID

**Headers Required:**
- `Authorization: Bearer {token}`

**Example Request:**
```bash
curl -X DELETE "http://localhost:8888/local/courseapi/api/index.php/activity/15" \
  -H "Authorization: Bearer {token}"
```

**Success Response:** `204 No Content` (empty body)

**Error Responses:**
- `403 Forbidden` - User lacks permission to delete this activity
- `404 Not Found` - Activity doesn't exist or was already deleted

### List Course Activities
```
GET /activity/list?courseid={id}
```
Returns all activities in a course.

### Toggle Activity Visibility
```
POST /activity/{id}/visibility
```
Toggles the visibility status of an activity.

### Duplicate Activity
```
POST /activity/{id}/duplicate
```
Creates a duplicate of an activity.

**Response:**
```json
{
  "id": 123,
  "name": "Copy of Original Activity",
  "modname": "assign",
  "visible": true,
  "sectionid": 5
}
```

## Section Endpoints

### Update Section

Modify a course section's properties.

**Endpoint:** `PUT /section/{sectionId}`

**URL Parameters:**
- `sectionId` (integer, required) - The section ID

**Headers Required:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body (all fields optional):**
```json
{
  "name": "Week 2: Advanced Topics",
  "visible": true,
  "summary": "<p>This week we explore advanced concepts including...</p>"
}
```

**Example Request:**
```bash
curl -X PUT "http://localhost:8888/local/courseapi/api/index.php/section/2" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Week 2: Object-Oriented Programming",
    "visible": true,
    "summary": "<p>Introduction to OOP concepts.</p>"
  }'
```

**Success Response (200 OK):**
```json
{
  "id": 2,
  "name": "Week 2: Object-Oriented Programming",
  "visible": true,
  "summary": "<p>Introduction to OOP concepts.</p>"
}
```

**Error Responses:**
- `403 Forbidden` - User lacks permission to update course sections
- `404 Not Found` - Section doesn't exist

### Create Section
```
POST /section
{
  "courseid": 2,
  "name": "New Section",
  "summary": "Section description",
  "visible": true
}
```

### Delete Section
```
DELETE /section/{id}
```
Deletes a section (must be empty).

### Toggle Section Visibility
```
POST /section/{id}/visibility
```
Toggles the visibility status of a section.

### Reorder Activities in Section

Change the order of activities within a section.

**Endpoint:** `POST /section/{sectionId}/reorder_activities`

**URL Parameters:**
- `sectionId` (integer, required) - The section ID

**Headers Required:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
  "activity_ids": [5, 3, 4]
}
```

**Note:** The activity IDs must all belong to the specified section. The order in the array determines the new order.

**Example Request:**
```bash
curl -X POST "http://localhost:8888/local/courseapi/api/index.php/section/1/reorder_activities" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "activity_ids": [3, 1, 2]
  }'
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Activities in section 1 reordered."
}
```

**Error Responses:**
- `400 Bad Request` - Invalid activity ID or activity doesn't belong to section
- `403 Forbidden` - User lacks permission to manage activities
- `404 Not Found` - Section doesn't exist

### Move Activity to Different Section

Transfer an activity from one section to another.

**Endpoint:** `POST /section/{sectionId}/move_activity`

**URL Parameters:**
- `sectionId` (integer, required) - The target section ID

**Headers Required:**
- `Authorization: Bearer {token}`
- `Content-Type: application/json`

**Request Body:**
```json
{
  "activityid": 5,
  "position": 0
}
```

**Parameters:**
- `activityid` - The ID of the activity to move
- `position` - The position in the target section (0-based index)

**Example Request:**
```bash
curl -X POST "http://localhost:8888/local/courseapi/api/index.php/section/2/move_activity" \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "activityid": 5,
    "position": 0
  }'
```

**Success Response (200 OK):**
```json
{
  "status": "success",
  "message": "Activity moved successfully"
}
```

**Error Responses:**
- `400 Bad Request` - Activity and section are in different courses
- `403 Forbidden` - User lacks permission to manage activities
- `404 Not Found` - Activity or section doesn't exist
- `422 Unprocessable Entity` - Missing activityid

## Error Handling

All error responses follow a consistent format:

```json
{
  "error": "Error message description"
}
```

### Common HTTP Status Codes

- `200 OK` - Request succeeded
- `201 Created` - Resource created successfully
- `204 No Content` - Request succeeded with no response body (e.g., DELETE)
- `400 Bad Request` - Invalid request or parameters
- `401 Unauthorized` - Missing or invalid authentication token
- `403 Forbidden` - Authenticated but lacking required permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Missing required fields or invalid data
- `500 Internal Server Error` - Server error

### Moodle-Specific Error Codes

Some errors return Moodle error codes in the format `module/errorcode`:

- `error/invalidrecord` - Database record not found
- `error/nopermissions` - Permission denied
- `error/invalidtoken` - JWT token is invalid
- `error/tokenexpired` - JWT token has expired
- `error/errorwritingtodatabase` - Database write operation failed

## Permissions

All API operations respect Moodle's capability system:

- **Category Management**: Requires `moodle/category:manage` capability
- **Course Creation**: Requires `moodle/course:create` capability
- **Course Update**: Requires `moodle/course:update` capability
- **Course Deletion**: Requires `moodle/course:delete` capability
- **Course Visibility**: Requires `moodle/course:visibility` capability
- **Activity Management**: Requires `moodle/course:manageactivities` capability
- **Section Management**: Requires `moodle/course:update`, `moodle/course:sectionvisibility` capabilities

Users must be enrolled in the course with appropriate roles (e.g., Teacher, Manager) or be a site administrator.

## Best Practices

### 1. Token Management
- Store tokens securely in your application
- Implement token refresh before expiration
- Never expose tokens in URLs or logs

### 2. Error Handling
```javascript
async function apiRequest(url, options = {}) {
  try {
    const response = await fetch(url, {
      ...options,
      headers: {
        'Authorization': `Bearer ${getStoredToken()}`,
        'Content-Type': 'application/json',
        ...options.headers
      }
    });

    if (response.status === 401) {
      // Token expired, refresh and retry
      await refreshToken();
      return apiRequest(url, options);
    }

    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error || `HTTP ${response.status}`);
    }

    return response.status === 204 ? null : await response.json();
  } catch (error) {
    console.error('API request failed:', error);
    throw error;
  }
}
```

### 3. Batch Operations
When updating multiple items, consider the order of operations:
1. Create new activities first
2. Update existing activities
3. Reorder activities
4. Delete unwanted activities

### 4. React Integration Example
```javascript
// CourseManager.jsx
import React, { useState, useEffect } from 'react';

const API_BASE = '/local/courseapi/api/index.php';

function CourseManager({ courseId, token }) {
  const [courseData, setCourseData] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    fetchCourseData();
  }, [courseId]);

  const fetchCourseData = async () => {
    try {
      const response = await fetch(`${API_BASE}/course/${courseId}/management_data`, {
        headers: {
          'Authorization': `Bearer ${token}`
        }
      });
      
      if (!response.ok) throw new Error('Failed to fetch course data');
      
      const data = await response.json();
      setCourseData(data);
    } catch (error) {
      console.error('Error fetching course:', error);
    } finally {
      setLoading(false);
    }
  };

  const updateActivity = async (activityId, updates) => {
    try {
      const response = await fetch(`${API_BASE}/activity/${activityId}`, {
        method: 'PUT',
        headers: {
          'Authorization': `Bearer ${token}`,
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(updates)
      });

      if (!response.ok) throw new Error('Failed to update activity');
      
      // Refresh course data to show changes
      await fetchCourseData();
    } catch (error) {
      console.error('Error updating activity:', error);
    }
  };

  if (loading) return <div>Loading...</div>;
  if (!courseData) return <div>Error loading course</div>;

  return (
    <div>
      <h1>{courseData.course_name}</h1>
      {/* Render sections and activities */}
    </div>
  );
}
```

## Testing the API

### Using cURL

Set up environment variables for easier testing:
```bash
export MOODLE_URL="http://localhost:8888"
export API_BASE="$MOODLE_URL/local/courseapi/api/index.php"

# Get token
export TOKEN=$(curl -s -X POST "$API_BASE/auth/token" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"ADMINadmin12!"}' \
  | jq -r '.token')

# Test API
curl -X GET "$API_BASE/course/2/management_data" \
  -H "Authorization: Bearer $TOKEN" | jq .
```

### Using JavaScript/Fetch

```javascript
// Get token
const tokenResponse = await fetch('http://localhost:8888/local/courseapi/api/index.php/auth/token', {
  method: 'POST',
  headers: { 'Content-Type': application/json' },
  body: JSON.stringify({
    username: 'admin',
    password: 'ADMINadmin12!'
  })
});

const { token } = await tokenResponse.json();

// Use token for subsequent requests
const courseResponse = await fetch('http://localhost:8888/local/courseapi/api/index.php/course/2/management_data', {
  headers: { 'Authorization': `Bearer ${token}` }
});

const courseData = await courseResponse.json();
console.log(courseData);
```

### Testing All Endpoints

Use the provided test script to verify all endpoints:

```bash
php test_course_api.php
```

This will test all endpoints and provide a summary of which ones are working correctly.

## Troubleshooting

### Common Issues

1. **404 Not Found on all endpoints**
   - Ensure the plugin is installed correctly
   - Check that `.htaccess` file exists in `/local/courseapi/api/`
   - Verify Apache mod_rewrite is enabled

2. **401 Unauthorized errors**
   - Token may have expired (check if >1 hour old)
   - Ensure "Bearer " prefix is included in Authorization header
   - Verify token was copied correctly (no extra spaces/newlines)

3. **403 Forbidden errors**
   - User doesn't have required Moodle capabilities
   - User is not enrolled in the course
   - Check user's role in the course

4. **Activities not creating**
   - Ensure all required fields are provided
   - Check that modname is a valid, installed module type
   - Verify sectionid exists and belongs to the specified course

### Debug Mode

Enable API debugging by setting environment variable:
```bash
export API_DEBUG=true
```

This will output detailed request/response information when using the provided test framework.

## Version History

- **1.0.0** - Initial release with basic CRUD operations for activities and sections
- **1.1.0** - Changed from course-scoped to user-level JWT tokens
- **2.0.0** - Added full course and category management endpoints

## Additional Resources

- [Moodle Development Documentation](https://docs.moodle.org/dev/Main_Page)
- [JWT.io](https://jwt.io/) - JWT token decoder and information
- [Moodle Capabilities](https://docs.moodle.org/dev/Capabilities) - Understanding Moodle permissions