# Course Management API Phase 2 - Architecture Design

## Overview

This document provides the complete architectural design for implementing the three Phase 2 endpoints:
- POST /course (Create Course)
- DELETE /course/{id} (Delete Course)
- GET /course/{id} (Get Course Details)

## API Routing Design

### Integration in `/api/index.php`

The routing follows the existing pattern in the API router. All three endpoints will be integrated into the switch statement:

```php
switch ($method) {
    case 'GET':
        if (preg_match('/^course\/(\d+)$/', $path, $matches)) {
            // GET /course/{id} - Get course details
            $courseid = (int)$matches[1];
            $includes = $_GET['include'] ?? '';
            $userinfo = isset($_GET['userinfo']) ? (bool)$_GET['userinfo'] : true;
            
            $include_array = !empty($includes) ? explode(',', $includes) : [];
            $result = external::get_course_details($courseid, $include_array, $userinfo);
            send_response($result);
        }
        // ... existing GET routes
        break;
        
    case 'POST':
        if ($path === 'course') {
            // POST /course - Create course
            $required_fields = ['fullname', 'shortname', 'category'];
            foreach ($required_fields as $field) {
                if (!isset($input[$field]) || empty($input[$field])) {
                    send_error("Missing required field: $field", 422);
                }
            }
            
            $result = external::create_course(
                $input['fullname'],
                $input['shortname'],
                $input['category'],
                $input['summary'] ?? '',
                $input['format'] ?? 'topics',
                $input['numsections'] ?? 10,
                $input['startdate'] ?? time(),
                $input['enddate'] ?? 0,
                $input['visible'] ?? true,
                $input['options'] ?? []
            );
            send_response($result, 201);
        }
        // ... existing POST routes
        break;
        
    case 'DELETE':
        if (preg_match('/^course\/(\d+)$/', $path, $matches)) {
            // DELETE /course/{id}
            $courseid = (int)$matches[1];
            $async = isset($_GET['async']) ? (bool)$_GET['async'] : false;
            $confirm = isset($_GET['confirm']) ? (bool)$_GET['confirm'] : false;
            
            $result = external::delete_course($courseid, $async, $confirm);
            
            if (isset($result['requires_confirmation']) && $result['requires_confirmation']) {
                send_response($result, 409);
            } else {
                send_response(null, 204);
            }
        }
        // ... existing DELETE routes
        break;
}
```

## External Function Signatures

### 1. Create Course Function

