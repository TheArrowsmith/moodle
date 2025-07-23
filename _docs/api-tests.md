# API for Assignment Data - Acceptance Tests

## Prerequisites
- Local Custom API plugin installed
- Web services enabled in Moodle
- REST protocol enabled
- At least one course with Code Sandbox activities
- Multiple students with submissions
- API testing tool (Postman, curl, or similar)

## Test 1: Plugin Installation Verification

### Steps:
1. Log in as administrator
2. Navigate to Site administration → Notifications
3. Verify local_customapi installation
4. Check Site administration → Plugins → Local plugins

### Expected Results:
- [ ] Plugin installs without errors
- [ ] local_customapi appears in local plugins list
- [ ] No database errors
- [ ] Version number displays correctly

## Test 2: Enable Web Services

### Steps:
1. Go to Site administration → Advanced features
2. Check "Enable web services"
3. Save changes
4. Go to Site administration → Plugins → Web services → Manage protocols
5. Enable REST protocol

### Expected Results:
- [ ] Web services checkbox enables successfully
- [ ] REST protocol can be enabled
- [ ] Eye icon shows REST is active
- [ ] No error messages

## Test 3: Create Web Service

### Steps:
1. Navigate to Site administration → Plugins → Web services → External services
2. Click "Add"
3. Create service:
   - Name: "Custom Grade API"
   - Short name: "customgradeapi"
   - Enabled: Yes
   - Authorized users only: Yes
4. Save changes
5. Click "Functions" for the new service
6. Add function: `local_customapi_get_sandbox_grades`

### Expected Results:
- [ ] Service creates successfully
- [ ] Function appears in available functions list
- [ ] Function can be added to service
- [ ] Service shows 1 function

## Test 4: Create API Token

### Steps:
1. Go to Site administration → Plugins → Web services → Manage tokens
2. Click "Add"
3. Fill in:
   - User: Select a teacher account
   - Service: Custom Grade API
   - Valid until: Leave empty or future date
4. Save changes
5. Copy the generated token

### Expected Results:
- [ ] Token generates successfully
- [ ] Token is 32 characters long
- [ ] Token associated with correct user
- [ ] Token appears in tokens list

## Test 5: Basic API Call

### Steps:
1. Using curl or Postman, make a POST request:
   ```bash
   curl -X POST https://yourmoodle.com/webservice/rest/server.php \
     -d "wstoken=YOUR_TOKEN_HERE" \
     -d "wsfunction=local_customapi_get_sandbox_grades" \
     -d "moodlewsrestformat=json" \
     -d "courseid=2"
   ```
2. Replace YOUR_TOKEN_HERE with actual token
3. Replace courseid with valid course ID

### Expected Results:
- [ ] HTTP 200 response
- [ ] Valid JSON returned
- [ ] No error messages
- [ ] Data structure matches specification

## Test 6: Response Data Validation

### Steps:
1. Examine the JSON response from Test 5
2. Verify each record contains:
   - userid (integer)
   - username (string)
   - fullname (string)
   - activityid (integer)
   - activityname (string)
   - cmid (integer)
   - grade (float or null)
   - submissionstatus (string)
   - timesubmitted (integer or null)

### Expected Results:
- [ ] All required fields present
- [ ] Data types match specification
- [ ] Grades are numeric or null
- [ ] Status is "submitted" or "notsubmitted"
- [ ] Full names formatted correctly

## Test 7: Multiple Students and Activities

### Steps:
1. Ensure course has:
   - At least 3 Code Sandbox activities
   - At least 5 enrolled students
   - Various submission states
2. Make API call for this course
3. Verify response contains all combinations

### Expected Results:
- [ ] Response includes all students × activities
- [ ] Each student appears for each activity
- [ ] Submitted and not submitted statuses appear
- [ ] Grades vary based on submissions

## Test 8: Permission Testing - Valid Teacher

### Steps:
1. Create token for a teacher in the course
2. Make API call with teacher's token
3. Verify successful response

### Expected Results:
- [ ] Teacher can access course data
- [ ] All student grades visible
- [ ] No permission errors
- [ ] Complete data returned

## Test 9: Permission Testing - Wrong Course

### Steps:
1. Using teacher token, request different course ID
2. Course where teacher is NOT enrolled
3. Make API call

### Expected Results:
- [ ] Permission denied error
- [ ] No student data exposed
- [ ] Clear error message
- [ ] HTTP error code (not 200)

## Test 10: Permission Testing - Student Token

### Steps:
1. Create token for a student account
2. Attempt to call the API
3. Check response

### Expected Results:
- [ ] Access denied
- [ ] Error indicates missing capability
- [ ] No grade data returned
- [ ] Security maintained

## Test 11: Invalid Parameters

### Steps:
1. Test with missing courseid:
   ```bash
   curl -X POST https://yourmoodle.com/webservice/rest/server.php \
     -d "wstoken=YOUR_TOKEN" \
     -d "wsfunction=local_customapi_get_sandbox_grades" \
     -d "moodlewsrestformat=json"
   ```
