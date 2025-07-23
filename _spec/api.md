### **Feature 4: API for Assignment Data**

*   **Objective:** To create a secure, modern API that exposes student progress data, enabling the creation of custom dashboards and external tools.
*   **User Story:** "As an instructor, I want a way to programmatically access my students' grades and submission statuses for coding assignments, so I can build my own custom reports or integrate the data with other tools."
*   **Acceptance Criteria:**
    1.  A new REST API endpoint is available, e.g., `/webservice/rest/server.php`.
    2.  The endpoint is secured and requires a Moodle web service token.
    3.  The API has a function that accepts a `course_id` and returns a JSON array of all student grades for all "Code Sandbox" activities in that course.

*   **Technical Specification:**
    *   **Moodle Component:** New Local Plugin (`local_customapi`) to define the web service.
    *   **Moodle Web Services:** You will use Moodle's built-in Web Services functionality.
        1.  Define a new external function within your local plugin's `externallib.php`.
        2.  This function will contain a direct SQL query to join `mdl_user`, `mdl_grade_grades`, `mdl_grade_items`, and `mdl_codesandbox` to get the required data.
        3.  Define the parameter types (`course_id`) and return type (a list of objects) in `db/services.php`.
        4.  An admin must manually enable the service, create a token for a specific user (e.g., the instructor), and authorize the new function.

# Implementation Plan

## Overview
This feature creates a REST API endpoint to expose student progress data for coding assignments, enabling instructors to build custom dashboards and integrate with external tools.

## Existing Code Analysis

### Relevant Database Tables
1. **Core Tables**:
   - `mdl_user` - User information
   - `mdl_course` - Course details
   - `mdl_grade_items` - Defines gradable items
   - `mdl_grade_grades` - Actual grade records
   - `mdl_course_modules` - Links activities to courses

2. **Web Service Tables**:
   - `mdl_external_functions` - Registered web service functions
   - `mdl_external_services` - Service definitions
   - `mdl_external_tokens` - Authentication tokens

3. **Assignment/Activity Tables**:
   - `mdl_assign` - Assignment instances (if using standard assignments)
   - `mdl_codesandbox` - Custom code sandbox activities (to be created)

### Key Code Components
1. **Web Service Framework**:
   - `/lib/externallib.php` - Core external API classes
   - `/webservice/rest/server.php` - REST endpoint
   - `/webservice/lib.php` - Web service utilities

2. **Grade Access**:
   - `/lib/gradelib.php` - Grade retrieval functions
   - `/grade/lib.php` - Additional grade utilities

3. **Local Plugin Structure**:
   - `/local/` - Location for local plugins
   - Standard plugin structure: `db/`, `classes/`, `version.php`

## Current Code Flow

### Web Service Request Flow
1. Client sends request to `/webservice/rest/server.php` with token
2. Server validates token and identifies user/permissions
3. Server determines requested function from parameters
4. Server calls the external function implementation
5. Function validates parameters using defined structures
6. Function executes logic with capability checks
7. Results are formatted according to return structure
8. Response is encoded (JSON/XML) and sent back

### Grade Retrieval Flow
1. Grade items are linked to course modules
2. `grade_get_grades()` retrieves grades for specific items
3. Grades are filtered by user permissions
4. Final grades consider category aggregation

## Implementation Changes

### New Components to Create

1. **Local Plugin: `local_customapi`**
   ```
   /local/customapi/
   ├── version.php           # Plugin version info
   ├── db/
   │   ├── services.php      # Web service definitions
   │   └── access.php        # Capability definitions
   ├── classes/
   │   └── external.php      # External API class
   └── lang/en/
       └── local_customapi.php  # Language strings
   ```

2. **External Function Class** (`classes/external.php`):
   ```php
   class local_customapi_external extends external_api {
       // Function parameter definitions
       public static function get_sandbox_grades_parameters() {
           return new external_function_parameters(array(
               'courseid' => new external_value(PARAM_INT, 'Course ID')
           ));
       }
       
       // Main function implementation
       public static function get_sandbox_grades($courseid) {
           // Parameter validation
           // Context and capability checks
           // SQL query to join tables
           // Format and return results
       }
       
       // Return structure definition
       public static function get_sandbox_grades_returns() {
           return new external_multiple_structure(
               new external_single_structure(array(
                   'userid' => new external_value(PARAM_INT),
                   'username' => new external_value(PARAM_TEXT),
                   'activityid' => new external_value(PARAM_INT),
                   'activityname' => new external_value(PARAM_TEXT),
                   'grade' => new external_value(PARAM_FLOAT),
                   'submissionstatus' => new external_value(PARAM_TEXT)
               ))
           );
       }
   }
   ```

3. **Service Definition** (`db/services.php`):
   ```php
   $functions = array(
       'local_customapi_get_sandbox_grades' => array(
           'classname'   => 'local_customapi_external',
           'methodname'  => 'get_sandbox_grades',
           'classpath'   => 'local/customapi/classes/external.php',
           'description' => 'Get grades for code sandbox activities',
           'type'        => 'read',
           'capabilities'=> 'moodle/grade:viewall'
       )
   );
   
   $services = array(
       'Custom API Service' => array(
           'functions' => array('local_customapi_get_sandbox_grades'),
           'restrictedusers' => 1,
           'enabled' => 1
       )
   );
   ```

### Database Changes
No schema changes required - using existing grade and user tables.

### Security Considerations
1. **Token Authentication**: Use Moodle's built-in token system
2. **Capability Checks**: Require `moodle/grade:viewall` or course-specific teacher role
3. **Context Validation**: Ensure user has access to requested course
4. **Data Filtering**: Only return data user is authorized to see

### Configuration Steps
1. **Enable Web Services**:
   - Admin → Advanced features → Enable web services
   - Admin → Plugins → Web services → Manage protocols → Enable REST

2. **Create Service**:
   - Admin → Plugins → Web services → External services
   - Add new service with the custom function

3. **Generate Token**:
   - Create token for specific user (instructor)
   - Set appropriate expiration and IP restrictions

### API Usage Example
```
POST /webservice/rest/server.php
Content-Type: application/x-www-form-urlencoded

wstoken=YOUR_TOKEN&
wsfunction=local_customapi_get_sandbox_grades&
moodlewsrestformat=json&
courseid=5
```

Response:
```json
[
    {
        "userid": 123,
        "username": "student1",
        "activityid": 45,
        "activityname": "Week 1 Coding Exercise",
        "grade": 85.5,
        "submissionstatus": "submitted"
    }
]
```

## Development Checklist
1. [ ] Create local plugin directory structure
2. [ ] Implement external function class with parameter validation
3. [ ] Write SQL query joining user, grade, and activity tables
4. [ ] Define service in services.php
5. [ ] Add language strings
6. [ ] Test with REST client
7. [ ] Document API endpoint and parameters
8. [ ] Add error handling for edge cases

## Testing Approach
1. Unit test the external function with mock data
2. Integration test with real course data
3. Test permission boundaries
4. Verify token authentication
5. Load test with multiple concurrent requests