```php
/**
 * Returns description of create_course parameters
 *
 * @return external_function_parameters
 */
public static function create_course_parameters() {
    return new external_function_parameters([
        'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
        'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
        'category' => new external_value(PARAM_INT, 'Category ID'),
        'summary' => new external_value(PARAM_RAW, 'Course summary', VALUE_DEFAULT, ''),
        'format' => new external_value(PARAM_TEXT, 'Course format', VALUE_DEFAULT, 'topics'),
        'numsections' => new external_value(PARAM_INT, 'Number of sections', VALUE_DEFAULT, 10),
        'startdate' => new external_value(PARAM_INT, 'Start date timestamp', VALUE_DEFAULT, 0),
        'enddate' => new external_value(PARAM_INT, 'End date timestamp', VALUE_DEFAULT, 0),
        'visible' => new external_value(PARAM_BOOL, 'Course visibility', VALUE_DEFAULT, true),
        'options' => new external_single_structure([
            'showgrades' => new external_value(PARAM_BOOL, 'Show gradebook', VALUE_OPTIONAL),
            'showreports' => new external_value(PARAM_BOOL, 'Show reports', VALUE_OPTIONAL),
            'maxbytes' => new external_value(PARAM_INT, 'Max upload size', VALUE_OPTIONAL),
            'enablecompletion' => new external_value(PARAM_BOOL, 'Enable completion', VALUE_OPTIONAL),
            'lang' => new external_value(PARAM_LANG, 'Force language', VALUE_OPTIONAL)
        ], 'Additional course options', VALUE_DEFAULT, [])
    ]);
}

/**
 * Create a new course
 *
 * @param string $fullname Course full name
 * @param string $shortname Course short name (must be unique)
 * @param int $category Category ID
 * @param string $summary Course summary
 * @param string $format Course format
 * @param int $numsections Number of sections
 * @param int $startdate Start date timestamp
 * @param int $enddate End date timestamp
 * @param bool $visible Course visibility
 * @param array $options Additional options
 * @return array Course creation result
 * @throws moodle_exception
 */
public static function create_course($fullname, $shortname, $category, $summary = '', 
    $format = 'topics', $numsections = 10, $startdate = 0, $enddate = 0, 
    $visible = true, $options = []) {
    
    global $CFG, $DB;
    require_once($CFG->dirroot . '/course/lib.php');
    
    // Parameter validation
    $params = self::validate_parameters(self::create_course_parameters(), [
        'fullname' => $fullname,
        'shortname' => $shortname,
        'category' => $category,
        'summary' => $summary,
        'format' => $format,
        'numsections' => $numsections,
        'startdate' => $startdate,
        'enddate' => $enddate,
        'visible' => $visible,
        'options' => $options
    ]);
    
    // Check category exists and user has permission
    $categorycontext = context_coursecat::instance($params['category']);
    self::validate_context($categorycontext);
    require_capability('moodle/course:create', $categorycontext);
    
    // Check shortname uniqueness
    if ($DB->record_exists('course', ['shortname' => $params['shortname']])) {
        throw new moodle_exception('shortnametaken', 'error', '', $params['shortname']);
    }
    
    // Prepare course data
    $coursedata = new stdClass();
    $coursedata->fullname = $params['fullname'];
    $coursedata->shortname = $params['shortname'];
    $coursedata->category = $params['category'];
    $coursedata->summary = $params['summary'];
    $coursedata->summaryformat = FORMAT_HTML;
    $coursedata->format = $params['format'];
    $coursedata->numsections = $params['numsections'];
    $coursedata->startdate = $params['startdate'] ?: time();
    $coursedata->enddate = $params['enddate'];
    $coursedata->visible = $params['visible'] ? 1 : 0;
    
    // Apply options
    $coursedata->showgrades = $options['showgrades'] ?? $CFG->showgrades ?? 1;
    $coursedata->showreports = $options['showreports'] ?? $CFG->showreports ?? 1;
    $coursedata->maxbytes = $options['maxbytes'] ?? $CFG->maxbytes ?? 0;
    $coursedata->enablecompletion = $options['enablecompletion'] ?? $CFG->enablecompletion ?? 1;
    $coursedata->lang = $options['lang'] ?? '';
    
    // Create the course
    $course = create_course($coursedata);
    
    // Return course info
    return [
        'id' => (int)$course->id,
        'shortname' => $course->shortname,
        'fullname' => $course->fullname,
        'displayname' => get_course_display_name_for_list($course),
        'category' => (int)$course->category,
        'visible' => (bool)$course->visible,
        'format' => $course->format,
        'startdate' => (int)$course->startdate,
        'enddate' => (int)$course->enddate,
        'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false)
    ];
}

/**
 * Returns description of create_course return value
 *
 * @return external_single_structure
 */
public static function create_course_returns() {
    return new external_single_structure([
        'id' => new external_value(PARAM_INT, 'Course ID'),
        'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
        'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
        'displayname' => new external_value(PARAM_TEXT, 'Course display name'),
        'category' => new external_value(PARAM_INT, 'Category ID'),
        'visible' => new external_value(PARAM_BOOL, 'Course visibility'),
        'format' => new external_value(PARAM_TEXT, 'Course format'),
        'startdate' => new external_value(PARAM_INT, 'Start date timestamp'),
        'enddate' => new external_value(PARAM_INT, 'End date timestamp'),
        'url' => new external_value(PARAM_URL, 'Course URL')
    ]);
}
```

### 2. Delete Course Function

