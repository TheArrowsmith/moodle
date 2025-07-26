# JSON API for Moodle 3.5

## The Problem

The original Moodle 3.5 system was built entirely with server-side PHP rendering and form submissions. This architecture makes it difficult to:
- Build modern, reactive user interfaces
- Create mobile applications
- Integrate with external systems
- Develop single-page applications
- Provide real-time updates without page refreshes

For a modern programming education platform, instructors and students expect responsive interfaces that work seamlessly across devices and enable rapid interactions without constant page reloads.

## The New Feature

This feature adds a comprehensive RESTful JSON API to Moodle 3.5, enabling modern application development. The API provides:

- **JWT Authentication**: Secure, stateless authentication using JSON Web Tokens
- **RESTful Design**: Standard HTTP methods (GET, POST, PUT, DELETE) with JSON payloads
- **Comprehensive Coverage**: 35+ endpoints covering courses, categories, activities, sections, and users
- **React-Ready**: Designed specifically to support modern JavaScript frameworks
- **Permission-Aware**: Respects Moodle's existing role and capability system

### Key Capabilities:

- **Course Management**: Create, update, delete, and organize courses
- **Content Management**: Add, modify, and reorder activities and resources
- **Category Organization**: Full hierarchy management with drag-and-drop support
- **User Information**: Access authenticated user details and permissions
- **Bulk Operations**: Efficient endpoints for complex operations like reordering

## Implementation Notes

The API was implemented as a Moodle local plugin (`local/courseapi`) to ensure clean integration:

### Architecture

- **Plugin-Based**: Implemented as `local/courseapi` for easy installation and updates
- **Single Entry Point**: All requests route through `/local/courseapi/api/index.php`
- **JWT Tokens**: Stateless authentication with 1-hour expiration
- **Permission Integration**: Uses Moodle's capability system for authorization
- **Clean URLs**: RESTful paths like `/course/2/management_data`

### Technical Details

- Custom request router handles all HTTP methods
- JWT tokens include user ID and expiration timestamp
- Responses use consistent JSON structure with proper HTTP status codes
- Error messages are human-readable and actionable
- CORS headers support cross-origin requests for development

## How to Test

### 1. Get Authentication Token
First, obtain a JWT token using your Moodle credentials:

```bash
curl -X POST "http://localhost:8888/local/courseapi/api/index.php/auth/token" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"ADMINadmin12!"}'
```

You'll receive a response with your token:
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

### 2. Test Basic API Access
Use the token to fetch your user information:

```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/user/me" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 3. Fetch Course Data
Get the complete structure of a course (sections and activities):

```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/2/management_data" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

This returns the full course structure used by the React interface.

### 4. Create a New Activity
Add a new assignment to a course section:

```bash
curl -X POST "http://localhost:8888/local/courseapi/api/index.php/activity" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "courseid": 2,
    "sectionid": 1,
    "modname": "assign",
    "name": "API Test Assignment",
    "intro": "Created via the JSON API",
    "visible": true
  }'
```

### 5. Update Section Information
Modify a section's title and visibility:

```bash
curl -X PUT "http://localhost:8888/local/courseapi/api/index.php/section/1" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Section Title",
    "visible": true
  }'
```

### 6. List All Courses
Get a paginated list of courses with search:

```bash
curl -X GET "http://localhost:8888/local/courseapi/api/index.php/course/list?search=programming&page=0&perpage=10" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Quick Test Checklist

- [ ] Token generation works with valid credentials
- [ ] Token is rejected with invalid credentials
- [ ] API returns 401 without token
- [ ] Can fetch course structure
- [ ] Can create new activities
- [ ] Can update existing content
- [ ] Permissions are enforced (try as student vs teacher)
- [ ] Error messages are clear when operations fail

### Testing with React Interface

The Course Management React component (`/course/management.php`) uses this API extensively:
1. Navigate to any course as a teacher
2. Click "Course Management" in the administration block
3. Try dragging activities between sections
4. Edit activity names inline
5. Toggle visibility of sections/activities

All these operations use the JSON API behind the scenes.

### Browser Testing

You can also test the API using browser developer tools:
1. Open any Moodle page while logged in
2. Open browser console (F12)
3. Run: `fetch('/local/courseapi/api/index.php/user/me').then(r => r.json()).then(console.log)`
4. Note: Browser requests work because the API accepts session authentication as fallback

### Common Issues

- **No token in response**: Check username/password are correct
- **404 errors**: Ensure the plugin is installed and URL is correct
- **403 errors**: User lacks required permissions for the operation
- **500 errors**: Check PHP error logs for detailed error messages

The API provides a modern foundation for building responsive interfaces and integrations with Moodle, enabling the creation of enhanced learning experiences for programming education.