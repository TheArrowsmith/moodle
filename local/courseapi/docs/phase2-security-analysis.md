# Course Management API Phase 2 - Security Analysis

## Executive Summary

This document provides a comprehensive security analysis for the three Phase 2 endpoints. Each endpoint has been analyzed for potential vulnerabilities and appropriate mitigations have been designed.

## Endpoint Security Analysis

### 1. POST /course - Create Course

#### Threat Analysis

1. **Unauthorized Course Creation**
   - **Risk**: Users creating courses without permission
   - **Mitigation**: 
     - Enforce `moodle/course:create` capability check
     - Validate category-level permissions
     - Log all course creation attempts

2. **Resource Exhaustion**
   - **Risk**: Mass course creation leading to DoS
   - **Mitigation**:
     - Implement rate limiting per user
     - Queue creation for resource-intensive operations
     - Monitor creation patterns

3. **SQL Injection via Input**
   - **Risk**: Malicious SQL in course names/descriptions
   - **Mitigation**:
     - Use parameterized queries exclusively
     - Validate all inputs with PARAM_* types
     - Escape special characters

4. **XSS in Course Content**
   - **Risk**: JavaScript injection in summary/description
   - **Mitigation**:
     - Use FORMAT_HTML with proper filtering
     - Context-aware output escaping
     - Content Security Policy headers

5. **Privilege Escalation**
   - **Risk**: Creating courses in unauthorized categories
   - **Mitigation**:
     - Check category-specific permissions
     - Validate category hierarchy access
     - Audit trail for category changes

#### Security Controls

```php
// Input validation
$fullname = clean_param($input['fullname'], PARAM_TEXT);
$shortname = clean_param($input['shortname'], PARAM_TEXT);
$category = clean_param($input['category'], PARAM_INT);

// HTML content filtering
$summary = format_text($input['summary'], FORMAT_HTML, [
    'context' => $context,
    'trusted' => false,
    'noclean' => false
]);

// Rate limiting check
if (api_rate_limit_exceeded($USER->id, 'course_create', 10, 3600)) {
    throw new moodle_exception('ratelimitexceeded');
}

// Audit logging
add_to_log($course->id, 'course', 'new', 'view.php?id='.$course->id, 
    'Course created via API by user '.$USER->id);
```

### 2. DELETE /course/{id} - Delete Course

#### Threat Analysis

1. **Unauthorized Deletion**
   - **Risk**: Deleting courses without permission
   - **Mitigation**:
     - Enforce `moodle/course:delete` capability
     - Multi-factor confirmation for courses with users
     - Comprehensive audit logging

2. **Data Loss**
   - **Risk**: Accidental deletion of important courses
   - **Mitigation**:
     - Require explicit confirmation parameter
     - Backup before deletion option
     - Soft delete with recovery period

3. **Cascade Deletion Issues**
   - **Risk**: Orphaned data or incomplete cleanup
   - **Mitigation**:
     - Use Moodle's delete_course() for proper cleanup
     - Transaction-based deletion
     - Verify complete removal

4. **Timing Attacks**
   - **Risk**: Information disclosure via deletion timing
   - **Mitigation**:
     - Consistent response times
     - Queue large deletions
     - Generic error messages

#### Security Controls

```php
// Prevent site course deletion
if ($courseid == SITEID) {
    throw new moodle_exception('cannotdeletesitecourse');
}

// Force confirmation for active courses
if (!$confirm && count_enrolled_users($context) > 0) {
    return ['requires_confirmation' => true];
}

// Create backup before deletion
if ($create_backup) {
    $backup = backup_course($courseid);
    store_deletion_backup($backup);
}

// Comprehensive audit log
$event = \core\event\course_deleted::create([
    'objectid' => $course->id,
    'context' => $context,
    'other' => [
        'fullname' => $course->fullname,
        'api_deletion' => true
    ]
]);
$event->trigger();
```

### 3. GET /course/{id} - Get Course Details

#### Threat Analysis

1. **Information Disclosure**
   - **Risk**: Exposing course data to unauthorized users
   - **Mitigation**:
     - Enrollment-based access control
     - Role-specific data filtering
     - Hidden course protection

2. **Enrollment Key Exposure**
   - **Risk**: Revealing self-enrollment passwords
   - **Mitigation**:
     - Only show to course managers
     - Mask in responses
     - Separate capability check

3. **User Privacy Violations**
   - **Risk**: Exposing other users' enrollment data
   - **Mitigation**:
     - Only return requesting user's data
     - Aggregate counts only
     - GDPR compliance

4. **Timing/Size Attacks**
   - **Risk**: Inferring course content via response analysis
   - **Mitigation**:
     - Consistent response structure
     - Padded responses
     - Rate limiting

#### Security Controls

```php
// Multi-level access control
$can_access = false;
if (is_enrolled($context, $USER->id, '', true)) {
    $can_access = true;
} else if (has_capability('moodle/course:view', $context)) {
    $can_access = true;
} else if ($course->visible && $CFG->allowguestaccess) {
    $can_access = true;
}

if (!$can_access) {
    throw new moodle_exception('nopermissions');
}

// Filter sensitive data based on role
if (!has_capability('moodle/course:update', $context)) {
    unset($result['enrollment_methods']);
    foreach ($result['enrollment_methods'] as &$method) {
        unset($method['enrollment_key']);
    }
}

// Privacy protection
$result['user_enrollment'] = get_user_specific_data($USER->id, $course->id);
// Never include other users' personal data
```