```php
/**
 * Returns description of delete_course parameters
 *
 * @return external_function_parameters
 */
public static function delete_course_parameters() {
    return new external_function_parameters([
        'courseid' => new external_value(PARAM_INT, 'Course ID'),
        'async' => new external_value(PARAM_BOOL, 'Process asynchronously', VALUE_DEFAULT, false),
        'confirm' => new external_value(PARAM_BOOL, 'Skip confirmation', VALUE_DEFAULT, false)
    ]);
}

/**
 * Delete a course
 *
 * @param int $courseid Course ID to delete
 * @param bool $async Process deletion asynchronously
 * @param bool $confirm Skip confirmation check
 * @return array Deletion result or confirmation requirement
 * @throws moodle_exception
 */
public static function delete_course($courseid, $async = false, $confirm = false) {
    global $CFG, $DB;
    require_once($CFG->dirroot . '/course/lib.php');
    
    // Parameter validation
    $params = self::validate_parameters(self::delete_course_parameters(), [
        'courseid' => $courseid,
        'async' => $async,
        'confirm' => $confirm
    ]);
    
    // Get course and context
    $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
    $context = context_course::instance($course->id);
    self::validate_context($context);
    
    // Check capability
    require_capability('moodle/course:delete', $context);
    
    // Prevent deletion of site course
    if ($course->id == SITEID) {
        throw new moodle_exception('cannotdeletesitecoourse', 'error');
    }
    
    // Check for active users if confirmation not provided
    if (!$params['confirm']) {
        $activeusers = count_enrolled_users($context, '', 0, true);
        if ($activeusers > 0) {
            return [
                'error' => "Course has $activeusers active users. Set confirm=true to force deletion",
                'active_users' => $activeusers,
                'requires_confirmation' => true
            ];
        }
    }
    
    // Perform deletion
    if ($params['async']) {
        // Queue for async deletion (requires admin/cli/delete_course.php or similar)
        $task = new \core\task\delete_course_task();
        $task->set_custom_data(['courseid' => $course->id]);
        \core\task\manager::queue_adhoc_task($task);
        
        return ['status' => 'queued', 'message' => 'Course deletion queued for processing'];
    } else {
        // Delete synchronously
        delete_course($course);
        return [];
    }
}

/**
 * Returns description of delete_course return value
 *
 * @return external_single_structure
 */
public static function delete_course_returns() {
    return new external_single_structure([
        'error' => new external_value(PARAM_TEXT, 'Error message', VALUE_OPTIONAL),
        'active_users' => new external_value(PARAM_INT, 'Number of active users', VALUE_OPTIONAL),
        'requires_confirmation' => new external_value(PARAM_BOOL, 'Confirmation required', VALUE_OPTIONAL),
        'status' => new external_value(PARAM_TEXT, 'Operation status', VALUE_OPTIONAL),
        'message' => new external_value(PARAM_TEXT, 'Status message', VALUE_OPTIONAL)
    ], 'Delete course result', VALUE_OPTIONAL);
}
```

### 3. Get Course Details Function

