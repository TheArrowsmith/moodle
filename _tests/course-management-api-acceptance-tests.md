# Course Management API - Manual Acceptance Tests

## Prerequisites
- Moodle 3.5 or higher installation with admin access
- A test course with at least 3 sections and 5 activities
- Teacher/Admin role in the test course
- Terminal/command line with `curl` installed
- `jq` tool for JSON parsing (optional but recommended: `brew install jq` or `apt-get install jq`)

## Test Setup
1. Install the local_courseapi plugin
2. Navigate to Site Administration > Notifications
3. Complete the plugin installation (ensure dependencies check passes)
4. Note your test course ID (visible in URL when viewing course)

## Environment Variables Setup
Run these commands to set up your test environment:
```bash
export MOODLE_URL="http://localhost:8888"
export API_BASE="$MOODLE_URL/local/courseapi/api/index.php"
export COURSE_ID=2  # Replace with your test course ID
```

## Test 1: POST /auth/token - Generate API Token
**Objective**: Generate JWT token via API endpoint

### Command:
```bash
curl -X POST "$API_BASE/auth/token" \
  -H "Content-Type: application/json" \
  -d '{
    "username": "admin",
    "password": "ADMINadmin12!"
  }'
```

### Save token for subsequent tests:
```bash
# If you have jq installed:
export TOKEN=$(curl -s -X POST "$API_BASE/auth/token" \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"ADMINadmin12!"}' \
  | jq -r '.token')

# Verify token was saved:
echo $TOKEN
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Response contains JWT token:
  ```json
  {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
  }
  ```
- [ ] Token is saved in $TOKEN variable

## Test 2: GET /user/me Endpoint
**Objective**: Verify user authentication endpoint works correctly

### Command:
```bash
curl -X GET "$API_BASE/user/me" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### Pretty print with jq:
```bash
curl -s -X GET "$API_BASE/user/me" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq .
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Response contains your user data:
  ```json
  {
    "id": 2,
    "username": "admin",
    "firstname": "Admin",
    "lastname": "User"
  }
  ```
- [ ] Data matches your Moodle user profile

### Error Testing:
```bash
# Test without Authorization header:
curl -X GET "$API_BASE/user/me" \
  -H "Accept: application/json"
```
- [ ] Response status: 401 Unauthorized
- [ ] Error message: "Authentication token is missing"

```bash
# Test with invalid token:
curl -X GET "$API_BASE/user/me" \
  -H "Authorization: Bearer invalid_token_here" \
  -H "Accept: application/json"
```
- [ ] Response status: 401 Unauthorized
- [ ] Error message: "Invalid or expired authentication token"

## Test 3: GET /course/{courseId}/management_data
**Objective**: Verify course structure retrieval

### Command:
```bash
curl -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```

### Pretty print and save to file:
```bash
curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq . > course_data.json

# View the saved data:
cat course_data.json
```

### Extract activity IDs for later tests:
```bash
# Get first activity ID:
export ACTIVITY_ID=$(curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq -r '.sections[0].activities[0].id')

echo "First activity ID: $ACTIVITY_ID"

# Get first section ID:
export SECTION_ID=$(curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq -r '.sections[0].id')

echo "First section ID: $SECTION_ID"
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Response contains course name
- [ ] All course sections are listed
- [ ] Each section contains its activities
- [ ] Activity data includes: id, name, modname, modicon, visible
- [ ] Section data includes: id, name, visible, summary, activities array

### Error Testing:
```bash
# Test non-existent course:
curl -X GET "$API_BASE/course/99999/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json"
```
- [ ] Response status: 404 Not Found
- [ ] Appropriate error message

## Test 4: Update Activity
**Objective**: Test activity property modifications

### Command:
```bash
curl -X PUT "$API_BASE/activity/$ACTIVITY_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Updated Activity Name",
    "visible": false
  }'
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Response shows updated activity data
- [ ] Refresh course page in Moodle - activity name changed
- [ ] Activity is now hidden (grayed out)

### Partial Update Tests:
```bash
# Update only visibility:
curl -X PUT "$API_BASE/activity/$ACTIVITY_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"visible": true}'
```
- [ ] Only visibility changes, name remains same

```bash
# Update only name:
curl -X PUT "$API_BASE/activity/$ACTIVITY_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "Another Name via API"}'
```
- [ ] Only name changes, visibility remains same

## Test 5: Update Section
**Objective**: Test section property modifications

### Command:
```bash
curl -X PUT "$API_BASE/section/$SECTION_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Week 1: Updated Topic",
    "visible": false,
    "summary": "<p>This is the updated summary.</p>"
  }'
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Section data returned (without activities array)
- [ ] Refresh course - section name, visibility, and summary updated

