# Live Code Sandbox Module - Acceptance Tests

## Prerequisites
- Moodle 3.5 installation with Code Sandbox module installed
- Docker service running on the server
- FastAPI microservice running on port 8000
- Teacher and student test accounts
- A test course where you have teacher privileges

## Test 1: Module Installation Verification

### Steps:
1. Log in as an administrator
2. Navigate to Site administration → Notifications
3. Verify that mod_codesandbox appears in the plugins list
4. Check that the installation completed without errors

### Expected Results:
- [ ] Code Sandbox module appears in the installed plugins list
- [ ] No error messages during installation
- [ ] Database tables `mdl_codesandbox` and `mdl_codesandbox_submissions` exist

## Test 2: Adding Code Sandbox Activity to Course

### Steps:
1. Log in as a teacher
2. Navigate to a test course
3. Turn editing on
4. Click "Add an activity or resource" in any section
5. Select "Code Sandbox" from the activity list
6. Fill in the form:
   - Name: "Python Practice Exercise"
   - Description: "Practice basic Python programming"
   - Starter code:
     ```python
     # Welcome to Python!
     # Write a function that adds two numbers
     
     def add(a, b):
         # Your code here
         pass
     ```
7. Leave grading disabled for now
8. Save and return to course

### Expected Results:
- [ ] Code Sandbox appears in the activity chooser
- [ ] Form saves without errors
- [ ] Activity appears in the course with the correct name
- [ ] Activity icon is visible

## Test 3: Student View and Basic Code Execution

### Steps:
1. Log in as a student enrolled in the test course
2. Click on "Python Practice Exercise"
3. Verify the code editor loads with starter code
4. Replace the starter code with:
   ```python
   def add(a, b):
       return a + b
   
   # Test the function
   print("2 + 3 =", add(2, 3))
   print("Hello from Moodle!")
   ```
5. Click "Run Code"
6. Wait for execution to complete

### Expected Results:
- [ ] CodeMirror editor loads properly with syntax highlighting
- [ ] Starter code is pre-populated in the editor
- [ ] Line numbers are visible
- [ ] "Run Code" button is clickable
- [ ] Loading spinner appears while code executes
- [ ] Output shows:
  ```
  2 + 3 = 5
  Hello from Moodle!
  ```
- [ ] No error messages appear

## Test 4: Error Handling

### Steps:
1. Clear the editor and enter code with a syntax error:
   ```python
   print("Missing closing quote
   ```
2. Click "Run Code"
3. Clear the editor and enter code with a runtime error:
   ```python
   print(undefined_variable)
   ```
4. Click "Run Code"
5. Clear the editor and enter an infinite loop:
   ```python
   while True:
       print("Infinite loop")
   ```
6. Click "Run Code"

### Expected Results:
- [ ] Syntax error shows in the error output tab
- [ ] Runtime error shows appropriate error message
- [ ] Infinite loop terminates after timeout (10 seconds)
- [ ] Error messages are displayed in red
- [ ] Editor remains functional after errors

## Test 5: Code Persistence

### Steps:
1. As a student, write some code:
   ```python
   def factorial(n):
       if n <= 1:
           return 1
       return n * factorial(n-1)
   
   print(factorial(5))
   ```
2. Navigate away from the activity
3. Return to the activity

### Expected Results:
- [ ] Previous code is restored in the editor
- [ ] Code executes correctly when run again

## Test 6: Multiple Students

### Steps:
1. Log in as Student A and submit code
2. Log in as Student B in a different browser
3. Verify Student B sees the starter code, not Student A's code
4. Have both students submit different code
5. As teacher, verify you can see both submissions

### Expected Results:
- [ ] Each student's work is isolated
- [ ] No code mixing between students
- [ ] Teacher can identify which code belongs to which student

## Test 7: API Service Connection

### Steps:
1. As administrator, temporarily stop the Docker service
2. As student, try to run code
3. Restart the Docker service
4. Try to run code again

### Expected Results:
- [ ] Appropriate error message when service is down
- [ ] No PHP errors or blank pages
- [ ] Functionality restored when service is back up

## Test 8: Special Characters and Unicode

### Steps:
1. Enter code with special characters:
   ```python
   print("Special chars: <>&\"'")
   print("Unicode: 你好世界 🌍")
   print("Math symbols: ∑ ∏ ∫")
   ```
2. Run the code

### Expected Results:
- [ ] All characters display correctly in editor
- [ ] Output shows all characters properly
- [ ] No encoding errors

## Test 9: Resource Limits

### Steps:
1. Try to consume excessive memory:
   ```python
   huge_list = [0] * (10**8)  # Try to allocate huge list
   ```
2. Try to write to filesystem:
   ```python
   with open('/tmp/test.txt', 'w') as f:
       f.write('test')
   ```
3. Try to access network:
   ```python
   import urllib.request
   response = urllib.request.urlopen('http://google.com')
   ```

### Expected Results:
- [ ] Memory limit prevents excessive allocation
- [ ] File operations may work in /tmp but are not persistent
- [ ] Network access is blocked
- [ ] Appropriate error messages for each case

## Test 10: Browser Compatibility

### Steps:
1. Test the activity in different browsers:
   - Chrome/Chromium
   - Firefox
   - Safari (if available)
   - Edge

### Expected Results:
- [ ] Editor loads in all browsers
- [ ] Syntax highlighting works
- [ ] Code execution works
- [ ] No JavaScript console errors
- [ ] Responsive layout on mobile devices

## Test 11: Accessibility

### Steps:
1. Navigate through the interface using only keyboard
2. Use Tab to move between elements
3. Use Enter to activate buttons
4. Test with a screen reader if available

### Expected Results:
- [ ] All interactive elements are keyboard accessible
- [ ] Focus indicators are visible
- [ ] Buttons have appropriate labels
- [ ] Editor is navigable with keyboard

## Test 12: Performance

### Steps:
1. Enter a large code file (100+ lines)
2. Run code that produces lots of output:
   ```python
   for i in range(1000):
       print(f"Line {i}")
   ```
3. Run multiple executions quickly

### Expected Results:
- [ ] Editor remains responsive with large files
- [ ] Large output is displayed without freezing
- [ ] Rapid executions are queued properly
- [ ] No memory leaks after multiple runs

## Common Issues and Solutions

### Issue: "Could not connect to execution service"
- **Check**: Is Docker running? `sudo systemctl status docker`
- **Check**: Is the API service running? `docker ps | grep codesandbox-api`
- **Check**: Firewall allows port 8000?

### Issue: Code editor not appearing
- **Check**: Browser JavaScript console for errors
- **Check**: CodeMirror files loaded correctly
- **Clear**: Browser cache and reload

### Issue: No output after running code
- **Check**: Output and Errors tabs
- **Check**: API service logs: `docker logs codesandbox-api`
- **Try**: Simple print statement first

## Sign-off

- [ ] All tests completed successfully
- [ ] No critical issues found
- [ ] Performance is acceptable
- [ ] User experience is smooth

**Tester Name**: _________________

**Date**: _________________

**Notes**: _________________