```php
/**
 * Returns description of get_course_details parameters
 *
 * @return external_function_parameters
 */
public static function get_course_details_parameters() {
    return new external_function_parameters([
        'courseid' => new external_value(PARAM_INT, 'Course ID'),
        'includes' => new external_multiple_structure(
            new external_value(PARAM_TEXT, 'Include type'),
            'Additional data to include',
            VALUE_DEFAULT,
            []
        ),
        'userinfo' => new external_value(PARAM_BOOL, 'Include user enrollment info', VALUE_DEFAULT, true)
    ]);
}

/**
 * Get course details
 *
 * @param int $courseid Course ID
 * @param array $includes Additional data to include
 * @param bool $userinfo Include user enrollment info
 * @return array Course details
 * @throws moodle_exception
 */
public static function get_course_details($courseid, $includes = [], $userinfo = true) {
    global $CFG, $DB, $USER;
    require_once($CFG->libdir . '/enrollib.php');
    require_once($CFG->libdir . '/completionlib.php');
    
    // Parameter validation
    $params = self::validate_parameters(self::get_course_details_parameters(), [
        'courseid' => $courseid,
        'includes' => $includes,
        'userinfo' => $userinfo
    ]);
    
    // Get course
    $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
    $context = context_course::instance($course->id);
    
    // Check access - user must be enrolled or have view capability
    $enrolled = is_enrolled($context, $USER->id, '', true);
    if (!$enrolled && !has_capability('moodle/course:view', $context)) {
        throw new moodle_exception('nopermissions', 'error', '', 'view this course');
    }
    
    // Get category info
    $category = $DB->get_record('course_categories', ['id' => $course->category], '*', MUST_EXIST);
    
    // Build basic course info
    $result = [
        'id' => (int)$course->id,
        'shortname' => $course->shortname,
        'fullname' => $course->fullname,
        'displayname' => get_course_display_name_for_list($course),
        'summary' => format_text($course->summary, $course->summaryformat, ['context' => $context]),
        'summaryformat' => (int)$course->summaryformat,
        'format' => $course->format,
        'startdate' => (int)$course->startdate,
        'enddate' => (int)$course->enddate,
        'visible' => (bool)$course->visible,
        'category' => [
            'id' => (int)$category->id,
            'name' => $category->name,
            'path' => $category->path
        ],
        'timecreated' => (int)$course->timecreated,
        'timemodified' => (int)$course->timemodified,
        'url' => (new moodle_url('/course/view.php', ['id' => $course->id]))->out(false),
        'enrollmentcount' => count_enrolled_users($context, '', 0, true),
        'sectioncount' => course_get_format($course)->get_last_section_number(),
        'activitycount' => count(get_fast_modinfo($course)->get_cms()),
        'completionenabled' => (bool)$course->enablecompletion
    ];
    
    // Add user enrollment info if requested and user is enrolled
    if ($params['userinfo'] && $enrolled) {
        $roles = get_user_roles($context, $USER->id);
        $rolenames = array_map(function($role) {
            return $role->shortname;
        }, $roles);
        
        $lastaccess = $DB->get_field('user_lastaccess', 'timeaccess', 
            ['userid' => $USER->id, 'courseid' => $course->id]);
        
        $result['user_enrollment'] = [
            'enrolled' => true,
            'roles' => array_values($rolenames),
            'timeenrolled' => 0, // Would need to query enrol tables
            'progress' => 0,
            'lastaccess' => (int)$lastaccess
        ];
        
        // Add completion progress if enabled
        if ($course->enablecompletion) {
            $completion = new completion_info($course);
            if ($completion->is_enabled()) {
                $progress = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
                $result['user_enrollment']['progress'] = $progress !== null ? (int)$progress : 0;
            }
        }
    } elseif ($params['userinfo']) {
        $result['user_enrollment'] = [
            'enrolled' => false,
            'roles' => [],
            'timeenrolled' => 0,
            'progress' => 0,
            'lastaccess' => 0
        ];
    }
    
    // Add optional includes
    if (in_array('enrollmentmethods', $params['includes'])) {
        $enrolinstances = enrol_get_instances($course->id, true);
        $result['enrollment_methods'] = [];
        
        foreach ($enrolinstances as $instance) {
            $plugin = enrol_get_plugin($instance->enrol);
            if ($plugin) {
                $method = [
                    'type' => $instance->enrol,
                    'enabled' => (bool)$instance->status == ENROL_INSTANCE_ENABLED,
                    'name' => $plugin->get_instance_name($instance)
                ];
                
                if ($instance->enrol === 'self') {
                    $method['password_required'] = !empty($instance->password);
                    $method['enrollment_key'] = has_capability('moodle/course:update', $context) 
                        ? $instance->password : '';
                }
                
                $result['enrollment_methods'][] = $method;
            }
        }
    }
    
    if (in_array('completion', $params['includes']) && $course->enablecompletion) {
        $completion = new completion_info($course);
        $criteria = $completion->get_criteria();
        
        $result['completion'] = [
            'enabled' => true,
            'criteria_count' => count($criteria),
            'user_completed' => 0,
            'user_completion_percentage' => 0
        ];
        
        if ($enrolled) {
            $progress = \core_completion\progress::get_course_progress_percentage($course, $USER->id);
            $result['completion']['user_completion_percentage'] = $progress !== null ? (int)$progress : 0;
            
            // Count completed criteria
            $completed = 0;
            foreach ($criteria as $criterion) {
                $completion = $completion->get_user_completion($USER->id, $criterion);
                if ($completion->is_complete()) {
                    $completed++;
                }
            }
            $result['completion']['user_completed'] = $completed;
        }
    }
    
    return $result;
}

/**
 * Returns description of get_course_details return value
 *
 * @return external_single_structure
 */
public static function get_course_details_returns() {
    return new external_single_structure([
        'id' => new external_value(PARAM_INT, 'Course ID'),
        'shortname' => new external_value(PARAM_TEXT, 'Course short name'),
        'fullname' => new external_value(PARAM_TEXT, 'Course full name'),
        'displayname' => new external_value(PARAM_TEXT, 'Course display name'),
        'summary' => new external_value(PARAM_RAW, 'Course summary'),
        'summaryformat' => new external_value(PARAM_INT, 'Summary format'),
        'format' => new external_value(PARAM_TEXT, 'Course format'),
        'startdate' => new external_value(PARAM_INT, 'Start date timestamp'),
        'enddate' => new external_value(PARAM_INT, 'End date timestamp'),
        'visible' => new external_value(PARAM_BOOL, 'Course visibility'),
        'category' => new external_single_structure([
            'id' => new external_value(PARAM_INT, 'Category ID'),
            'name' => new external_value(PARAM_TEXT, 'Category name'),
            'path' => new external_value(PARAM_TEXT, 'Category path')
        ]),
        'timecreated' => new external_value(PARAM_INT, 'Creation timestamp'),
        'timemodified' => new external_value(PARAM_INT, 'Last modified timestamp'),
        'url' => new external_value(PARAM_URL, 'Course URL'),
        'enrollmentcount' => new external_value(PARAM_INT, 'Number of enrolled users'),
        'sectioncount' => new external_value(PARAM_INT, 'Number of sections'),
        'activitycount' => new external_value(PARAM_INT, 'Number of activities'),
        'completionenabled' => new external_value(PARAM_BOOL, 'Completion tracking enabled'),
        'user_enrollment' => new external_single_structure([
            'enrolled' => new external_value(PARAM_BOOL, 'User is enrolled'),
            'roles' => new external_multiple_structure(
                new external_value(PARAM_TEXT, 'Role shortname')
            ),
            'timeenrolled' => new external_value(PARAM_INT, 'Enrollment timestamp'),
            'progress' => new external_value(PARAM_INT, 'Completion percentage'),
            'lastaccess' => new external_value(PARAM_INT, 'Last access timestamp')
        ], 'User enrollment info', VALUE_OPTIONAL),
        'enrollment_methods' => new external_multiple_structure(
            new external_single_structure([
                'type' => new external_value(PARAM_TEXT, 'Enrollment type'),
                'enabled' => new external_value(PARAM_BOOL, 'Method enabled'),
                'name' => new external_value(PARAM_TEXT, 'Method name'),
                'password_required' => new external_value(PARAM_BOOL, 'Password required', VALUE_OPTIONAL),
                'enrollment_key' => new external_value(PARAM_TEXT, 'Enrollment key', VALUE_OPTIONAL)
            ]),
            'Available enrollment methods',
            VALUE_OPTIONAL
        ),
        'completion' => new external_single_structure([
            'enabled' => new external_value(PARAM_BOOL, 'Completion enabled'),
            'criteria_count' => new external_value(PARAM_INT, 'Number of criteria'),
            'user_completed' => new external_value(PARAM_INT, 'Completed criteria'),
            'user_completion_percentage' => new external_value(PARAM_INT, 'Completion percentage')
        ], 'Completion info', VALUE_OPTIONAL)
    ]);
}
```