2. Test with invalid courseid:
   ```bash
   -d "courseid=99999"
   ```
3. Test with non-numeric courseid:
   ```bash
   -d "courseid=abc"
   ```

### Expected Results:
- [ ] Missing parameter error
- [ ] Invalid course error
- [ ] Parameter type error
- [ ] No crashes or PHP errors

## Test 12: Token Authentication Failures

### Steps:
1. Test with invalid token:
   ```bash
   -d "wstoken=invalidtoken123"
   ```
2. Test with missing token:
   ```bash
   # Omit wstoken parameter
   ```
3. Test with expired token (if applicable)

### Expected Results:
- [ ] "Invalid token" error
- [ ] "Missing token" error  
- [ ] No data exposed
- [ ] Clear error messages

## Test 13: Format Options

### Steps:
1. Test with XML format:
   ```bash
   -d "moodlewsrestformat=xml"
   ```
2. Test with no format specified
3. Compare outputs

### Expected Results:
- [ ] XML format returns valid XML
- [ ] Default format works (JSON or XML)
- [ ] Data identical in both formats
- [ ] Proper content-type headers

## Test 14: Performance Testing

### Steps:
1. Create course with:
   - 10 Code Sandbox activities
   - 50 enrolled students
   - Various submissions
2. Time the API call
3. Check response size

### Expected Results:
- [ ] Response time under 5 seconds
- [ ] No timeout errors
- [ ] Complete data returned
- [ ] Server remains responsive

## Test 15: Special Characters in Data

### Steps:
1. Create activities with special names:
   - "Test & Practice"
   - "Assignment #1: <HTML>"
   - "Ñoño's Exercise"
   - "作业 (Assignment)"
2. Students with special characters in names
3. Make API call

### Expected Results:
- [ ] Special characters properly encoded
- [ ] JSON remains valid
- [ ] No encoding errors
- [ ] Unicode handled correctly

## Test 16: Empty Course Test

### Steps:
1. Create new course with no Code Sandbox activities
2. Make API call for this course

### Expected Results:
- [ ] Empty array returned
- [ ] No errors
- [ ] Valid JSON structure
- [ ] Quick response

## Test 17: Gradebook Integration

### Steps:
1. Have students submit code with grades
2. Call API to get grades
3. Compare with Moodle gradebook
4. Manually update a grade in gradebook
5. Call API again

### Expected Results:
- [ ] API grades match gradebook
- [ ] Grade calculations consistent
- [ ] Manual overrides reflected
- [ ] No discrepancies

## Test 18: Concurrent API Calls

### Steps:
1. Make 10 simultaneous API calls
2. Use different course IDs
3. Monitor server performance

### Expected Results:
- [ ] All calls succeed
- [ ] No data mixing between calls
- [ ] Server handles load
- [ ] Consistent response times

## Test 19: API Documentation Test

### Steps:
1. Go to Site administration → Plugins → Web services → API Documentation
2. Find local_customapi_get_sandbox_grades
3. Review documentation

### Expected Results:
- [ ] Function is documented
- [ ] Parameters described clearly
- [ ] Return structure documented
- [ ] Examples provided

## Test 20: Cross-Origin Requests (CORS)

### Steps:
1. Create simple HTML page on different domain
2. Make AJAX call to API
3. Check browser console

### Expected Results:
- [ ] Understand CORS limitations
- [ ] Options for enabling if needed
- [ ] Security implications clear
- [ ] Alternative approaches documented

## Integration Testing

### Test 21: Progress Dashboard Integration

### Steps:
1. Access instructor progress dashboard
2. Compare data with direct API call
3. Verify consistency

### Expected Results:
- [ ] Dashboard uses same API
- [ ] Data matches exactly
- [ ] Performance acceptable
- [ ] No duplicate API calls

## Common Issues and Solutions

### Issue: "Access denied" errors
- **Check**: User has moodle/grade:viewall capability
- **Check**: Token is for correct user
- **Check**: User enrolled in course as teacher

### Issue: Empty responses
- **Check**: Code Sandbox activities exist in course
- **Check**: Course ID is correct
- **Check**: Activities are visible

### Issue: Timeout errors
- **Check**: Database indexes exist
- **Check**: Not too many students/activities
- **Consider**: Pagination for large datasets

## Security Audit

- [ ] Tokens not logged in plain text
- [ ] SQL injection not possible
- [ ] XSS prevented in responses
- [ ] Rate limiting considered
- [ ] Access logs available

## Performance Benchmarks

| Scenario | Expected Time |
|----------|--------------|
| 10 students, 5 activities | < 1 second |
| 50 students, 10 activities | < 3 seconds |
| 100 students, 20 activities | < 5 seconds |

## Sign-off

- [ ] All tests completed successfully
- [ ] API performs within benchmarks
- [ ] Security measures verified
- [ ] Documentation complete
- [ ] Ready for production use

**Tester Name**: _________________

**Date**: _________________

**Notes**: _________________