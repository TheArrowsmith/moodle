### **Feature 2: Automated Unit Test Grading**

*   **Objective:** To extend the Code Sandbox to automatically grade a student's code against a predefined set of tests, providing instant, private feedback.
*   **User Story:** "As an instructor, I want to attach a set of hidden unit tests to a coding assignment, so student submissions are graded automatically and accurately, saving me time."
*   **Acceptance Criteria:**
    1.  The "Code Sandbox" activity settings must include a file upload field for an instructor's Python `unittest` file.
    2.  When a student submits code, it is executed against the instructor's tests.
    3.  The results (e.g., "3/5 tests passed") and specific error messages are displayed only to that student.
    4.  A numerical grade (e.g., 60%) is calculated and automatically sent to the Moodle Gradebook.

*   **Technical Specification:**
    *   **Moodle Component:** Enhancements to `mod_codesandbox`.
    *   **Database:** Add columns to `mdl_codesandbox`: `test_suite_path` (VARCHAR), `is_gradable` (BOOLEAN).
    *   **Backend:** Extend the FastAPI microservice.
        *   **API Endpoint:** `POST /grade`
        *   **Request Body:** `{"student_code": "...", "test_code": "..."}`
        *   **Response Body:** `{"score": 0.6, "total_tests": 5, "passed_tests": 3, "results": [{"test_name": "test_add_positive", "passed": true}, ...]}`
    *   **Grading Logic:** The FastAPI service will:
        1.  Create two files inside the temporary Docker container: `solution.py` (from `student_code`) and `test_solution.py` (from `test_code`).
        2.  Execute `python -m unittest test_solution.py`.
        3.  Parse the `stderr` of the `unittest` output to identify which tests passed and failed.
        4.  Calculate the score and construct the JSON response.
    *   **Gradebook Integration:** The `mod_codesandbox` plugin will use Moodle's Gradebook API (`grade_update`) to save the score after receiving a successful response from the `/grade` endpoint.

# Implementation Plan

## Overview
This feature extends the Code Sandbox module to automatically grade student code submissions against instructor-provided unit tests, with results immediately synced to the Moodle gradebook.

## Existing Code Analysis

### Relevant Database Tables
1. **Grade-Related Tables**:
   - `mdl_grade_items` - Defines gradable activities
   - `mdl_grade_grades` - Stores actual student grades
   - `mdl_grade_categories` - Grade organization structure
   
2. **File Storage Tables**:
   - `mdl_files` - File metadata and storage
   - `mdl_files_reference` - External file references

3. **Activity Tables** (to be created):
   - `mdl_codesandbox` - Code sandbox instances
   - Need to add: `test_suite_path`, `is_gradable` columns

### Key Code Components
1. **Grading Integration**:
   - `/lib/gradelib.php` - Core grading API (`grade_update()` function)
   - `/grade/lib.php` - Additional grade utilities
   - `/mod/assign/lib.php` - Example of grade integration pattern

2. **File Handling**:
   - `/lib/filelib.php` - File API functions
   - `/lib/formslib.php` - Form elements including filemanager
   - `/repository/lib.php` - File picker integration

3. **Activity Module Structure**:
   - `/mod/codesandbox/mod_form.php` - Settings form (to be created)
   - `/mod/codesandbox/lib.php` - Core functions (to be created)
   - `/mod/codesandbox/view.php` - Student view (to be created)

## Current Code Flow

### Grade Update Flow (from mod_assign)
1. Student submits work
2. Teacher/system evaluates submission
3. Activity calls `grade_update()` with:
   - Source identifier
   - Course and activity details
   - Grade array with userid and rawgrade
   - Item details (max grade, type, etc.)
4. Gradebook API updates `grade_items` and `grade_grades`
5. Grade aggregation recalculates if needed

