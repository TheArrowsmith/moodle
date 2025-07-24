# Course Management API for Moodle

A RESTful API plugin for Moodle that provides course management capabilities with JWT authentication.

## Features

- JWT-based authentication
- RESTful API endpoints for course management
- Support for managing activities, sections, and course content
- Full CRUD operations for course elements
- Clean URL structure with JSON request/response format

## Installation

1. Copy the `courseapi` folder to `/local/courseapi` in your Moodle installation
2. Visit Site Administration > Notifications to install the plugin
3. The API will be available at `{your-moodle-url}/local/courseapi/api`

## API Endpoints

### Authentication

#### POST /auth/token
Generate a JWT token for API authentication.

**Request:**
```json
{
    "username": "teacher1",
    "password": "Teacher123!",
    "course_id": 2  // Optional
}
```

**Response:**
```json
{
    "token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
    "expires_in": 3600,
    "user": {
        "id": 3,
        "username": "teacher1",
        "firstname": "Test",
        "lastname": "Teacher"
    }
}
```

### User Information

#### GET /user/me
Get information about the authenticated user.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "id": 3,
    "username": "teacher1",
    "firstname": "Test",
    "lastname": "Teacher"
}
```

### Course Management

#### GET /course/{courseId}/management_data
Fetch the entire course structure including sections and activities.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
    "course_name": "Introduction to Programming",
    "sections": [
        {
            "id": 25,
            "name": "Week 1: Introduction",
            "visible": true,
            "summary": "<p>This week we cover the basics.</p>",
            "activities": [
                {
                    "id": 101,
                    "name": "Week 1 Assignment",
                    "modname": "assign",
                    "modicon": "https://.../assign.svg",
                    "visible": true
                }
            ]
        }
    ]
}
```

### Activity Management

#### PUT /activity/{activityId}
Update activity properties.

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
    "name": "Updated Assignment Name",
    "visible": false
}
```

#### DELETE /activity/{activityId}
Delete an activity from the course.

**Headers:**
```
Authorization: Bearer {token}
```

#### POST /activity
Create a new activity.

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
    "courseid": 2,
    "sectionid": 1,
    "modname": "assign",
    "name": "New Assignment",
    "intro": "Assignment description",
    "visible": true
}
```

### Section Management

#### PUT /section/{sectionId}
Update section properties.

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
    "name": "Updated Section Title",
    "visible": false,
    "summary": "<p>New summary content.</p>"
}
```

#### POST /section/{sectionId}/reorder_activities
Reorder activities within a section.

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
    "activity_ids": [103, 101, 102]
}
```

#### POST /section/{sectionId}/move_activity
Move an activity to a different section.

**Headers:**
```
Authorization: Bearer {token}
```

**Request:**
```json
{
    "activityid": 101,
    "position": 0
}
```

## Error Handling

All error responses follow this format:

```json
{
    "error": "A human-readable error message."
}
```

Common HTTP status codes:
- `401 Unauthorized` - Invalid or missing JWT token
- `403 Forbidden` - User lacks required permissions
- `404 Not Found` - Resource not found
- `422 Unprocessable Entity` - Invalid request data

## Testing

A test script is included to verify the API functionality:

```bash
php local/courseapi/tests/test_api.php
```

## React Integration

To use this API with React:

```javascript
// Get token from data attribute
const mountPoint = document.getElementById('course-management-app');
const token = mountPoint.dataset.token;
const apiBase = mountPoint.dataset.apiBase || '/local/courseapi/api';

// Make API request
fetch(`${apiBase}/course/2/management_data`, {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Content-Type': 'application/json'
    }
})
.then(response => response.json())
.then(data => console.log(data));
```

## Security

- All endpoints require JWT authentication (except /auth/token)
- Tokens expire after 1 hour by default
- All actions are subject to Moodle's capability checks
- CORS headers are configured for cross-origin requests

## Requirements

- Moodle 3.7 or higher
- PHP 7.2 or higher
- Apache with mod_rewrite enabled (for clean URLs)

## License

GPL v3 or later