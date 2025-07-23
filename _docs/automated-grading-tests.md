# Automated Unit Test Grading - Acceptance Tests

## Prerequisites
- Code Sandbox module installed and working
- Teacher account with course creation privileges
- Student test accounts
- Python unittest knowledge for creating test files

## Test 1: Enable Grading in Code Sandbox

### Steps:
1. Log in as a teacher
2. Create a new Code Sandbox activity
3. Fill in basic information:
   - Name: "Graded Python Assignment"
   - Description: "Implement the required functions"
4. In the Grading Settings section:
   - Check "Enable automatic grading"
   - Verify test suite file upload appears
   - Set Maximum grade to 100

### Expected Results:
- [ ] Grading settings section is visible
- [ ] Checking "Enable grading" reveals additional options
- [ ] File upload field accepts .py files only
- [ ] Maximum grade field accepts numeric values

## Test 2: Upload Test Suite

### Steps:
1. Create a test file `test_assignment.py`:
   ```python
   import unittest
   from solution import add, multiply, is_even
   
   class TestAssignment(unittest.TestCase):
       def test_add_positive(self):
           """Test addition with positive numbers"""
           self.assertEqual(add(2, 3), 5)
           self.assertEqual(add(10, 20), 30)
       
       def test_add_negative(self):
           """Test addition with negative numbers"""
           self.assertEqual(add(-5, -3), -8)
           self.assertEqual(add(-10, 5), -5)
       
       def test_multiply(self):
           """Test multiplication"""
           self.assertEqual(multiply(3, 4), 12)
           self.assertEqual(multiply(-2, 5), -10)
           self.assertEqual(multiply(0, 100), 0)
       
       def test_is_even(self):
           """Test even number checker"""
           self.assertTrue(is_even(2))
           self.assertTrue(is_even(100))
           self.assertFalse(is_even(3))
           self.assertFalse(is_even(-5))
   
   if __name__ == '__main__':
       unittest.main()
   ```
2. Upload this file in the test suite field
3. Set starter code:
   ```python
   # Implement the following functions:
   
   def add(a, b):
       """Add two numbers"""
       pass
   
   def multiply(a, b):
       """Multiply two numbers"""
       pass
   
   def is_even(n):
       """Return True if n is even, False otherwise"""
       pass
   ```
4. Save the activity

### Expected Results:
- [ ] File uploads successfully
- [ ] No error messages
- [ ] Activity saves with grading enabled
- [ ] Grade item appears in gradebook

## Test 3: Student Submission - All Tests Pass

### Steps:
1. Log in as a student
2. Open "Graded Python Assignment"
3. Implement the functions correctly:
   ```python
   def add(a, b):
       """Add two numbers"""
       return a + b
   
   def multiply(a, b):
       """Multiply two numbers"""
       return a * b
   
   def is_even(n):
       """Return True if n is even, False otherwise"""
       return n % 2 == 0
   ```
4. Click "Run Code" to test
5. Click "Submit for Grading"

### Expected Results:
- [ ] Code runs without errors
- [ ] Submit button is visible for gradable activities
- [ ] After submission, test results show:
   - Score: 100% (4/4 tests passed)
   - ✓ test_add_positive
   - ✓ test_add_negative  
   - ✓ test_multiply
   - ✓ test_is_even
- [ ] Results appear in "Test Results" tab
- [ ] All test names are displayed with green checkmarks

## Test 4: Student Submission - Partial Credit

### Steps:
1. Log in as a different student
2. Implement with one error:
   ```python
   def add(a, b):
       """Add two numbers"""
       return a + b
   
   def multiply(a, b):
       """Multiply two numbers"""
       return a * b  # Correct
   
   def is_even(n):
       """Return True if n is even, False otherwise"""
       return n % 2 == 1  # Wrong! This checks for odd
   ```
3. Submit for grading

### Expected Results:
- [ ] Score: 75% (3/4 tests passed)
- [ ] Test results show:
   - ✓ test_add_positive (passed)
   - ✓ test_add_negative (passed)
   - ✓ test_multiply (passed)
   - ✗ test_is_even (failed)
- [ ] Failed test shows error message
- [ ] Grade of 75/100 appears in gradebook

## Test 5: Student Submission - Syntax Error

### Steps:
1. Submit code with syntax error:
   ```python
   def add(a, b):
       return a + b
   
   def multiply(a, b)  # Missing colon
       return a * b
   
   def is_even(n):
       return n % 2 == 0
   ```
2. Click "Submit for Grading"

### Expected Results:
- [ ] Score: 0% (0/4 tests passed)
- [ ] Error message indicates syntax error
- [ ] Specific line number mentioned
- [ ] Grade of 0/100 in gradebook

## Test 6: Student Submission - Runtime Error