## Test 6: Reorder Activities
**Objective**: Test drag-and-drop functionality via API

### Get activities in a section:
```bash
# Find a section with multiple activities:
curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq '.sections[] | select(.activities | length > 2)'

# Extract activity IDs from a section (adjust section index as needed):
export ACTIVITY_IDS=$(curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq -r '.sections[0].activities[].id' | tr '\n' ',' | sed 's/,$//')

echo "Activity IDs: $ACTIVITY_IDS"
```

### Reorder activities (reverse order example):
```bash
# Assuming you have activities 101,102,103, reverse them:
curl -X POST "$API_BASE/section/$SECTION_ID/reorder_activities" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "activity_ids": [103, 102, 101]
  }'
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Success message returned
- [ ] Refresh course - activities appear in new order

### Error Testing:
```bash
# Test with invalid activity ID:
curl -X POST "$API_BASE/section/$SECTION_ID/reorder_activities" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"activity_ids": [99999, 102, 101]}'
```
- [ ] Response status: 400 Bad Request
- [ ] Error indicates invalid activity

## Test 7: Delete Activity
**Objective**: Test activity deletion

### Create a test activity first:
```bash
# Create an activity to delete:
export DELETE_ACTIVITY_ID=$(curl -s -X POST "$API_BASE/activity" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "courseid": '$COURSE_ID',
    "sectionid": '$SECTION_ID',
    "modname": "label",
    "name": "Activity to Delete",
    "intro": "This will be deleted",
    "visible": true
  }' | jq -r '.id')

echo "Created activity to delete: $DELETE_ACTIVITY_ID"
```

### Delete the activity:
```bash
curl -X DELETE "$API_BASE/activity/$DELETE_ACTIVITY_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -v
```

### Expected Results:
- [ ] Response status: 204 No Content
- [ ] No response body
- [ ] Refresh course - activity is gone
- [ ] Activity cannot be accessed directly

### Error Testing:
```bash
# Try to delete same activity again:
curl -X DELETE "$API_BASE/activity/$DELETE_ACTIVITY_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -v
```
- [ ] Response status: 404 Not Found
- [ ] Error indicates activity doesn't exist

## Test 8: Move Activity Between Sections
**Objective**: Test moving activities to different sections

### Get a different section ID:
```bash
# Get the second section ID:
export TARGET_SECTION_ID=$(curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | jq -r '.sections[1].id')

echo "Target section ID: $TARGET_SECTION_ID"
```

### Move activity to different section:
```bash
curl -X POST "$API_BASE/section/$TARGET_SECTION_ID/move_activity" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "activityid": '$ACTIVITY_ID',
    "position": 0
  }'
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Success message returned
- [ ] Activity now appears in target section
- [ ] Activity removed from original section

## Test 9: Create New Activity
**Objective**: Test activity creation via API

### Create an assignment:
```bash
curl -X POST "$API_BASE/activity" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "courseid": '$COURSE_ID',
    "sectionid": '$SECTION_ID',
    "modname": "assign",
    "name": "API Created Assignment",
    "intro": "This assignment was created via API",
    "visible": true
  }'
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] New activity data returned with ID
- [ ] Refresh course - new assignment appears
- [ ] Assignment has correct name and description

### Test Different Module Types:
```bash
# Create a quiz:
curl -X POST "$API_BASE/activity" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "courseid": '$COURSE_ID',
    "sectionid": '$SECTION_ID',
    "modname": "quiz",
    "name": "API Created Quiz",
    "intro": "Test your knowledge",
    "visible": true
  }'
```
- [ ] Quiz created successfully

```bash
# Create a forum:
curl -X POST "$API_BASE/activity" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "courseid": '$COURSE_ID',
    "sectionid": '$SECTION_ID',
    "modname": "forum",
    "name": "Discussion Forum",
    "intro": "Discuss course topics here",
    "visible": true
  }'
```
- [ ] Forum created successfully

```bash
# Create a page:
curl -X POST "$API_BASE/activity" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "courseid": '$COURSE_ID',
    "sectionid": '$SECTION_ID',
    "modname": "page",
    "name": "Course Information",
    "intro": "Important course details",
    "visible": true
  }'
```
- [ ] Page created successfully

## Test 10: API Error Handling
**Objective**: Verify consistent error responses

### Test Cases:

#### 1. Missing Token:
```bash
curl -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Accept: application/json" \
  -v