## Error Handling Strategy

### 1. HTTP Status Code Mapping

```php
// In index.php error handling:
$http_code_map = [
    'invalidtoken' => 401,
    'notloggedin' => 401,
    'nopermissions' => 403,
    'invalidrecord' => 404,
    'shortnametaken' => 400,
    'invalidcourse' => 400,
    'cannotdeletesitecoourse' => 400,
    'missingrequiredfield' => 422
];
```

### 2. Error Response Format

All error responses follow this format:
```json
{
    "error": "Human-readable error message"
}
```

### 3. Validation Rules

#### Create Course Validation:
- `fullname`: Required, non-empty string
- `shortname`: Required, unique, non-empty string
- `category`: Required, valid category ID
- `format`: Optional, must be valid course format
- `numsections`: Optional, positive integer
- `startdate`/`enddate`: Optional, valid timestamps
- `visible`: Optional, boolean

#### Delete Course Validation:
- `courseid`: Required, valid course ID
- Cannot delete site course (ID = SITEID)
- User must have delete capability
- Check for active enrollments if confirm=false

#### Get Course Details Validation:
- `courseid`: Required, valid course ID
- User must be enrolled or have view capability
- Optional includes validated against allowed list

## Security Considerations

### 1. Capability Checks

```php
// Create Course
require_capability('moodle/course:create', $categorycontext);

// Delete Course
require_capability('moodle/course:delete', $coursecontext);

// Get Course Details
// Must be enrolled OR have view capability
if (!is_enrolled($context) && !has_capability('moodle/course:view', $context)) {
    throw new moodle_exception('nopermissions');
}
```

