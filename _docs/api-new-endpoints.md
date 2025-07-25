# Course Management API - New Endpoints

This document describes the new endpoints added to the Course Management API to support full course management functionality.

## Base URL
```
{moodle_url}/local/courseapi/api/index.php
```

## Authentication
All endpoints require JWT authentication token in the Authorization header:
```
Authorization: Bearer {token}
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

## Course Management Endpoints

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

## Activity Management Endpoints

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

## Section Management Endpoints

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

## Error Responses

All endpoints return consistent error responses:

```json
{
  "error": "Error message description"
}
```

Common HTTP status codes:
- 200: Success
- 201: Created
- 204: No Content (successful deletion)
- 400: Bad Request
- 401: Unauthorized
- 403: Forbidden (no permission)
- 404: Not Found
- 422: Unprocessable Entity (validation error)
- 500: Internal Server Error

## Capabilities Required

Different endpoints require different Moodle capabilities:

- **Category Management**: `moodle/category:manage`
- **Course Creation**: `moodle/course:create`
- **Course Update**: `moodle/course:update`
- **Course Deletion**: `moodle/course:delete`
- **Course Visibility**: `moodle/course:visibility`
- **Activity Management**: `moodle/course:manageactivities`
- **Section Management**: `moodle/course:update`, `moodle/course:sectionvisibility`

## Testing

Use the provided test script to verify all endpoints:

```bash
php test_course_api.php
```

This will test all endpoints and provide a summary of which ones are working correctly.