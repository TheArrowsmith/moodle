# Coding Progress Report Testing Guide

This guide provides detailed instructions for testing the instructor progress dashboard feature, including how to generate test data and verify all functionality.

## Prerequisites

- Teacher account with access to a course
- Student accounts enrolled in the course
- Code Sandbox activities created in the course

## 1. Setting Up Test Data

### 1.1 Create Test Student Accounts

```sql
-- Connect to MySQL (MAMP)
mysql -h localhost -P 8889 -u root -proot moodle

-- Check existing test students
SELECT id, username, firstname, lastname FROM mdl_user 
WHERE username LIKE 'student%' ORDER BY username;
```

Or create students through Moodle UI:
1. Login as admin
2. Go to Site administration → Users → Add a new user
3. Create several test students:
   - Username: student1, student2, student3, etc.
   - Password: Student123!
   - Email: student1@example.com, etc.

### 1.2 Enroll Students in Course

1. Navigate to your course
2. Go to Participants → Enroll users
3. Enroll all test students as "Student" role

### 1.3 Create Code Sandbox Activities

1. Turn editing on in the course
2. Add several Code Sandbox activities:
   - "Basic HTML Exercise" (Max grade: 100)
   - "JavaScript Functions" (Max grade: 100)
   - "CSS Styling Challenge" (Max grade: 100)
   - "DOM Manipulation" (Max grade: 100)

## 2. Generating Test Submissions

### 2.1 Manual Submission Method

1. Log out and log in as a student
2. Navigate to a Code Sandbox activity
3. Write some code and submit
4. Repeat for different students and activities

### 2.2 Database Submission Method (Faster)

```sql
-- Connect to MySQL
mysql -h localhost -P 8889 -u root -proot moodle

-- View existing Code Sandbox activities
SELECT id, course, name, grade_max FROM mdl_codesandbox WHERE course = 2;

-- View enrolled students
SELECT u.id, u.username, u.firstname, u.lastname 
FROM mdl_user u
JOIN mdl_user_enrolments ue ON u.id = ue.userid
JOIN mdl_enrol e ON ue.enrolid = e.id
WHERE e.courseid = 2 AND u.username LIKE 'student%';

-- Insert test submissions with varying grades
-- Replace IDs based on your actual data
INSERT INTO mdl_codesandbox_submissions (codesandboxid, userid, code, score, timesubmitted, timegraded) VALUES
-- Student 1 - High performer (80-100%)
(1, 5, '<html><body>Test HTML</body></html>', 0.95, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, 5, 'function test() { return true; }', 0.88, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(3, 5, 'body { color: red; }', 0.92, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(4, 5, 'document.getElementById("test");', 0.85, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),

-- Student 2 - Average performer (60-80%)
(1, 6, '<html>Basic HTML</html>', 0.75, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, 6, 'function() { }', 0.65, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(3, 6, 'color: blue;', 0.70, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
-- Student 2 hasn't submitted activity 4

-- Student 3 - Low performer (40-60%)
(1, 7, '<html></html>', 0.45, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
(2, 7, 'function', 0.50, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
-- Student 3 hasn't submitted activities 3 and 4

-- Student 4 - Mixed performance
(1, 8, '<html><head><title>Good</title></head></html>', 0.90, UNIX_TIMESTAMP(), UNIX_TIMESTAMP()),
-- Student 4 only submitted first activity

-- Student 5 - No submissions yet
-- (No records for user ID 9)
;

-- Verify submissions
SELECT 
    u.username,
    cs.name as activity,
    ROUND(sub.score * cs.grade_max, 2) as grade,
    FROM_UNIXTIME(sub.timesubmitted) as submitted
FROM mdl_codesandbox_submissions sub
JOIN mdl_user u ON sub.userid = u.id
JOIN mdl_codesandbox cs ON sub.codesandboxid = cs.id
WHERE cs.course = 2
ORDER BY u.username, cs.name;
```

### 2.3 Generate Varied Test Scenarios

```sql
-- Update some grades to create variety
UPDATE mdl_codesandbox_submissions 
SET score = 0.95 
WHERE userid = 5 AND codesandboxid = 1;

-- Add a zero score
UPDATE mdl_codesandbox_submissions 
SET score = 0 
WHERE userid = 7 AND codesandboxid = 1;

-- Add some NULL scores (submitted but not graded)
INSERT INTO mdl_codesandbox_submissions (codesandboxid, userid, code, score, timesubmitted) VALUES
(3, 7, 'body {}', NULL, UNIX_TIMESTAMP());
```

## 3. Testing the Report Features

### 3.1 Access Methods

1. **Via Block**:
   - Add "Coding Progress Report" block to course
   - Test both "Standalone" and "Integrated" versions

2. **Direct URL**:
   - Standalone: `/report/codeprogress/standalone.php?course=2`
   - Integrated: `/report/codeprogress/index.php?course=2`

3. **Via Reports Menu** (if visible):
   - Course → Reports → Coding Progress

### 3.2 Features to Test