### 2. Data Sanitization

- All input parameters validated using Moodle's PARAM_* types
- HTML content uses FORMAT_HTML with proper context
- SQL injection prevented via parameterized queries
- XSS prevention through output escaping

### 3. Authentication

- All endpoints require valid JWT token
- Token validated before any operation
- User session established from token

## Integration Test Scenarios

### 1. Create Course Tests

```php
// Test 1: Create with minimum fields
$data = [
    'fullname' => 'Test Course',
    'shortname' => 'TC101',
    'category' => 1
];

// Test 2: Create with all fields
$data = [
    'fullname' => 'Complete Test Course',
    'shortname' => 'CTC101',
    'category' => 1,
    'summary' => '<p>Course description</p>',
    'format' => 'topics',
    'numsections' => 12,
    'startdate' => time(),
    'enddate' => time() + (90 * 24 * 60 * 60),
    'visible' => true,
    'options' => [
        'showgrades' => true,
        'enablecompletion' => true
    ]
];

// Test 3: Duplicate shortname (should fail)
// Test 4: Invalid category (should fail)
// Test 5: No permission (should fail)
```

### 2. Delete Course Tests

```php
// Test 1: Delete empty course
// Test 2: Delete with enrollments, no confirmation (should return 409)
// Test 3: Delete with enrollments, with confirmation
// Test 4: Async deletion
// Test 5: Delete site course (should fail)
// Test 6: No permission (should fail)
```

### 3. Get Course Details Tests

```php
// Test 1: Basic details as enrolled user
// Test 2: Details with all includes
// Test 3: Details as admin (not enrolled)
// Test 4: Details without enrollment (should fail)
// Test 5: Non-existent course (should fail)
// Test 6: Hidden course visibility
```

## Performance Optimization

### 1. Query Optimization
- Use get_fast_modinfo() for activity counts
- Cache course format instances
- Batch load user enrollment data

### 2. Response Caching
- Cache course details for enrolled users
- Invalidate on course update
- Consider Redis/Memcached for production

### 3. Async Operations
- Large course deletion should be queued
- Progress tracking for long operations
- Cleanup in batches

## Database Considerations

### 1. Required Tables
- `course` - Main course data
- `course_categories` - Category information
- `course_sections` - Section data
- `user_enrolments` - User enrollment data
- `role_assignments` - User roles
- `course_completions` - Completion tracking

### 2. Indexes
- Ensure index on `course.shortname` for uniqueness checks
- Index on `user_lastaccess` for performance
- Category path index for hierarchy queries

## Next Steps

1. Implement the three functions in external.php
2. Update index.php routing
3. Create comprehensive unit tests
4. Add integration tests
5. Update API documentation
6. Performance testing with large datasets