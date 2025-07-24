# Course Management API - Manual Acceptance Tests

## Prerequisites
- Moodle installation with admin access
- A test course with at least 3 sections and 5 activities
- Teacher/Admin role in the test course
- Browser developer tools for inspecting network requests
- REST client tool (Postman, curl, or browser extensions)

## Test Setup
1. Install the local_courseapi plugin
2. Navigate to Site Administration > Notifications
3. Complete the plugin installation
4. Note your test course ID (visible in URL when viewing course)

## Test 1: JWT Token Generation
**Objective**: Verify JWT tokens are properly generated and injected

### Steps:
1. Navigate to any course where you have update permissions
2. Open browser developer console (F12)
3. In the console, type: `window.COURSE_API_TOKEN`
4. Press Enter

### Expected Results:
- [ ] A JWT token string should be displayed (format: `xxxxx.yyyyy.zzzzz`)
- [ ] Token should have three parts separated by dots
- [ ] No error messages in console

## Test 2: GET /user/me Endpoint
**Objective**: Verify user authentication endpoint works correctly

### Steps:
1. Get your JWT token from Test 1
2. Open your REST client
3. Make a GET request to: `http://[your-moodle]/local/courseapi/api/v1/user/me`
4. Add header: `Authorization: Bearer [your-token]`

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
1. Make request without Authorization header
   - [ ] Response status: 401 Unauthorized
   - [ ] Error message: "Authentication token is missing"

2. Make request with invalid token
   - [ ] Response status: 401 Unauthorized
   - [ ] Error message: "Invalid or expired authentication token"

## Test 3: GET /course/{courseId}/management_data
**Objective**: Verify course structure retrieval

### Steps:
1. Make a GET request to: `http://[your-moodle]/local/courseapi/api/v1/course/[courseId]/management_data`
2. Include valid Authorization header

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Response contains course name
- [ ] All course sections are listed
- [ ] Each section contains its activities
- [ ] Activity data includes: id, name, modname, modicon, visible
- [ ] Section data includes: id, name, visible, summary, activities array

### Error Testing:
1. Request non-existent course (courseId: 99999)
   - [ ] Response status: 404 Not Found
   - [ ] Appropriate error message

2. Request course without permissions
   - [ ] Response status: 403 Forbidden
   - [ ] Error indicates permission denied

## Test 4: Update Activity
**Objective**: Test activity property modifications

### Steps:
1. Note an activity ID from Test 3 response
2. Make a PUT request to: `http://[your-moodle]/local/courseapi/api/v1/activity/[activityId]`
3. Include request body:
   ```json
   {
     "name": "Updated Activity Name",
     "visible": false
   }
   ```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Response shows updated activity data
- [ ] Refresh course page in Moodle - activity name changed
- [ ] Activity is now hidden (grayed out)

### Partial Update Test:
1. Update only visibility:
   ```json
   { "visible": true }
   ```
   - [ ] Only visibility changes, name remains same

2. Update only name:
   ```json
   { "name": "Another Name" }
   ```
   - [ ] Only name changes, visibility remains same

## Test 5: Update Section
**Objective**: Test section property modifications

### Steps:
1. Note a section ID from Test 3
2. Make a PUT request to: `http://[your-moodle]/local/courseapi/api/v1/section/[sectionId]`
3. Include request body:
   ```json
   {
     "name": "Week 1: Updated Topic",
     "visible": false,
     "summary": "<p>This is the updated summary.</p>"
   }
   ```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Section data returned (without activities array)
- [ ] Refresh course - section name, visibility, and summary updated

## Test 6: Reorder Activities
**Objective**: Test drag-and-drop functionality via API

### Steps:
1. Note a section with multiple activities
2. Note the current order of activity IDs
3. Make a POST request to: `http://[your-moodle]/local/courseapi/api/v1/section/[sectionId]/reorder_activities`
4. Reverse the order in request body:
   ```json
   {
     "activity_ids": [103, 101, 102]
   }
   ```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Success message returned
- [ ] Refresh course - activities appear in new order