#### A. Data Display
- [ ] Student names appear correctly
- [ ] All Code Sandbox activities show as columns
- [ ] Grades display with correct color coding:
  - Green: 80-100%
  - Yellow: 60-79%
  - Red: Below 60%
- [ ] Non-submitted shows as "-"
- [ ] Average row calculates correctly

#### B. Statistics Cards
- [ ] Total Students count is accurate
- [ ] Assignments count matches activities
- [ ] Average Score calculation is correct
- [ ] Completion Rate percentage is accurate

#### C. Charts
- [ ] Bar chart shows average scores per assignment
- [ ] Pie chart shows submission vs non-submission ratio
- [ ] Charts are responsive and display tooltips

#### D. CSV Export
- [ ] Export button downloads CSV file
- [ ] CSV contains all students and grades
- [ ] File naming includes course and date

### 3.3 Performance Testing

```sql
-- Generate bulk test data
DELIMITER //
CREATE PROCEDURE generate_bulk_submissions()
BEGIN
    DECLARE i INT DEFAULT 10;
    DECLARE j INT DEFAULT 1;
    DECLARE score DECIMAL(3,2);
    
    -- Create 50 test students if needed
    WHILE i <= 60 DO
        -- Random score between 0.4 and 1.0
        SET score = 0.4 + (RAND() * 0.6);
        
        -- Insert submissions for each activity
        WHILE j <= 4 DO
            -- 80% chance of submission
            IF RAND() > 0.2 THEN
                INSERT IGNORE INTO mdl_codesandbox_submissions 
                (codesandboxid, userid, code, score, timesubmitted, timegraded) 
                VALUES (j, i, 'Test code', score, UNIX_TIMESTAMP(), UNIX_TIMESTAMP());
            END IF;
            SET j = j + 1;
        END WHILE;
        
        SET j = 1;
        SET i = i + 1;
    END WHILE;
END//
DELIMITER ;

-- Run the procedure
CALL generate_bulk_submissions();
```

## 4. Edge Cases to Test

### 4.1 No Data Scenarios
- [ ] Course with no Code Sandbox activities
- [ ] Course with activities but no students
- [ ] Course with students but no submissions

### 4.2 Partial Data Scenarios
- [ ] Students with only some activities submitted
- [ ] Activities with no submissions from any student
- [ ] Submissions with NULL scores (not graded)

### 4.3 Access Control
- [ ] Teacher can view report
- [ ] Student cannot access report (permission denied)
- [ ] Admin can view any course report

## 5. Troubleshooting Common Issues

### Issue: "Web service is not available"
**Solution**: The integrated version bypasses web services by loading data directly.

### Issue: JavaScript errors in console
**Test**: 
1. Open browser console (F12)
2. Navigate to report
3. Check for errors
4. Standalone version should have NO Moodle JS errors

### Issue: Charts not displaying
**Check**:
1. Chart.js is loaded (check Network tab)
2. Canvas elements exist in DOM
3. No JavaScript errors in console

### Issue: Missing students or grades
**Verify**:
```sql
-- Check enrollments
SELECT COUNT(*) FROM mdl_user u
JOIN mdl_user_enrolments ue ON u.id = ue.userid
JOIN mdl_enrol e ON ue.enrolid = e.id
WHERE e.courseid = 2 AND ue.status = 0;

-- Check submissions
SELECT COUNT(*) FROM mdl_codesandbox_submissions sub
JOIN mdl_codesandbox cs ON sub.codesandboxid = cs.id
WHERE cs.course = 2;
```

## 6. Automated Testing Script

Create a file `test_report.php` in course folder:

```php
<?php
require_once('../../config.php');
require_once($CFG->dirroot . '/local/customapi/classes/external.php');

$courseid = 2; // Your test course ID

// Test data retrieval
try {
    $data = local_customapi_external::get_sandbox_grades($courseid);
    echo "Retrieved " . count($data) . " grade records\n";
    
    // Verify data structure
    if (!empty($data)) {
        $record = $data[0];
        $required_fields = ['userid', 'username', 'fullname', 'activityid', 
                          'activityname', 'cmid', 'submissionstatus'];
        foreach ($required_fields as $field) {
            if (!isset($record->$field)) {
                echo "ERROR: Missing field $field\n";
            }
        }
        echo "Data structure verified\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
```

Run with: `php test_report.php`

## 7. User Acceptance Testing

### For Teachers
1. Can you easily find and access the report?
2. Does the data match your gradebook?
3. Are the visualizations helpful?
4. Is the CSV export useful?
5. Does it load quickly with many students?

### For Administrators
1. Can you see reports for all courses?
2. Are permissions working correctly?
3. Is the feature discoverable?

## 8. Regression Testing

After any changes, verify:
- [ ] Both standalone and integrated versions work
- [ ] No new JavaScript errors introduced
- [ ] CSV export still functions
- [ ] Charts render correctly
- [ ] Mobile responsive view works

## Notes

- The standalone version (`standalone.php`) is more reliable if there are Moodle JavaScript issues
- The integrated version (`index.php`) provides better navigation integration
- Test with different numbers of students and activities to ensure scalability
- Consider browser testing (Chrome, Firefox, Safari, Edge)