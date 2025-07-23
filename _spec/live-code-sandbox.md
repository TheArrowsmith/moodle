### **Feature 1: Live Code Sandbox Module**

*   **Objective:** To allow students to write and execute Python code directly within a Moodle course, providing immediate feedback without leaving the learning environment.
*   **User Story:** "As a student, I want to write Python code in a browser editor, click 'Run,' and see the output immediately so I can quickly test my code and ideas."
*   **Acceptance Criteria:**
    1.  Instructors can add a "Code Sandbox" as a new activity to any course section.
    2.  The activity page displays a text editor pre-populated with optional starter code provided by the instructor.
    3.  A "Run Code" button is present.
    4.  When clicked, the code is executed, and the standard output (`stdout`) and standard error (`stderr`) are displayed in a separate, read-only panel on the same page.
    5.  The execution is secure and cannot affect the Moodle server.

*   **Technical Specification:**
    *   **Moodle Component:** New Activity Plugin (`mod_codesandbox`).
    *   **Database:** A new table `mdl_codesandbox` will be created with columns: `id`, `course` (foreign key to `mdl_course`), `name`, `intro` (HTML content), `starter_code` (TEXT).
    *   **Frontend:** The `view.php` of the plugin will use the [CodeMirror](https://codemirror.net/) library for the code editor. A JavaScript module will handle the "Run Code" button's click event, sending the editor's content via an AJAX (Asynchronous JavaScript and XML) request.
    *   **Backend:** A dedicated microservice built with **Python and FastAPI**. This service is decoupled from the Moodle PHP application for security and performance.
        *   **API Endpoint:** `POST /execute`
        *   **Request Body:** `{"code": "print('hello')"}`
        *   **Response Body:** `{"stdout": "hello\n", "stderr": ""}`
    *   **Sandboxing:** The FastAPI service will use the `docker-py` library. On receiving a request, it will:
        1.  Create a new, temporary Docker container from a minimal `python:3.8-slim` image.
        2.  Copy the user's code into the container.
        3.  Execute the script (`python script.py`).
        4.  Capture the `stdout` and `stderr` streams.
        5.  Destroy the container immediately after execution.

# Implementation Plan

## Overview
This feature creates a new Moodle activity module that allows students to write and execute Python code directly in the browser with real-time output display, using a secure Docker-based execution environment.

## Existing Code Analysis

### Relevant Database Tables
1. **Core Activity Tables** (to be created):
   - `mdl_codesandbox` - Main activity instances
   - `mdl_codesandbox_submissions` - Student code submissions (optional)

2. **Framework Tables**:
   - `mdl_course_modules` - Links activities to courses
   - `mdl_course_sections` - Course structure
   - `mdl_context` - Permission contexts
   - `mdl_log` - Activity logging

### Key Code Components
1. **Activity Module Framework**:
   - `/mod/` - Activity modules location
   - `/lib/modinfolib.php` - Module information
   - `/course/modedit.php` - Activity creation/editing

2. **JavaScript/AJAX Framework**:
   - AMD modules in `/lib/amd/`
   - Core AJAX service `/lib/ajax/service.php`
   - Template rendering system

3. **Similar Modules for Reference**:
   - `/mod/assign/` - Complex activity with submissions
   - `/mod/page/` - Simple content display
   - `/mod/quiz/` - Real-time interaction patterns

## Current Code Flow

### Activity Creation Flow
1. Teacher clicks "Add activity" → "Code Sandbox"
2. `mod_form.php` displays configuration form
3. Form submission calls `codesandbox_add_instance()`
4. Database record created in `mdl_codesandbox`
5. Course cache cleared, activity appears in course

### Activity Display Flow
1. Student clicks activity link
2. `view.php` loads with course/context checks
3. Page includes JavaScript modules
4. CodeMirror editor initialized
5. Run button triggers AJAX to external service

## Implementation Changes

### Module Structure
```
/mod/codesandbox/
├── version.php                 # Module version info
├── lib.php                    # Required functions
├── mod_form.php               # Settings form
├── view.php                   # Student view
├── index.php                  # Course activity list
├── db/
│   ├── install.xml            # Database schema
│   ├── access.php             # Capabilities
│   ├── services.php           # Web services (if needed)
│   └── upgrade.php            # Future upgrades
├── lang/en/
│   └── codesandbox.php        # Language strings
├── amd/
│   ├── src/
│   │   └── editor.js          # ES6 source
│   └── build/
│       └── editor.min.js      # Compiled JS
├── templates/
│   └── view.mustache          # Main view template
├── styles.css                 # Custom styles
├── pix/
│   └── icon.svg              # Activity icon
└── backup/moodle2/            # Backup/restore support
```

### Database Schema (`db/install.xml`)
```xml
<?xml version="1.0" encoding="UTF-8" ?>
<XMLDB PATH="mod/codesandbox/db" VERSION="20231001" COMMENT="Code Sandbox module">
  <TABLES>
    <TABLE NAME="codesandbox" COMMENT="Code sandbox instances">
      <FIELDS>
        <FIELD NAME="id" TYPE="int" LENGTH="10" NOTNULL="true" SEQUENCE="true"/>
        <FIELD NAME="course" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="0"/>
        <FIELD NAME="name" TYPE="char" LENGTH="255" NOTNULL="true"/>
        <FIELD NAME="intro" TYPE="text" NOTNULL="false"/>
        <FIELD NAME="introformat" TYPE="int" LENGTH="4" NOTNULL="true" DEFAULT="0"/>
        <FIELD NAME="starter_code" TYPE="text" NOTNULL="false"/>
        <FIELD NAME="timecreated" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="0"/>
        <FIELD NAME="timemodified" TYPE="int" LENGTH="10" NOTNULL="true" DEFAULT="0"/>
      </FIELDS>
      <KEYS>
        <KEY NAME="primary" TYPE="primary" FIELDS="id"/>
        <KEY NAME="course" TYPE="foreign" FIELDS="course" REFTABLE="course" REFFIELDS="id"/>
      </KEYS>
    </TABLE>
  </TABLES>
</XMLDB>
```

### Core Implementation Files

1. **Module Library** (`lib.php`):
   ```php
   function codesandbox_add_instance($codesandbox) {
       global $DB;
       
       $codesandbox->timecreated = time();
       $codesandbox->timemodified = time();
       
       $codesandbox->id = $DB->insert_record('codesandbox', $codesandbox);
       
       return $codesandbox->id;
   }
   
   function codesandbox_update_instance($codesandbox) {
       global $DB;
       
       $codesandbox->timemodified = time();
       $codesandbox->id = $codesandbox->instance;
       
       return $DB->update_record('codesandbox', $codesandbox);
   }
   
   function codesandbox_delete_instance($id) {
       global $DB;
       
       if (!$codesandbox = $DB->get_record('codesandbox', array('id' => $id))) {
           return false;
       }
       
       $DB->delete_records('codesandbox', array('id' => $id));
       
       return true;
   }
   ```

2. **Settings Form** (`mod_form.php`):
   ```php
   class mod_codesandbox_mod_form extends moodleform_mod {
       function definition() {
           $mform = $this->_form;
           
           // General settings
           $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
           $mform->setType('name', PARAM_TEXT);
           $mform->addRule('name', null, 'required', null, 'client');
           
           // Introduction
           $this->standard_intro_elements();
           
           // Starter code
           $mform->addElement('textarea', 'starter_code', get_string('startercode', 'codesandbox'), 
                             array('rows' => 10, 'cols' => 80));
           $mform->setType('starter_code', PARAM_RAW);
           $mform->addHelpButton('starter_code', 'startercode', 'codesandbox');
           
           // Standard course module elements
           $this->standard_coursemodule_elements();
           
           $this->add_action_buttons();
       }
   }
   ```

3. **View Page** (`view.php`):
   ```php
   require_once('../../config.php');
   require_once('lib.php');
   
   $id = required_param('id', PARAM_INT); // Course module ID
   
   if (!$cm = get_coursemodule_from_id('codesandbox', $id)) {
       print_error('invalidcoursemodule');
   }
   
   if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
       print_error('coursemissingshort');
   }
   
   if (!$codesandbox = $DB->get_record('codesandbox', array('id' => $cm->instance))) {
       print_error('invalidcodesandboxid', 'codesandbox');
   }
   
   require_login($course, true, $cm);
   $context = context_module::instance($cm->id);
   
   // Log view event
   $event = \mod_codesandbox\event\course_module_viewed::create(array(
       'objectid' => $codesandbox->id,
       'context' => $context
   ));
   $event->trigger();
   
   $PAGE->set_url('/mod/codesandbox/view.php', array('id' => $id));
   $PAGE->set_title(format_string($codesandbox->name));
   $PAGE->set_heading(format_string($course->fullname));
   $PAGE->set_context($context);
   
   // Include CodeMirror
   $PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.css'));
   $PAGE->requires->css(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/theme/monokai.min.css'));
   $PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/codemirror.min.js'), true);
   $PAGE->requires->js(new moodle_url('https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.2/mode/python/python.min.js'), true);
   
   // Include our JavaScript module
   $PAGE->requires->js_call_amd('mod_codesandbox/editor', 'init', array(
       'starterCode' => $codesandbox->starter_code,
       'apiUrl' => $CFG->codesandbox_api_url ?? 'http://localhost:8000'
   ));
   
   echo $OUTPUT->header();
   echo $OUTPUT->heading($codesandbox->name);
   
   if ($codesandbox->intro) {
       echo $OUTPUT->box(format_module_intro('codesandbox', $codesandbox, $cm->id), 
                        'generalbox mod_introbox', 'codesandboxintro');
   }
   
   // Render the template
   $templatecontext = array(
       'id' => $cm->id
   );
   echo $OUTPUT->render_from_template('mod_codesandbox/view', $templatecontext);
   
   echo $OUTPUT->footer();
   ```

4. **View Template** (`templates/view.mustache`):
   ```html
   <div id="codesandbox-container">
       <div class="editor-panel">
           <h3>{{#str}}codeeditor, mod_codesandbox{{/str}}</h3>
           <textarea id="code-editor"></textarea>
           <div class="editor-controls">
               <button id="run-code" class="btn btn-primary">
                   <i class="fa fa-play"></i> {{#str}}runcode, mod_codesandbox{{/str}}
               </button>
               <button id="clear-output" class="btn btn-secondary">
                   <i class="fa fa-eraser"></i> {{#str}}clearoutput, mod_codesandbox{{/str}}
               </button>
           </div>
       </div>
       
       <div class="output-panel">
           <h3>{{#str}}output, mod_codesandbox{{/str}}</h3>
           <div id="output-container">
               <pre id="stdout" class="output-section"></pre>
               <pre id="stderr" class="output-section error"></pre>
           </div>
       </div>
       
       <div id="loading-spinner" style="display: none;">
           <i class="fa fa-spinner fa-spin"></i> {{#str}}executing, mod_codesandbox{{/str}}
       </div>
   </div>
   ```

5. **JavaScript Module** (`amd/src/editor.js`):
   ```javascript
   define(['jquery', 'core/ajax', 'core/notification'], function($, Ajax, Notification) {
       return {
           init: function(starterCode, apiUrl) {
               var editor = CodeMirror.fromTextArea(document.getElementById('code-editor'), {
                   mode: 'python',
                   theme: 'monokai',
                   lineNumbers: true,
                   indentUnit: 4,
                   lineWrapping: true
               });
               
               // Set starter code
               if (starterCode) {
                   editor.setValue(starterCode);
               }
               
               // Handle run button
               $('#run-code').on('click', function() {
                   var code = editor.getValue();
                   $('#loading-spinner').show();
                   $('#run-code').prop('disabled', true);
                   
                   // Call external API
                   $.ajax({
                       url: apiUrl + '/execute',
                       method: 'POST',
                       contentType: 'application/json',
                       data: JSON.stringify({code: code}),
                       success: function(response) {
                           $('#stdout').text(response.stdout || '');
                           $('#stderr').text(response.stderr || '');
                           $('#output-container').show();
                       },
                       error: function(xhr, status, error) {
                           Notification.alert(
                               M.util.get_string('executionerror', 'mod_codesandbox'),
                               error || status
                           );
                       },
                       complete: function() {
                           $('#loading-spinner').hide();
                           $('#run-code').prop('disabled', false);
                       }
                   });
               });
               
               // Handle clear button
               $('#clear-output').on('click', function() {
                   $('#stdout, #stderr').text('');
                   $('#output-container').hide();
               });
           }
       };
   });
   ```

6. **Styles** (`styles.css`):
   ```css
   #codesandbox-container {
       display: flex;
       gap: 20px;
       margin-top: 20px;
   }
   
   .editor-panel, .output-panel {
       flex: 1;
       min-height: 500px;
   }
   
   .CodeMirror {
       height: 400px;
       border: 1px solid #ddd;
   }
   
   .editor-controls {
       margin-top: 10px;
   }
   
   #output-container {
       background-color: #f5f5f5;
       border: 1px solid #ddd;
       border-radius: 4px;
       padding: 10px;
       min-height: 400px;
       font-family: monospace;
   }
   
   .output-section {
       margin: 0;
       white-space: pre-wrap;
   }
   
   .output-section.error {
       color: #d32f2f;
   }
   
   #loading-spinner {
       text-align: center;
       margin: 20px;
       font-size: 1.2em;
   }
   ```

### FastAPI Microservice

The external Python execution service:

```python
# main.py
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import docker
import tempfile
import os
import asyncio

app = FastAPI()

# Configure CORS for Moodle
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure with Moodle URL in production
    allow_methods=["POST"],
    allow_headers=["*"],
)

class CodeRequest(BaseModel):
    code: str

class CodeResponse(BaseModel):
    stdout: str
    stderr: str

@app.post("/execute", response_model=CodeResponse)
async def execute_code(request: CodeRequest):
    client = docker.from_env()
    
    # Create temporary file
    with tempfile.NamedTemporaryFile(mode='w', suffix='.py', delete=False) as f:
        f.write(request.code)
        temp_file = f.name
    
    try:
        # Run in container with timeout
        container = client.containers.run(
            "python:3.8-slim",
            f"python /code/script.py",
            volumes={os.path.dirname(temp_file): {'bind': '/code', 'mode': 'ro'}},
            working_dir="/code",
            mem_limit="128m",
            cpu_quota=50000,  # 50% CPU
            remove=True,
            stdout=True,
            stderr=True,
            timeout=10  # 10 second timeout
        )
        
        return CodeResponse(
            stdout=container.decode('utf-8') if isinstance(container, bytes) else '',
            stderr=""
        )
        
    except docker.errors.ContainerError as e:
        return CodeResponse(
            stdout=e.stdout.decode('utf-8') if e.stdout else '',
            stderr=e.stderr.decode('utf-8') if e.stderr else str(e)
        )
    except Exception as e:
        raise HTTPException(status_code=500, detail=str(e))
    finally:
        os.unlink(temp_file)

# Dockerfile for the API service
"""
FROM python:3.8-slim
WORKDIR /app
COPY requirements.txt .
RUN pip install -r requirements.txt
COPY main.py .
CMD ["uvicorn", "main:app", "--host", "0.0.0.0", "--port", "8000"]
"""
```

## Security Considerations

1. **Docker Isolation**:
   - Containers run with limited resources
   - No network access from containers
   - Read-only code mounting
   - Automatic cleanup after execution

2. **Input Validation**:
   - Code size limits
   - Request rate limiting
   - Authentication tokens (in production)

3. **Moodle Security**:
   - Standard capability checks
   - Context validation
   - CSRF protection for forms

## Development Checklist

1. [ ] Create module boilerplate structure
2. [ ] Define database schema
3. [ ] Implement core lib.php functions
4. [ ] Create settings form
5. [ ] Build view.php page
6. [ ] Design Mustache template
7. [ ] Write JavaScript editor module
8. [ ] Integrate CodeMirror
9. [ ] Implement run button handler
10. [ ] Add CSS styling
11. [ ] Create language strings
12. [ ] Build FastAPI microservice
13. [ ] Implement Docker execution
14. [ ] Add security measures
15. [ ] Test error handling
16. [ ] Add activity completion support
17. [ ] Implement backup/restore

## Testing Approach

1. Test module installation
2. Test activity creation/editing
3. Test various Python code scenarios
4. Test error handling (syntax errors, runtime errors)
5. Test resource limits (infinite loops, memory)
6. Test concurrent executions
7. Test UI responsiveness
8. Cross-browser compatibility
9. Mobile device testing
10. Performance under load
