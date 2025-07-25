# Course Management API Phase 2 Specification

## Overview

This specification defines three additional endpoints for the Course Management API to support basic course CRUD operations. These endpoints complement the existing activity and section management endpoints by providing course-level operations.

## Endpoints

### 1. Create Course

Create a new course in Moodle with specified properties.

**Endpoint:** `POST /course`

**Authentication:** Required (Bearer token)

**Required Capability:** `moodle/course:create`

**Request Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "fullname": "Introduction to Web Development",
  "shortname": "WEBDEV101",
  "category": 1,
  "summary": "Learn the fundamentals of HTML, CSS, and JavaScript",
  "format": "topics",
  "numsections": 10,
  "startdate": 1704067200,
  "enddate": 1719792000,
  "visible": true,
  "options": {
    "showgrades": true,
    "showreports": true,
    "maxbytes": 52428800,
    "enablecompletion": true,
    "lang": "en"
  }
}
```

**Field Descriptions:**
- `fullname` (string, required): The full display name of the course
- `shortname` (string, required): Unique short identifier (must be unique across Moodle)
- `category` (integer, required): Category ID where course will be created
- `summary` (string, optional): Course description/summary (supports HTML)
- `format` (string, optional): Course format - "topics", "weeks", "social", "singleactivity" (default: "topics")
- `numsections` (integer, optional): Number of sections to create (default: 10)
- `startdate` (integer, optional): Unix timestamp for course start (default: current time)
- `enddate` (integer, optional): Unix timestamp for course end (default: 0 = no end date)
- `visible` (boolean, optional): Whether course is visible to students (default: true)
- `options` (object, optional): Additional course settings
  - `showgrades` (boolean): Show gradebook to students (default: true)
  - `showreports` (boolean): Show activity reports (default: true)
  - `maxbytes` (integer): Maximum upload size in bytes (default: site limit)
  - `enablecompletion` (boolean): Enable completion tracking (default: true)
  - `lang` (string): Force course language (default: site default)

**Success Response (201 Created):**
```json
{
  "id": 15,
  "shortname": "WEBDEV101",
  "fullname": "Introduction to Web Development",
  "displayname": "Introduction to Web Development",
  "category": 1,
  "visible": true,
  "format": "topics",
  "startdate": 1704067200,
  "enddate": 1719792000,
  "url": "http://localhost:8888/course/view.php?id=15"
}
```

**Error Responses:**
- `400 Bad Request` - Invalid parameters or shortname already exists
  ```json
  {
    "error": "A course with shortname 'WEBDEV101' already exists"
  }
  ```
- `403 Forbidden` - User lacks course creation capability
  ```json
  {
    "error": "You do not have permission to create courses"
  }
  ```
- `404 Not Found` - Category doesn't exist
  ```json
  {
    "error": "Category with id 99 not found"
  }
  ```
- `422 Unprocessable Entity` - Missing required fields
  ```json
  {
    "error": "Missing required field: fullname"
  }
  ```

**Implementation Notes:**
1. The endpoint should validate the shortname is unique before creation
2. Should use Moodle's `create_course()` function internally
3. Default course settings should be applied from site configuration
4. Should trigger standard Moodle events for course creation
5. Should create default "General" section automatically

### 2. Delete Course

Permanently delete a course and all its content.

**Endpoint:** `DELETE /course/{id}`

**Authentication:** Required (Bearer token)

**Required Capability:** `moodle/course:delete`

**URL Parameters:**
- `id` (integer, required): The course ID to delete

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters (optional):**
- `async` (boolean): Process deletion asynchronously (default: false)
- `confirm` (boolean): Skip confirmation check (default: false)

**Example Request:**
```bash
DELETE /course/15?confirm=true
Authorization: Bearer {token}
```

**Success Response:** `204 No Content` (empty body)

**Error Responses:**
- `403 Forbidden` - User lacks permission to delete this course
  ```json
  {
    "error": "You do not have permission to delete this course"
  }
  ```
- `404 Not Found` - Course doesn't exist
  ```json
  {
    "error": "Course with id 15 not found"
  }
  ```
- `409 Conflict` - Course has active enrollments (when confirm=false)
  ```json
  {
    "error": "Course has 45 active users. Set confirm=true to force deletion",
    "active_users": 45,
    "requires_confirmation": true
  }
  ```

**Implementation Notes:**
1. Should check for active enrollments before deletion
2. Must remove all course content, grades, and user data
3. Should use Moodle's `delete_course()` function
4. For large courses, consider async deletion to avoid timeouts
5. Should trigger standard Moodle events for course deletion
6. Should clean up all related data (backups, logs, etc.)

### 3. Get Course Details

Retrieve basic course information without full activity/section data.

**Endpoint:** `GET /course/{id}`

**Authentication:** Required (Bearer token)

**Required Capability:** User must be enrolled in course OR have `moodle/course:view` capability

**URL Parameters:**
- `id` (integer, required): The course ID

**Request Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters (optional):**
- `include` (string): Comma-separated list of additional data to include
  - Options: `enrollmentmethods`, `categories`, `users`, `completion`
- `userinfo` (boolean): Include current user's enrollment info (default: true)

**Example Request:**
```bash
GET /course/2?include=enrollmentmethods,completion&userinfo=true
Authorization: Bearer {token}
```

**Success Response (200 OK):**
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
      "name": "Self enrollment",
      "password_required": false,
      "enrollment_key": ""
    }
  ],
  "completion": {
    "enabled": true,
    "criteria_count": 15,
    "user_completed": 10,
    "user_completion_percentage": 67
  }
}
```