## Cross-Cutting Security Concerns

### 1. Authentication & Authorization

```php
// JWT Token Validation
try {
    $payload = jwt::decode($token);
    if ($payload->exp < time()) {
        throw new moodle_exception('tokenexpired');
    }
    $user = $DB->get_record('user', ['id' => $payload->userid]);
    complete_user_login($user);
} catch (Exception $e) {
    send_error('Invalid authentication token', 401);
}

// Capability checking pattern
function check_course_capability($capability, $context) {
    if (!has_capability($capability, $context)) {
        $event = \core\event\capability_denied::create([
            'context' => $context,
            'other' => ['capability' => $capability]
        ]);
        $event->trigger();
        throw new required_capability_exception($context, $capability);
    }
}
```

### 2. Input Validation & Sanitization

```php
// Comprehensive input validation
function validate_course_input($input) {
    $errors = [];
    
    // Required field validation
    if (empty($input['fullname'])) {
        $errors[] = 'fullname is required';
    }
    
    // Length validation
    if (strlen($input['shortname']) > 255) {
        $errors[] = 'shortname too long';
    }
    
    // Pattern validation
    if (!preg_match('/^[A-Z0-9_-]+$/i', $input['shortname'])) {
        $errors[] = 'shortname contains invalid characters';
    }
    
    // Numeric validation
    if ($input['numsections'] < 0 || $input['numsections'] > 52) {
        $errors[] = 'numsections out of range';
    }
    
    if (!empty($errors)) {
        throw new invalid_parameter_exception(implode(', ', $errors));
    }
}
```

### 3. Output Encoding

```php
// Context-aware output encoding
function encode_course_output($data, $context) {
    // HTML entities for text fields
    $data['fullname'] = s($data['fullname']);
    $data['shortname'] = s($data['shortname']);
    
    // Format HTML content safely
    $data['summary'] = format_text(
        $data['summary'],
        $data['summaryformat'],
        ['context' => $context]
    );
    
    // URL encoding for links
    $data['url'] = clean_param($data['url'], PARAM_URL);
    
    return $data;
}
```

### 4. Rate Limiting

```php
// API rate limiting implementation
function check_api_rate_limit($userid, $endpoint, $limit = 100, $window = 3600) {
    global $DB;
    
    $since = time() - $window;
    $count = $DB->count_records_select(
        'api_requests',
        'userid = ? AND endpoint = ? AND timecreated > ?',
        [$userid, $endpoint, $since]
    );
    
    if ($count >= $limit) {
        header('X-RateLimit-Limit: ' . $limit);
        header('X-RateLimit-Remaining: 0');
        header('X-RateLimit-Reset: ' . (time() + $window));
        throw new moodle_exception('ratelimitexceeded');
    }
    
    // Log this request
    $DB->insert_record('api_requests', [
        'userid' => $userid,
        'endpoint' => $endpoint,
        'timecreated' => time()
    ]);
}
```

## Security Headers

```php
// Recommended security headers for API responses
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
header('Content-Security-Policy: default-src \'none\'; frame-ancestors \'none\'');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
```

## Audit Logging

```php
// Comprehensive audit logging
function log_api_action($action, $objectid, $data = []) {
    global $USER;
    
    $event = \local_courseapi\event\api_action::create([
        'context' => context_system::instance(),
        'objectid' => $objectid,
        'other' => array_merge($data, [
            'action' => $action,
            'endpoint' => $_SERVER['REQUEST_URI'],
            'method' => $_SERVER['REQUEST_METHOD'],
            'ip' => getremoteaddr(),
            'useragent' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ])
    ]);
    $event->trigger();
}
```

## Security Testing Checklist

### 1. Authentication Tests
- [ ] Invalid token rejection
- [ ] Expired token handling
- [ ] Token tampering detection
- [ ] Missing token response

### 2. Authorization Tests
- [ ] Capability enforcement
- [ ] Cross-category access
- [ ] Role-based filtering
- [ ] Guest access prevention

### 3. Input Validation Tests
- [ ] SQL injection attempts
- [ ] XSS payloads
- [ ] Buffer overflow attempts
- [ ] Unicode/encoding attacks
- [ ] Null byte injection

### 4. Business Logic Tests
- [ ] Race conditions
- [ ] State manipulation
- [ ] Replay attacks
- [ ] Parameter pollution

### 5. Error Handling Tests
- [ ] Information leakage
- [ ] Stack trace exposure
- [ ] Timing analysis
- [ ] Error-based enumeration

## Compliance Considerations

### GDPR Compliance
- User data minimization
- Purpose limitation
- Data retention policies
- Right to erasure support

### Accessibility
- WCAG 2.1 compliance for error messages
- Consistent error formats
- Meaningful error descriptions

### Privacy
- No unnecessary data exposure
- Anonymized analytics
- Secure data transmission
- Encrypted sensitive fields

## Recommendations

1. **Implement API Gateway**
   - Centralized authentication
   - Rate limiting
   - Request/response logging
   - DDoS protection

2. **Security Monitoring**
   - Real-time threat detection
   - Anomaly detection
   - Failed authentication tracking
   - Suspicious pattern alerts

3. **Regular Security Audits**
   - Quarterly penetration testing
   - Code security reviews
   - Dependency scanning
   - Compliance verification

4. **Incident Response Plan**
   - Clear escalation procedures
   - Automated alerting
   - Forensic capabilities
   - Recovery procedures