### **Feature 5: Instructor Progress Dashboard**

*   **Objective:** To provide instructors with an at-a-glance visual summary of student progress on coding assignments.
*   **User Story:** "As an instructor, I need a single dashboard page that shows me who has completed each coding assignment and what their score was, so I can quickly identify students who are falling behind."
*   **Acceptance Criteria:**
    1.  A new "Coding Progress Report" link is available in the course "Reports" section.
    2.  The page displays a grid or table with students listed in rows and "Code Sandbox" activities in columns.
    3.  Each cell in the grid displays the student's grade for that activity.
    4.  The data on the page is loaded dynamically via the API created in Feature 4.

*   **Technical Specification:**
    *   **Moodle Component:** New Report Plugin (`report_progress`).
    *   **Frontend:** The report's `index.php` will be a lightweight page. Most of the logic will be in JavaScript.
        *   On page load, a JavaScript function makes an AJAX call to the Moodle web service endpoint from Feature 4, using a pre-generated token.
        *   The JavaScript then dynamically builds an HTML table from the JSON response.
        *   Use a simple library like [Chart.js](https://www.chartjs.org/) for an optional visual summary (e.g., a bar chart of average scores per assignment).

# Implementation Plan

## Overview
This feature creates a visual dashboard for instructors to monitor student progress on coding assignments, displaying grades in a grid format with optional visualizations using Chart.js.

## Existing Code Analysis

### Relevant Database Tables
1. **Core Tables** (accessed via API):
   - `mdl_user` - Student information
   - `mdl_course` - Course details
   - `mdl_grade_items` - Gradable activities
   - `mdl_grade_grades` - Student grades
   - `mdl_course_modules` - Course activities

2. **Report Tracking**:
   - `mdl_logstore_standard_log` - Report viewing events

### Key Code Components
1. **Report Framework**:
   - `/report/` - Site-wide reports location
   - `/lib/navigationlib.php` - Navigation integration
   - `/lib/accesslib.php` - Capability checking

2. **JavaScript/AJAX**:
   - `/lib/amd/` - AMD JavaScript modules
   - `/lib/ajax/` - AJAX service endpoints
   - `/lib/requirejs.php` - RequireJS configuration

3. **Rendering**:
   - `/lib/outputrenderers.php` - Base renderer classes
   - Templates in `/templates/` directory

## Current Code Flow

### Report Access Flow
1. User navigates to course
2. Report link appears in navigation (if user has capability)
3. Click redirects to `/report/progress/index.php?id={courseid}`
4. Report checks permissions
5. Report loads and displays data

### AJAX Data Loading Pattern
1. Page loads with basic HTML structure
2. JavaScript module initiates
3. AJAX call to web service with authentication
4. Process JSON response
5. Dynamically update DOM

## Implementation Changes

### New Report Plugin Structure
```
/report/progress/
├── version.php              # Plugin version info
├── index.php               # Main report page
├── lib.php                 # Navigation hooks
├── db/
│   ├── access.php          # Capabilities
│   └── services.php        # Web service functions
├── classes/
│   ├── external.php        # External API
│   └── output/
│       └── renderer.php    # Output renderer
├── amd/
│   ├── src/
│   │   └── dashboard.js    # ES6 JavaScript module
│   └── build/
│       └── dashboard.min.js # Compiled JS
├── templates/
│   └── dashboard.mustache  # Main template
├── styles.css              # Custom styles
└── lang/en/
    └── report_progress.php # Language strings
```

### Database Changes
No new tables required - uses existing grade data via API.

### Implementation Components

1. **Plugin Version** (`version.php`):
   ```php
   $plugin->component = 'report_progress';
   $plugin->version = 2023100100;
   $plugin->requires = 2018051700; // Moodle 3.5
   $plugin->maturity = MATURITY_STABLE;
   $plugin->release = '1.0';
   ```

2. **Capability Definition** (`db/access.php`):
   ```php
   $capabilities = array(
       'report/progress:view' => array(
           'riskbitmask' => RISK_PERSONAL,
           'captype' => 'read',
           'contextlevel' => CONTEXT_COURSE,
           'archetypes' => array(
               'teacher' => CAP_ALLOW,
               'editingteacher' => CAP_ALLOW,
               'manager' => CAP_ALLOW
           )
       )
   );
   ```

3. **Navigation Hook** (`lib.php`):
   ```php
   function report_progress_extend_navigation_course($navigation, $course, $context) {
       if (has_capability('report/progress:view', $context)) {
           $url = new moodle_url('/report/progress/index.php', array('course' => $course->id));
           $navigation->add(get_string('pluginname', 'report_progress'), 
               $url, navigation_node::TYPE_SETTING, null, null, 
               new pix_icon('i/report', ''));
       }
   }
   ```

4. **Main Report Page** (`index.php`):
   ```php
   require_once('../../config.php');
   require_once($CFG->libdir.'/adminlib.php');
   
   $courseid = required_param('course', PARAM_INT);
   $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);
   
   require_login($course);
   $context = context_course::instance($course->id);
   require_capability('report/progress:view', $context);
   
   $PAGE->set_url('/report/progress/index.php', array('course' => $courseid));
   $PAGE->set_title(get_string('progressreport', 'report_progress'));
   $PAGE->set_heading($course->fullname);
   $PAGE->set_pagelayout('report');
   
   // Include JavaScript module
   $PAGE->requires->js_call_amd('report_progress/dashboard', 'init', array(
       'courseid' => $courseid,
       'wstoken' => $USER->sesskey // Or proper web service token
   ));
   
   echo $OUTPUT->header();
   echo $OUTPUT->heading(get_string('codingprogress', 'report_progress'));
   
   // Render template with placeholder
   $templatecontext = array(
       'courseid' => $courseid
   );
   echo $OUTPUT->render_from_template('report_progress/dashboard', $templatecontext);
   
   echo $OUTPUT->footer();
   ```

5. **Dashboard Template** (`templates/dashboard.mustache`):
   ```html
   <div id="progress-dashboard-container">
       <div class="alert alert-info" id="loading-message">
           <i class="fa fa-spinner fa-spin"></i> {{#str}}loadingdata, report_progress{{/str}}
       </div>
       <div id="progress-table-container" style="display: none;">
           <table class="table table-striped table-hover" id="progress-table">
               <thead>
                   <tr id="table-header">
                       <th>{{#str}}student, core{{/str}}</th>
                       <!-- Assignment columns added dynamically -->
                   </tr>
               </thead>
               <tbody id="table-body">
                   <!-- Rows added dynamically -->
               </tbody>
           </table>
       </div>
       <div id="chart-container" style="display: none; margin-top: 30px;">
           <h3>{{#str}}averagescores, report_progress{{/str}}</h3>
           <canvas id="progress-chart" width="400" height="200"></canvas>
       </div>
       <div class="alert alert-danger" id="error-message" style="display: none;">
           <i class="fa fa-exclamation-triangle"></i> <span id="error-text"></span>
       </div>
   </div>
   ```

6. **JavaScript Module** (`amd/src/dashboard.js`):
   ```javascript
   define(['jquery', 'core/ajax', 'core/notification', 'report_progress/chartjs'], 
   function($, Ajax, Notification, Chart) {
       return {
           init: function(courseid, wstoken) {
               this.courseid = courseid;
               this.wstoken = wstoken;
               this.loadProgressData();
           },
           
           loadProgressData: function() {
               var self = this;
               
               // Call the custom API from Feature 4
               var promises = Ajax.call([{
                   methodname: 'local_customapi_get_sandbox_grades',
                   args: {courseid: this.courseid}
               }]);
               
               promises[0].done(function(response) {
                   self.processData(response);
                   self.renderTable(response);
                   self.renderChart(response);
                   $('#loading-message').hide();
                   $('#progress-table-container, #chart-container').show();
               }).fail(function(ex) {
                   Notification.exception(ex);
                   $('#loading-message').hide();
                   $('#error-message').show();
                   $('#error-text').text(ex.message);
               });
           },
           
           processData: function(data) {
               // Transform flat data into structured format
               this.students = {};
               this.activities = {};
               
               data.forEach(function(record) {
                   if (!this.students[record.userid]) {
                       this.students[record.userid] = {
                           id: record.userid,
                           name: record.username,
                           grades: {}
                       };
                   }
                   
                   if (!this.activities[record.activityid]) {
                       this.activities[record.activityid] = {
                           id: record.activityid,
                           name: record.activityname
                       };
                   }
                   
                   this.students[record.userid].grades[record.activityid] = {
                       grade: record.grade,
                       status: record.submissionstatus
                   };
               }, this);
           },
           
           renderTable: function(data) {
               var $header = $('#table-header');
               var $body = $('#table-body');
               
               // Add activity columns to header
               Object.values(this.activities).forEach(function(activity) {
                   $header.append('<th>' + activity.name + '</th>');
               });
               
               // Add student rows
               Object.values(this.students).forEach(function(student) {
                   var $row = $('<tr>');
                   $row.append('<td>' + student.name + '</td>');
                   
                   Object.values(this.activities).forEach(function(activity) {
                       var grade = student.grades[activity.id];
                       if (grade) {
                           var cellClass = this.getGradeClass(grade.grade);
                           $row.append('<td class="' + cellClass + '">' + 
                                      grade.grade + '%</td>');
                       } else {
                           $row.append('<td class="text-muted">-</td>');
                       }
                   }, this);
                   
                   $body.append($row);
               }, this);
           },
           
           renderChart: function(data) {
               var ctx = document.getElementById('progress-chart').getContext('2d');
               
               // Calculate average scores per activity
               var labels = [];
               var averages = [];
               
               Object.values(this.activities).forEach(function(activity) {
                   labels.push(activity.name);
                   
                   var scores = [];
                   Object.values(this.students).forEach(function(student) {
                       if (student.grades[activity.id]) {
                           scores.push(student.grades[activity.id].grade);
                       }
                   });
                   
                   var avg = scores.length ? 
                       scores.reduce((a, b) => a + b) / scores.length : 0;
                   averages.push(avg.toFixed(1));
               });
               
               new Chart(ctx, {
                   type: 'bar',
                   data: {
                       labels: labels,
                       datasets: [{
                           label: 'Average Score (%)',
                           data: averages,
                           backgroundColor: 'rgba(54, 162, 235, 0.5)',
                           borderColor: 'rgba(54, 162, 235, 1)',
                           borderWidth: 1
                       }]
                   },
                   options: {
                       scales: {
                           y: {
                               beginAtZero: true,
                               max: 100
                           }
                       }
                   }
               });
           },
           
           getGradeClass: function(grade) {
               if (grade >= 80) return 'text-success';
               if (grade >= 60) return 'text-warning';
               return 'text-danger';
           }
       };
   });
   ```

7. **External Chart.js Integration**:
   - Download Chart.js and place in `amd/src/chartjs.js`
   - Or use CDN with appropriate AMD wrapper

### Security Considerations

1. **Authentication for AJAX**:
   - Use session key for same-session requests
   - Or generate proper web service token
   - Store token securely (not in JavaScript)

2. **Capability Checks**:
   - Verify `report/progress:view` capability
   - Check course context access
   - Filter data based on user permissions

3. **Data Privacy**:
   - Only show data user is authorized to see
   - Log report access for audit trail
   - Implement privacy provider for GDPR

### Styling
```css
/* styles.css */
#progress-dashboard-container {
    margin: 20px 0;
}

#progress-table th {
    background-color: #f8f9fa;
    position: sticky;
    top: 0;
}

#progress-table td {
    text-align: center;
}

.text-success { color: #28a745; }
.text-warning { color: #ffc107; }
.text-danger { color: #dc3545; }

#chart-container {
    max-width: 800px;
    margin: 30px auto;
}
```

## Development Checklist

1. [ ] Create report plugin structure
2. [ ] Define capabilities
3. [ ] Implement navigation hook
4. [ ] Create main report page
5. [ ] Design Mustache template
6. [ ] Write JavaScript dashboard module
7. [ ] Integrate Chart.js library
8. [ ] Implement data processing logic
9. [ ] Add table rendering
10. [ ] Add chart visualization
11. [ ] Apply styling
12. [ ] Add loading/error states
13. [ ] Test with various data sets
14. [ ] Add language strings
15. [ ] Implement privacy provider

## Testing Approach

1. Test capability-based access control
2. Verify navigation integration
3. Test AJAX data loading
4. Test with empty data sets
5. Test with large data sets
6. Verify grade calculations
7. Test chart rendering
8. Test responsive design
9. Cross-browser compatibility
10. Performance with many students/activities