**Response Fields:**
- Core fields always included:
  - `id`, `shortname`, `fullname`, `displayname`
  - `summary`, `summaryformat`, `format`
  - `startdate`, `enddate`, `visible`
  - `category` (object with id, name, path)
  - `timecreated`, `timemodified`
  - `url` (direct link to course)
  - `enrollmentcount`, `sectioncount`, `activitycount`
  - `completionenabled`

- `user_enrollment` (when userinfo=true):
  - `enrolled`: Whether current user is enrolled
  - `roles`: Array of user's roles in course
  - `timeenrolled`: Unix timestamp of enrollment
  - `progress`: Completion percentage (0-100)
  - `lastaccess`: Unix timestamp of last course access

- `enrollment_methods` (when include contains "enrollmentmethods"):
  - Array of available enrollment methods with configuration

- `completion` (when include contains "completion"):
  - Completion tracking information and user progress

**Error Responses:**
- `403 Forbidden` - User cannot view this course
  ```json
  {
    "error": "You do not have permission to view this course"
  }
  ```
- `404 Not Found` - Course doesn't exist
  ```json
  {
    "error": "Course with id 99 not found"
  }
  ```

**Implementation Notes:**
1. This endpoint provides lightweight course data compared to `/management_data`
2. Should check user's enrollment or view capability
3. Additional includes should only be processed if user has permission
4. User-specific data should be calculated for the authenticated user
5. Consider caching frequently accessed course details

## Security Considerations

1. **Course Creation:**
   - Validate all input thoroughly
   - Check category permissions
   - Enforce site-wide course creation limits
   - Log all course creations for auditing

2. **Course Deletion:**
   - Require explicit confirmation for courses with users
   - Consider soft-delete with recovery period
   - Log deletions with full course backup reference
   - Prevent deletion of site home course

3. **Course Details:**
   - Filter sensitive information based on user role
   - Hide enrollment keys from unauthorized users
   - Respect course visibility settings
   - Cache responses appropriately

## Performance Considerations

1. **Creation:** 
   - Course creation can be resource-intensive
   - Consider queueing for bulk operations
   - Limit concurrent course creations per user

2. **Deletion:**
   - Large courses should use async deletion
   - Implement progress tracking for async operations
   - Clean up in batches to avoid memory issues

3. **Details:**
   - Cache course details aggressively
   - Use database indexes on commonly queried fields
   - Lazy-load optional includes

## Testing Guidelines

### Test Cases for Course Creation:
1. Create course with minimum required fields
2. Create course with all optional fields
3. Attempt duplicate shortname (should fail)
4. Create course in non-existent category (should fail)
5. Create course without permission (should fail)
6. Validate special characters in course names
7. Test course format variations

### Test Cases for Course Deletion:
1. Delete empty course
2. Delete course with enrollments (with/without confirmation)
3. Delete non-existent course (should fail)
4. Delete without permission (should fail)
5. Test async deletion for large course
6. Verify complete data cleanup

### Test Cases for Course Details:
1. Get details as enrolled student
2. Get details as teacher
3. Get details as admin
4. Get details without enrollment (should fail unless admin)
5. Test all include combinations
6. Verify hidden courses respect visibility

## Example Integration

```javascript
// React component example
const CourseManager = {
  async createCourse(courseData) {
    const response = await fetch('/local/courseapi/api/index.php/course', {
      method: 'POST',
      headers: {
        'Authorization': `Bearer ${this.token}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(courseData)
    });
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error);
    }
    
    return await response.json();
  },
  
  async deleteCourse(courseId, confirm = false) {
    const response = await fetch(
      `/local/courseapi/api/index.php/course/${courseId}?confirm=${confirm}`,
      {
        method: 'DELETE',
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      }
    );
    
    if (response.status === 409) {
      const conflict = await response.json();
      // Handle confirmation requirement
      if (conflict.requires_confirmation) {
        return { requiresConfirmation: true, ...conflict };
      }
    }
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error);
    }
    
    return { success: true };
  },
  
  async getCourseDetails(courseId, includes = []) {
    const includeParam = includes.length ? `?include=${includes.join(',')}` : '';
    const response = await fetch(
      `/local/courseapi/api/index.php/course/${courseId}${includeParam}`,
      {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      }
    );
    
    if (!response.ok) {
      const error = await response.json();
      throw new Error(error.error);
    }
    
    return await response.json();
  }
};
```

## Migration Notes

For existing Moodle installations:
1. These endpoints supplement but don't replace existing Moodle interfaces
2. Ensure proper capability mappings for API access
3. Consider rate limiting for course creation
4. Monitor API usage for capacity planning
5. Plan for gradual migration of frontend to use these APIs