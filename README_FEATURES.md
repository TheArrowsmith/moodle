# Moodle 3.5 Enhanced Features Implementation

This document describes the six new features implemented for Moodle 3.5 to enhance the programming education experience.

## Table of Contents

1. [Live Code Sandbox Module](#1-live-code-sandbox-module)
2. [Automated Unit Test Grading](#2-automated-unit-test-grading)
3. [GitHub OAuth2 Authentication](#3-github-oauth2-authentication)
4. [API for Assignment Data](#4-api-for-assignment-data)
5. [Instructor Progress Dashboard](#5-instructor-progress-dashboard)
6. [Syntax Highlighting in Forums](#6-syntax-highlighting-in-forums)
7. [Installation Guide](#installation-guide)
8. [Testing Guide](#testing-guide)

## 1. Live Code Sandbox Module

### Overview
A new activity module that allows students to write and execute Python code directly in the browser.

### Features
- CodeMirror editor with syntax highlighting
- Real-time code execution via secure Docker containers
- Support for starter code templates
- Save and resume coding sessions

### Location
- Module: `/mod/codesandbox/`
- API Service: `/codesandbox-api/`

### Usage
1. Add "Code Sandbox" activity to any course
2. Configure starter code (optional)
3. Students can write and run Python code instantly

## 2. Automated Unit Test Grading

### Overview
Extension to Code Sandbox that automatically grades student code against instructor-provided unit tests.

### Features
- Upload Python unittest files
- Automatic grading on submission
- Detailed test results display
- Integration with Moodle gradebook

### Configuration
1. Enable grading in Code Sandbox settings
2. Upload a Python unittest file
3. Set maximum grade
4. Test results automatically sync to gradebook

## 3. GitHub OAuth2 Authentication

### Overview
Allows users to log in using their GitHub accounts via OAuth2.

### Features
- One-click GitHub login
- Automatic account creation for new users
- Profile data synchronization
- Secure OAuth2 implementation

### Setup
1. Create GitHub OAuth App at https://github.com/settings/developers
2. Run setup script: `/admin/tool/oauth2/github_setup.php`
3. Configure Client ID and Secret in OAuth2 services
4. Enable GitHub issuer

### Location
- OAuth2 Client: `/lib/classes/oauth2/client/github.php`
- Setup Script: `/admin/tool/oauth2/github_setup.php`

## 4. API for Assignment Data

### Overview
REST API endpoint for accessing student progress data programmatically.

### Features
- Secure token-based authentication
- JSON response format
- Course-level grade data access
- Support for external integrations

### Endpoints
- `GET /webservice/rest/server.php`
  - Function: `local_customapi_get_sandbox_grades`
  - Parameters: `courseid`

### Location
- Plugin: `/local/customapi/`

### Usage
```bash
curl -X POST https://yourmoodle.com/webservice/rest/server.php \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_customapi_get_sandbox_grades" \
  -d "moodlewsrestformat=json" \
  -d "courseid=5"
```

## 5. Instructor Progress Dashboard

### Overview
Visual dashboard showing student progress on coding assignments.

### Features
- Grid view of student grades
- Interactive charts (Chart.js)
- CSV export functionality
- Real-time data via API
- Summary statistics

### Location
- Report Plugin: `/report/codeprogress/`

### Access
- Course → Reports → Coding Progress Report
- Requires `report/codeprogress:view` capability

## 6. Syntax Highlighting in Forums

### Overview
Automatic syntax highlighting for code snippets in forum posts.

### Features
- Support for multiple languages (Python, JavaScript, PHP, etc.)
- Prism.js integration
- Works with existing forum posts
- Mobile responsive

### Location
- Theme Integration: `/theme/boost/javascript/prism/`
- Custom Styles: `/theme/boost/javascript/prism/prism-custom.css`

### Usage
In forums, wrap code in:
```
```python
def hello():
    print("Hello, World!")
```
```

## Installation Guide

### Prerequisites
- Moodle 3.5 installation
- Docker installed (for code execution)
- Web server with PHP 7.0+
- MySQL/PostgreSQL database

### Step 1: Install Code Sandbox Module
```bash
# Copy module files
cp -r mod/codesandbox /path/to/moodle/mod/

# Install via Moodle admin
# Site administration → Notifications
```

### Step 2: Setup Code Execution API
```bash
cd codesandbox-api
docker-compose up -d
```

### Step 3: Install Local API Plugin
```bash
cp -r local/customapi /path/to/moodle/local/
# Install via admin notifications
```

### Step 4: Install Progress Report
```bash
cp -r report/codeprogress /path/to/moodle/report/
# Install via admin notifications
```

### Step 5: Configure GitHub OAuth2
1. Access `/admin/tool/oauth2/github_setup.php`
2. Follow on-screen instructions
3. Configure GitHub OAuth App

### Step 6: Setup Syntax Highlighting
1. Download Prism.js bundle with required languages
2. Place files in `/theme/boost/javascript/prism/`
3. Clear theme caches

### Step 7: Configure Web Services
1. Enable web services: Admin → Advanced features
2. Enable REST protocol
3. Create service token for API access

## Testing Guide

### Test Code Sandbox
1. Create a Code Sandbox activity
2. Write Python code:
   ```python
   print("Hello, Moodle!")
   for i in range(5):
       print(f"Count: {i}")
   ```
3. Click "Run Code"
4. Verify output appears

### Test Automated Grading
1. Create test file `test_example.py`:
   ```python
   import unittest
   from solution import add
   
   class TestAdd(unittest.TestCase):
       def test_positive(self):
           self.assertEqual(add(2, 3), 5)
       
       def test_negative(self):
           self.assertEqual(add(-1, -1), -2)
   ```
2. Upload to Code Sandbox
3. Submit code and verify grading

### Test GitHub Login
1. Click "Login with GitHub"
2. Authorize application
3. Verify account creation/login

### Test API
```bash
# Get web service token first
curl -X POST https://yourmoodle.com/webservice/rest/server.php \
  -d "wstoken=YOUR_TOKEN" \
  -d "wsfunction=local_customapi_get_sandbox_grades" \
  -d "moodlewsrestformat=json" \
  -d "courseid=2"
```

### Test Progress Dashboard
1. Navigate to course with Code Sandbox activities
2. Access Reports → Coding Progress Report
3. Verify data display and charts
4. Test CSV export

### Test Forum Syntax Highlighting
1. Create forum post with code:
   ````
   Here's my Python solution:
   
   ```python
   def fibonacci(n):
       if n <= 1:
           return n
       return fibonacci(n-1) + fibonacci(n-2)
   ```
   ````
2. Verify syntax highlighting appears

## Security Considerations

1. **Code Execution**: Uses Docker containers with resource limits
2. **API Access**: Token-based authentication required
3. **OAuth2**: Secure implementation with state validation
4. **File Uploads**: Restricted to .py files for test suites
5. **Capabilities**: All features use Moodle's capability system

## Performance Notes

- Code execution timeout: 10 seconds
- API responses cached for 5 minutes
- Dashboard uses AJAX for responsive updates
- Syntax highlighting lazy-loads on large pages

## Troubleshooting

### Code Sandbox Not Executing
- Check Docker service is running
- Verify API URL in settings
- Check firewall allows port 8000

### GitHub Login Fails
- Verify OAuth App settings
- Check callback URL matches
- Ensure HTTPS is enabled

### API Returns No Data
- Verify web service token permissions
- Check user has grade:viewall capability
- Ensure Code Sandbox activities exist

### Syntax Highlighting Not Working
- Clear browser cache
- Verify Prism.js files exist
- Check theme cache is cleared

## Support

For issues or questions:
1. Check Moodle logs: `/admin/report/log/`
2. Review Docker logs: `docker logs codesandbox-api`
3. Enable debugging: `/admin/settings.php?section=debugging`

## License

All code follows Moodle's GPLv3 license.