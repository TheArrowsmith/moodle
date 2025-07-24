# Course Management API Phase 2 - Test Suite

This directory contains comprehensive tests for the Course Management API Phase 2 endpoints.

## Test Files

### 1. `external_test.php`
Main unit tests for the external API functions. Tests all Phase 1 and Phase 2 functionality:
- Course creation with various parameters
- Course deletion with/without confirmation
- Course details retrieval with includes
- Permission checking for all operations
- Error handling and edge cases

### 2. `integration_test.php`
Integration tests that verify complete workflows:
- Full course lifecycle (create → populate → delete)
- Special character handling
- Concurrent operations
- Extreme value testing
- Error recovery scenarios
- Permission inheritance

### 3. `api_endpoint_test.php`
Tests that simulate actual HTTP API calls:
- REST endpoint routing
- Authentication via JWT tokens
- Request/response format validation
- HTTP status code verification
- CORS and header testing

### 4. `performance_test.php`
Performance and load testing:
- Bulk course creation
- Large course handling
- Memory usage monitoring
- Response time consistency
- Concurrent operation handling

## Running the Tests

### Run All Tests
```bash
# From Moodle root directory
vendor/bin/phpunit --testsuite local_courseapi_testsuite
```

### Run Individual Test Files
```bash
# Unit tests
vendor/bin/phpunit local/courseapi/tests/external_test.php

# Integration tests
vendor/bin/phpunit local/courseapi/tests/integration_test.php

# API endpoint tests
vendor/bin/phpunit local/courseapi/tests/api_endpoint_test.php

# Performance tests (may take longer)
vendor/bin/phpunit local/courseapi/tests/performance_test.php
```

### Run Specific Test Methods
```bash
# Test only course creation
vendor/bin/phpunit --filter test_create_course local/courseapi/tests/external_test.php

# Test only Phase 2 features
vendor/bin/phpunit --filter "test_create_course|test_delete_course|test_get_course_details" local/courseapi/tests/external_test.php
```

## Test Coverage

### Phase 2 Endpoints Tested

#### POST /course (Create Course)
- ✅ Minimum required fields
- ✅ All optional fields
- ✅ Duplicate shortname handling
- ✅ Invalid category handling
- ✅ Permission checking
- ✅ Special characters in names
- ✅ HTML content sanitization
- ✅ Default value application

#### DELETE /course/{id} (Delete Course)
- ✅ Empty course deletion
- ✅ Course with enrollments (confirmation required)
- ✅ Force deletion with confirmation
- ✅ Non-existent course handling
- ✅ Permission checking
- ✅ Large course deletion performance
- ✅ Complete data cleanup verification

#### GET /course/{id} (Get Course Details)
- ✅ Basic course information
- ✅ User enrollment information
- ✅ Optional includes (enrollmentmethods, completion)
- ✅ Permission checking (enrolled vs not enrolled)
- ✅ Hidden course visibility
- ✅ Admin override permissions
- ✅ Performance with large courses

### Error Scenarios Tested

- 400 Bad Request - Invalid parameters
- 401 Unauthorized - Invalid/missing token
- 403 Forbidden - Insufficient permissions
- 404 Not Found - Non-existent resources
- 409 Conflict - Deletion requires confirmation
- 422 Unprocessable Entity - Missing required fields

### Performance Benchmarks

Expected performance metrics (on standard hardware):
- Course creation: < 1 second average
- Course details retrieval: < 500ms for large courses
- Course deletion: < 30 seconds for courses with 100+ users
- Memory usage: < 100MB for bulk operations

## Test Data

Tests automatically create and clean up test data:
- Test users (admin, teacher, student)
- Test courses with various configurations
- Test activities and enrollments
- Test categories

All test data is rolled back after each test using Moodle's `resetAfterTest()`.

## Debugging Failed Tests

1. **Enable verbose output:**
   ```bash
   vendor/bin/phpunit --verbose local/courseapi/tests/external_test.php
   ```

2. **Check Moodle debugging:**
   Set `$CFG->debug = DEBUG_DEVELOPER` in config.php

3. **Examine test logs:**
   Performance tests output metrics to help identify bottlenecks

4. **Common issues:**
   - Capability definitions not loaded
   - Database transactions not properly reset
   - JWT token generation failures
   - Missing required Moodle libraries

## Adding New Tests

When adding new test cases:
1. Extend `advanced_testcase` for proper cleanup
2. Use `$this->resetAfterTest()` in setUp
3. Create users with appropriate roles/capabilities
4. Test both success and failure scenarios
5. Include performance considerations for scalability

## Continuous Integration

These tests are designed to run in CI/CD pipelines:
- All tests are self-contained
- No external dependencies required
- Automatic cleanup of test data
- Performance metrics can be tracked over time