### File Upload Flow in Activity Settings
1. Form displays filemanager element
2. Files uploaded to draft area
3. `data_preprocessing()` prepares draft files
4. On save, `file_save_draft_area_files()` moves to permanent storage
5. File ID/path stored in activity instance

## Implementation Changes

### Database Schema Changes
```sql
ALTER TABLE mdl_codesandbox ADD COLUMN test_suite_path VARCHAR(255);
ALTER TABLE mdl_codesandbox ADD COLUMN is_gradable TINYINT(1) DEFAULT 0;
ALTER TABLE mdl_codesandbox ADD COLUMN grade_max DECIMAL(10,5) DEFAULT 100.00;
```

### New/Modified Components

1. **Activity Settings Form** (`mod_form.php`):
   ```php
   class mod_codesandbox_mod_form extends moodleform_mod {
       function definition() {
           $mform = $this->_form;
           
           // Standard activity settings
           $this->standard_intro_elements();
           
           // Grading settings section
           $mform->addElement('header', 'gradingsettings', get_string('gradingsettings', 'codesandbox'));
           
           // Enable grading checkbox
           $mform->addElement('checkbox', 'is_gradable', get_string('enablegrading', 'codesandbox'));
           
           // Test suite file upload
           $mform->addElement('filemanager', 'testsuitefiles', 
               get_string('testsuite', 'codesandbox'), null,
               array('subdirs' => 0, 'maxbytes' => $CFG->maxbytes, 
                     'maxfiles' => 1, 'accepted_types' => array('.py')));
           $mform->disabledIf('testsuitefiles', 'is_gradable');
           
           // Maximum grade
           $mform->addElement('text', 'grade_max', get_string('maximumgrade'), array('size' => 5));
           $mform->setType('grade_max', PARAM_FLOAT);
           $mform->setDefault('grade_max', 100);
           $mform->disabledIf('grade_max', 'is_gradable');
           
           // Standard course module elements
           $this->standard_coursemodule_elements();
           $this->add_action_buttons();
       }
       
       function data_preprocessing(&$default_values) {
           // Prepare test suite files for form
           $draftitemid = file_get_submitted_draft_itemid('testsuitefiles');
           file_prepare_draft_area($draftitemid, $this->context->id, 
               'mod_codesandbox', 'testsuite', 0);
           $default_values['testsuitefiles'] = $draftitemid;
       }
   }
   ```

2. **Grade Integration in lib.php**:
   ```php
   function codesandbox_grade_item_update($codesandbox, $grades=null) {
       global $CFG;
       require_once($CFG->libdir.'/gradelib.php');
       
       $params = array('itemname' => $codesandbox->name);
       
       if ($codesandbox->is_gradable) {
           $params['gradetype'] = GRADE_TYPE_VALUE;
           $params['grademax']  = $codesandbox->grade_max;
           $params['grademin']  = 0;
       } else {
           $params['gradetype'] = GRADE_TYPE_NONE;
       }
       
       return grade_update('mod/codesandbox', $codesandbox->course, 'mod', 
                          'codesandbox', $codesandbox->id, 0, $grades, $params);
   }
   
   function codesandbox_update_grades($codesandbox, $userid=0) {
       global $CFG, $DB;
       
       if (!$codesandbox->is_gradable) {
           return codesandbox_grade_item_update($codesandbox);
       }
       
       if ($userid) {
           $grades = codesandbox_get_user_grades($codesandbox, $userid);
       } else {
           $grades = codesandbox_get_all_grades($codesandbox);
       }
       
       return codesandbox_grade_item_update($codesandbox, $grades);
   }
   ```