### Steps:
1. Submit code with runtime error:
   ```python
   def add(a, b):
       return a + b
   
   def multiply(a, b):
       return a / b  # Division instead of multiplication
   
   def is_even(n):
       return n % 2 == 0
   ```
2. Submit for grading

### Expected Results:
- [ ] Partial credit for working functions
- [ ] Failed test shows specific error (e.g., assertion error)
- [ ] Error details help student debug

## Test 7: Teacher View of Grades

### Steps:
1. Log in as teacher
2. Navigate to course gradebook
3. Find the "Graded Python Assignment" column
4. Verify student grades match their test scores
5. Click on a specific grade to see details

### Expected Results:
- [ ] All student submissions appear in gradebook
- [ ] Grades match the test scores (as percentages)
- [ ] Can view individual submission details
- [ ] Test results are visible to teacher

## Test 8: Grade Modification

### Steps:
1. As teacher, manually override a student's grade in gradebook
2. As student, resubmit the same code
3. Check if automated grade updates

### Expected Results:
- [ ] Manual grade override is possible
- [ ] Resubmission updates the grade
- [ ] New automated grade replaces manual grade
- [ ] History of submissions is maintained

## Test 9: Complex Test Suite

### Steps:
1. Create a test suite with multiple test classes and edge cases:
   ```python
   import unittest
   import sys
   from io import StringIO
   from solution import *
   
   class TestEdgeCases(unittest.TestCase):
       def test_add_zero(self):
           self.assertEqual(add(0, 0), 0)
           self.assertEqual(add(5, 0), 5)
       
       def test_large_numbers(self):
           self.assertEqual(add(999999, 1), 1000000)
   
   class TestOutput(unittest.TestCase):
       def test_print_function(self):
           captured = StringIO()
           sys.stdout = captured
           print_greeting("World")
           sys.stdout = sys.__stdout__
           self.assertEqual(captured.getvalue().strip(), "Hello, World!")
   ```
2. Upload and test with various student implementations

### Expected Results:
- [ ] Multiple test classes work correctly
- [ ] Edge cases are properly tested
- [ ] Output capture tests work
- [ ] Test counts are accurate

## Test 10: Test Suite Security

### Steps:
1. Try uploading a malicious test file:
   ```python
   import os
   os.system("rm -rf /")  # Should not execute!
   
   class TestMalicious(unittest.TestCase):
       def test_hack(self):
           with open('/etc/passwd', 'r') as f:
               print(f.read())
   ```
2. Submit student code against this test

### Expected Results:
- [ ] Malicious commands don't affect system
- [ ] File access is restricted
- [ ] Tests run in sandboxed environment
- [ ] No system damage occurs

## Test 11: Performance with Many Tests

### Steps:
1. Create test suite with 50+ test methods
2. Submit code that passes all tests
3. Monitor execution time

### Expected Results:
- [ ] All tests execute within timeout (15 seconds)
- [ ] Results display properly for many tests
- [ ] UI remains responsive
- [ ] Scrollable test results if needed

## Test 12: Regrade Functionality

### Steps:
1. As teacher, modify the test suite file
2. Update the activity with new test file
3. Check if existing submissions can be regraded

### Expected Results:
- [ ] Can update test suite after submissions exist
- [ ] Option to regrade all submissions
- [ ] Grades update based on new tests
- [ ] Students notified of grade changes

## Test 13: Export and Analytics

### Steps:
1. After multiple submissions, export gradebook
2. Analyze the test results patterns
3. Identify commonly failed tests

### Expected Results:
- [ ] Export includes test scores
- [ ] Can identify problem areas
- [ ] Detailed feedback available
- [ ] Results useful for teaching improvements

## Common Issues and Solutions

### Issue: Tests not running
- **Check**: Test file syntax is valid Python
- **Check**: Import statements match student code
- **Check**: Function/class names match exactly

### Issue: Score always 0%
- **Check**: Test file uses correct assertions
- **Check**: Student code defines required functions
- **Verify**: Docker service is running

### Issue: Timeout errors
- **Check**: Tests don't have infinite loops
- **Check**: Not too many tests (< 100)
- **Consider**: Increasing timeout limit

## Integration Tests

### Test 14: Gradebook Integration

### Steps:
1. Set up gradebook categories
2. Place graded assignment in category
3. Set category weights
4. Verify final grades calculate correctly

### Expected Results:
- [ ] Assignment appears in correct category
- [ ] Weights apply correctly
- [ ] Can use in grade calculations
- [ ] Export includes test grades

## Sign-off

- [ ] All tests completed successfully
- [ ] Grading is accurate and reliable
- [ ] Performance is acceptable
- [ ] Security measures work properly
- [ ] Integration with gradebook is seamless

**Tester Name**: _________________

**Date**: _________________

**Notes**: _________________