### Error Testing:
1. Include invalid activity ID in list
   - [ ] Response status: 400 Bad Request
   - [ ] Error indicates invalid activity

## Test 7: Delete Activity
**Objective**: Test activity deletion

### Steps:
1. Create a test activity in your course (or use existing)
2. Note its ID
3. Make a DELETE request to: `http://[your-moodle]/local/courseapi/api/v1/activity/[activityId]`

### Expected Results:
- [ ] Response status: 204 No Content
- [ ] No response body
- [ ] Refresh course - activity is gone
- [ ] Activity cannot be accessed directly

### Error Testing:
1. Try to delete same activity again
   - [ ] Response status: 404 Not Found
   - [ ] Error indicates activity doesn't exist

## Test 8: Move Activity Between Sections
**Objective**: Test moving activities to different sections

### Steps:
1. Note an activity ID and target section ID
2. Make a POST request to: `http://[your-moodle]/local/courseapi/api/v1/section/[targetSectionId]/move_activity`
3. Include request body:
   ```json
   {
     "activityid": 101,
     "position": 0
   }
   ```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] Success message returned
- [ ] Activity now appears in target section
- [ ] Activity removed from original section

## Test 9: Create New Activity
**Objective**: Test activity creation via API

### Steps:
1. Make a POST request to: `http://[your-moodle]/local/courseapi/api/v1/activity`
2. Include request body:
   ```json
   {
     "courseid": 2,
     "sectionid": 1,
     "modname": "assign",
     "name": "API Created Assignment",
     "intro": "This assignment was created via API",
     "visible": true
   }
   ```

### Expected Results:
- [ ] Response status: 200 OK
- [ ] New activity data returned with ID
- [ ] Refresh course - new assignment appears
- [ ] Assignment has correct name and description

### Test Different Module Types:
1. Create a quiz: `"modname": "quiz"`
2. Create a resource: `"modname": "resource"`
3. Create a forum: `"modname": "forum"`

## Test 10: API Error Handling
**Objective**: Verify consistent error responses

### Test Cases:
1. **Missing Token**
   - Remove Authorization header
   - [ ] All endpoints return 401 Unauthorized

2. **Expired Token**
   - Wait 61 minutes (or modify token)
   - [ ] Response: 401 with "Invalid or expired token"

3. **Invalid JSON**
   - Send malformed JSON in PUT/POST requests
   - [ ] Response: 422 Unprocessable Entity

4. **Permission Denied**
   - Use student account token
   - [ ] Response: 403 Forbidden

5. **Resource Not Found**
   - Use non-existent IDs
   - [ ] Response: 404 Not Found

## Test 11: CORS Support
**Objective**: Verify cross-origin requests work

### Steps:
1. Create a simple HTML file on different domain/port
2. Add JavaScript to make API call
3. Open file in browser

### Expected Results:
- [ ] No CORS errors in console
- [ ] OPTIONS preflight requests succeed
- [ ] API calls complete successfully

## Performance Tests

### Large Course Test:
1. Test with course containing 50+ activities
   - [ ] GET management_data completes in < 2 seconds
   - [ ] Response size is reasonable

### Bulk Operations:
1. Reorder 20+ activities in one request
   - [ ] Operation completes successfully
   - [ ] No timeout errors

## Security Tests

### SQL Injection:
1. Try activity name: `'; DROP TABLE mdl_user; --`
   - [ ] Name is safely stored/displayed
   - [ ] No database errors

### XSS Prevention:
1. Update section summary with: `<script>alert('XSS')</script>`
   - [ ] Script is escaped/sanitized
   - [ ] No alert appears when viewing

### Token Security:
1. Try to decode JWT token
   - [ ] Cannot modify user_id or course_id
   - [ ] Modified token is rejected

## Final Checklist
- [ ] All endpoints accessible via clean URLs
- [ ] Consistent JSON response format
- [ ] Appropriate HTTP status codes
- [ ] Clear error messages
- [ ] No PHP errors in Moodle logs
- [ ] API respects Moodle permissions
- [ ] Changes made via API appear immediately in UI