3. **Submission Processing**:
   ```php
   function codesandbox_process_submission($codesandbox, $userid, $code) {
       global $CFG, $DB;
       
       // Save submission
       $submission = new stdClass();
       $submission->codesandboxid = $codesandbox->id;
       $submission->userid = $userid;
       $submission->code = $code;
       $submission->timesubmitted = time();
       
       if ($codesandbox->is_gradable) {
           // Get test suite content
           $fs = get_file_storage();
           $files = $fs->get_area_files(context_module::instance($codesandbox->cmid)->id,
                                        'mod_codesandbox', 'testsuite', 0);
           $testcode = '';
           foreach ($files as $file) {
               if (!$file->is_directory()) {
                   $testcode = $file->get_content();
                   break;
               }
           }
           
           // Call grading microservice
           $response = codesandbox_call_grading_api($code, $testcode);
           
           // Update submission with results
           $submission->score = $response->score;
           $submission->feedback = json_encode($response->results);
           
           // Update gradebook
           $grade = new stdClass();
           $grade->userid = $userid;
           $grade->rawgrade = $response->score * $codesandbox->grade_max;
           $grade->feedback = codesandbox_format_test_results($response->results);
           $grade->feedbackformat = FORMAT_HTML;
           $grade->datesubmitted = time();
           $grade->dategraded = time();
           
           codesandbox_grade_item_update($codesandbox, array($userid => $grade));
       }
       
       $DB->insert_record('codesandbox_submissions', $submission);
       return $submission;
   }
   ```

4. **API Integration**:
   ```php
   function codesandbox_call_grading_api($studentcode, $testcode) {
       global $CFG;
       
       $apiurl = $CFG->codesandbox_api_url . '/grade';
       
       $postdata = json_encode(array(
           'student_code' => $studentcode,
           'test_code' => $testcode
       ));
       
       $ch = curl_init($apiurl);
       curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
       
       $response = curl_exec($ch);
       curl_close($ch);
       
       return json_decode($response);
   }
   ```

### FastAPI Microservice Changes
1. **New `/grade` endpoint**:
   - Accept student code and test code
   - Create temporary files in Docker container
   - Run `python -m unittest` and capture output
   - Parse results to identify passed/failed tests
   - Return score and detailed results

2. **Response format**:
   ```json
   {
       "score": 0.6,
       "total_tests": 5,
       "passed_tests": 3,
       "results": [
           {"test_name": "test_add_positive", "passed": true, "message": ""},
           {"test_name": "test_add_negative", "passed": false, "message": "AssertionError..."}
       ]
   }
   ```

### UI Components

1. **Teacher View** (edit form):
   - Checkbox to enable grading
   - File upload for Python unittest file
   - Maximum grade setting

2. **Student View** (submission page):
   - Code editor for submission
   - Submit button
   - Results display showing:
     - Overall score (e.g., "3/5 tests passed - 60%")
     - Individual test results with pass/fail status
     - Error messages for failed tests

3. **Gradebook Integration**:
   - Automatic grade sync on submission
   - Detailed feedback in gradebook
   - Support for regrading

## Security Considerations
1. **File Validation**: Ensure uploaded test files are valid Python
2. **Sandboxing**: Test execution must be isolated in Docker
3. **Resource Limits**: Prevent infinite loops or excessive resource usage
4. **Access Control**: Only teachers can upload test suites
5. **Data Sanitization**: Escape all output from test execution

## Development Checklist
1. [ ] Create database schema additions
2. [ ] Implement mod_form.php with file upload
3. [ ] Add file handling in lib.php
4. [ ] Implement grade_update integration
5. [ ] Create submission processing logic
6. [ ] Build API client for grading service
7. [ ] Update FastAPI service with /grade endpoint
8. [ ] Create result formatting functions
9. [ ] Add student submission interface
10. [ ] Test gradebook synchronization
11. [ ] Add capability checks
12. [ ] Implement backup/restore support

## Testing Approach
1. Test file upload and storage
2. Verify API communication with microservice
3. Test various test suite scenarios (all pass, all fail, mixed)
4. Verify grade calculations and gradebook updates
5. Test edge cases (syntax errors, infinite loops)
6. Load test with concurrent submissions
7. Test backup and restore of activities with test suites