```
- [ ] Response: 401 Unauthorized
- [ ] Error: "Authentication token is missing"

#### 2. Invalid Token:
```bash
curl -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer invalid.token.here" \
  -H "Accept: application/json" \
  -v
```
- [ ] Response: 401 Unauthorized
- [ ] Error: "Invalid or expired authentication token"

#### 3. Invalid JSON:
```bash
curl -X PUT "$API_BASE/activity/$ACTIVITY_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d 'this is not valid json' \
  -v
```
- [ ] Response: 422 Unprocessable Entity

#### 4. Resource Not Found:
```bash
curl -X GET "$API_BASE/course/99999/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" \
  -v
```
- [ ] Response: 404 Not Found

```bash
curl -X DELETE "$API_BASE/activity/99999" \
  -H "Authorization: Bearer $TOKEN" \
  -v
```
- [ ] Response: 404 Not Found

## Test 11: CORS Support
**Objective**: Verify cross-origin requests work

### Test OPTIONS preflight:
```bash
curl -X OPTIONS "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Origin: http://example.com" \
  -H "Access-Control-Request-Method: GET" \
  -H "Access-Control-Request-Headers: Authorization" \
  -v
```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Response headers include:
  - `Access-Control-Allow-Origin: *`
  - `Access-Control-Allow-Headers: Authorization, Content-Type`
  - `Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS`

### Test cross-origin request:
```bash
curl -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Origin: http://example.com" \
  -H "Accept: application/json" \
  -v
```
- [ ] Request succeeds with proper CORS headers

## Performance Tests

### Large Course Test:
```bash
# Time the request:
time curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" > /dev/null
```
- [ ] GET management_data completes in < 2 seconds
- [ ] Response size is reasonable

### Check response size:
```bash
curl -s -X GET "$API_BASE/course/$COURSE_ID/management_data" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Accept: application/json" | wc -c
```
- [ ] Response size is appropriate for course content

## Security Tests

### SQL Injection:
```bash
# Try SQL injection in activity name:
curl -X PUT "$API_BASE/activity/$ACTIVITY_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "'; DROP TABLE mdl_user; --"
  }'
```
- [ ] Name is safely stored/displayed
- [ ] No database errors
- [ ] Activity name shows the literal string in Moodle

### XSS Prevention:
```bash
# Try XSS in section summary:
curl -X PUT "$API_BASE/section/$SECTION_ID" \
  -H "Authorization: Bearer $TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "summary": "<script>alert(\"XSS\")</script><p>Safe content</p>"
  }'
```
- [ ] Script is escaped/sanitized
- [ ] No alert appears when viewing course
- [ ] HTML tags like `<p>` are preserved

### Token Security:
```bash
# Decode the JWT token to inspect (requires jq):
echo $TOKEN | cut -d. -f2 | base64 -d 2>/dev/null | jq .
```
- [ ] Token contains user_id
- [ ] Token has expiration time (exp)
- [ ] Token has issued at time (iat)

```bash
# Try modified token (change a character):
MODIFIED_TOKEN="${TOKEN}x"
curl -X GET "$API_BASE/user/me" \
  -H "Authorization: Bearer $MODIFIED_TOKEN" \
  -H "Accept: application/json"
```
- [ ] Response: 401 Unauthorized
- [ ] Modified token is rejected

## API Endpoint Summary
All endpoints use the base URL: `http://localhost:8888/local/courseapi/api/index.php`

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/auth/token` | Generate JWT token |
| GET | `/user/me` | Get current user info |
| GET | `/course/{id}/management_data` | Get course structure |
| PUT | `/activity/{id}` | Update activity |
| DELETE | `/activity/{id}` | Delete activity |
| POST | `/activity` | Create new activity |
| PUT | `/section/{id}` | Update section |
| POST | `/section/{id}/reorder_activities` | Reorder activities |
| POST | `/section/{id}/move_activity` | Move activity to section |

## Final Checklist
- [ ] All endpoints accessible via `/api/index.php` path
- [ ] JWT authentication working correctly
- [ ] Consistent JSON response format
- [ ] Appropriate HTTP status codes
- [ ] Clear error messages (using Moodle error codes)
- [ ] No PHP errors in Moodle logs
- [ ] API respects Moodle permissions
- [ ] Changes made via API appear immediately in UI
- [ ] All module types can be created (assign, quiz, forum, resource, page, url, label)
- [ ] Section numbers handled correctly (not IDs)
