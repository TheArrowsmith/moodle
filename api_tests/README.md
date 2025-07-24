# Moodle Course Management API - Automated Tests

This directory contains automated tests for the Moodle Course Management API, implemented using Ruby and RSpec.

## Prerequisites

- Ruby 2.7+ installed
- Bundler gem installed (`gem install bundler`)
- A running Moodle instance with:
  - The Course Management API plugin installed
  - Test users created (admin, teacher, student)
  - A test course with appropriate permissions

## Setup

1. **Install dependencies:**
   ```bash
   cd api_tests
   bundle install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   ```

3. **Edit `.env` file with your test environment details:**
   ```
   MOODLE_BASE_URL=http://localhost:8080
   
   TEST_ADMIN_USERNAME=admin
   TEST_ADMIN_PASSWORD=Admin123!
   
   TEST_TEACHER_USERNAME=teacher1
   TEST_TEACHER_PASSWORD=Teacher123!
   
   TEST_STUDENT_USERNAME=student1
   TEST_STUDENT_PASSWORD=Student123!
   
   TEST_COURSE_ID=2
   ```

## Running Tests

**Run all tests:**
```bash
bundle exec rspec
```

**Run specific test file:**
```bash
bundle exec rspec spec/endpoints/activity_spec.rb
```

**Run tests matching a pattern:**
```bash
bundle exec rspec -e "JWT token"
```

**Run tests with detailed output:**
```bash
bundle exec rspec --format documentation
```

**Run tests in parallel (if you have parallel_tests gem):**
```bash
bundle exec parallel_rspec spec/
```

## Test Structure

```
spec/
├── authentication/     # JWT token generation and validation
├── endpoints/         # API endpoint tests
│   ├── user_spec.rb   # GET /user/me
│   ├── course_spec.rb # GET /course/{id}/management_data
│   ├── activity_spec.rb # Activity CRUD operations
│   └── section_spec.rb  # Section updates and reordering
├── integration/       # Cross-cutting concerns
│   └── error_handling_spec.rb # Consistent error responses
├── security/          # Security testing
│   ├── sql_injection_spec.rb # SQL injection prevention
│   └── xss_prevention_spec.rb # XSS prevention
└── support/           # Helper modules
    ├── api_helper.rb  # HTTP client wrapper
    ├── jwt_helper.rb  # JWT utilities
    └── test_data.rb   # Test data generators
```

## Test Coverage

The test suite covers all endpoints from the API specification:

### Authentication
- `POST /auth/token` - JWT token generation
- Token validation on all protected endpoints
- Token expiration handling

### User Operations
- `GET /user/me` - Current user information

### Course Operations
- `GET /course/{courseId}/management_data` - Full course structure

### Activity Operations
- `GET /activity/{activityId}` - Get activity details
- `PUT /activity/{activityId}` - Update activity properties
- `DELETE /activity/{activityId}` - Delete activity
- `POST /activity` - Create new activity

### Section Operations
- `PUT /section/{sectionId}` - Update section properties
- `POST /section/{sectionId}/reorder_activities` - Reorder activities
- `POST /section/{sectionId}/move_activity` - Move activity between sections

### Error Scenarios
- 401 Unauthorized - Missing/invalid/expired tokens
- 403 Forbidden - Insufficient permissions
- 404 Not Found - Non-existent resources
- 422 Unprocessable Entity - Invalid request data

### Security Testing
- SQL injection prevention
- XSS prevention
- Path traversal prevention
- Template injection prevention

## Writing New Tests

1. **Use the test helpers:**
   ```ruby
   # Authenticate before making requests
   authenticate_as(:teacher)
   
   # Create test data
   activity = create_activity(sample_activity_data)
   
   # Make API calls
   response = get("/activity/#{activity['id']}")
   expect(response.code).to eq(200)
   ```

2. **Clean up test data:**
   ```ruby
   # Activities created with create_activity are automatically cleaned up
   # For manual cleanup, track resources:
   @created_resources << { type: :activity, id: activity['id'] }
   ```

3. **Test both success and failure cases:**
   ```ruby
   it 'updates activity when authorized' do
     authenticate_as(:teacher)
     # ... test success case
   end
   
   it 'returns 403 when unauthorized' do
     authenticate_as(:student)
     # ... test failure case
   end
   ```

## Debugging

**Enable API debug mode:**
```bash
API_DEBUG=true bundle exec rspec
```

This will output all HTTP requests and responses.

**Use pry for debugging:**
```ruby
require 'pry'
# In your test:
binding.pry
```

**Check test logs:**
- RSpec output shows test failures with details
- Check `.rspec_status` for persistent test results
- Use `--format html` for HTML reports

## CI/CD Integration

For continuous integration, you can use this command:
```bash
bundle exec rspec --format RspecJunitFormatter --out spec/reports/rspec.xml
```

This generates JUnit-compatible XML output for CI systems.

## Troubleshooting

### Tests fail with 401 Unauthorized
- Check your credentials in `.env`
- Ensure the test users exist in Moodle
- Verify the Course Management API plugin is installed

### Tests fail with 404 Not Found
- Check `MOODLE_BASE_URL` includes the correct protocol
- Verify the API endpoints are at `/local/courseapi/api`
- Ensure the plugin is enabled in Moodle

### Connection refused errors
- Verify Moodle is running and accessible
- Check firewall rules if testing remote instance
- Try accessing the URL in a browser first

### Token expiration during long test runs
- The test suite obtains fresh tokens for each test file
- If individual tests take >60 minutes, they may fail
- Consider breaking up very long test files