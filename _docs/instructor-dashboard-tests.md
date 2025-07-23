# Instructor Progress Dashboard - Acceptance Tests

## Prerequisites
- Report module `report_codeprogress` installed
- Course with multiple Code Sandbox activities
- Multiple students enrolled with various submission states
- Teacher account with report viewing permissions
- Modern web browser with JavaScript enabled
- Some graded and ungraded submissions

## Test 1: Installation Verification

### Steps:
1. Log in as administrator
2. Navigate to Site administration → Notifications
3. Verify report_codeprogress installation
4. Check Site administration → Plugins → Reports

### Expected Results:
- [ ] Plugin installs without errors
- [ ] "Coding Progress Report" appears in reports list
- [ ] Dependencies (mod_codesandbox, local_customapi) satisfied
- [ ] No database errors

## Test 2: Report Access in Course

### Steps:
1. Log in as a teacher
2. Navigate to a course with Code Sandbox activities
3. Look in the Navigation block or course administration
4. Find "Reports" section
5. Click "Coding Progress Report"

### Expected Results:
- [ ] "Coding Progress Report" link visible
- [ ] Link only appears for users with permission
- [ ] Clicking opens report page
- [ ] No permission errors

## Test 3: Initial Report Load

### Steps:
1. Access the Coding Progress Report
2. Observe loading process
3. Wait for data to load

### Expected Results:
- [ ] Loading spinner appears
- [ ] "Loading progress data..." message shown
- [ ] Data loads within 5 seconds
- [ ] Loading message disappears
- [ ] Main dashboard becomes visible

## Test 4: Data Table Display

### Steps:
1. After report loads, examine the table
2. Verify table structure:
   - Student names in first column
   - Activity names in header row
   - Grades in cells
3. Check data accuracy

### Expected Results:
- [ ] Table displays correctly
- [ ] All enrolled students listed
- [ ] All Code Sandbox activities shown
- [ ] Student column is sticky (stays visible when scrolling)
- [ ] Grades match actual submissions
- [ ] Non-submissions show dash (-)

## Test 5: Grade Color Coding

### Steps:
1. Examine grades in the table
2. Verify color coding:
   - 80%+ should be green
   - 60-79% should be yellow/amber
   - Below 60% should be red
3. Check cells with no submission

### Expected Results:
- [ ] High grades (≥80%) show in green
- [ ] Medium grades (60-79%) show in amber
- [ ] Low grades (<60%) show in red
- [ ] No submission cells are gray with dash
- [ ] Colors are distinguishable

## Test 6: Average Calculations

### Steps:
1. Look at the bottom row of the table
2. Verify "Average" row appears
3. Check calculations for each activity
4. Manually calculate one column to verify

### Expected Results:
- [ ] Average row appears at bottom
- [ ] Averages calculated per activity
- [ ] Only submitted grades included in average
- [ ] Calculations are accurate
- [ ] Averages show one decimal place

## Test 7: Chart Display - Bar Chart

### Steps:
1. Scroll down to charts section
2. Examine the bar chart
3. Verify data matches table averages
4. Hover over bars for tooltips

### Expected Results:
- [ ] Bar chart displays average scores
- [ ] One bar per activity
- [ ] Heights correspond to averages
- [ ] Y-axis shows 0-100%
- [ ] Tooltips show exact values
- [ ] Chart is responsive to window size

## Test 8: Chart Display - Pie Chart

### Steps:
1. Examine the submission status pie chart
2. Verify segments for:
   - Submitted
   - Not Submitted
3. Hover for details

### Expected Results:
- [ ] Pie chart shows submission status
- [ ] Two segments clearly visible
- [ ] Percentages add up to 100%
- [ ] Legend identifies segments
- [ ] Tooltips show counts and percentages

## Test 9: Summary Statistics Cards

### Steps:
1. Look at the summary cards section
2. Verify four cards display:
   - Total Students
   - Assignments
   - Average Score
   - Completion Rate
3. Check calculations

### Expected Results:
- [ ] Four summary cards visible
- [ ] Total students count is correct
- [ ] Assignment count matches activities
- [ ] Overall average is accurate
- [ ] Completion rate percentage correct
- [ ] Cards are responsive layout

## Test 10: CSV Export

### Steps:
1. Click "Export to CSV" button
2. Save the file
3. Open in spreadsheet application
4. Verify data integrity

### Expected Results:
- [ ] Export button is visible
- [ ] File downloads with appropriate name
- [ ] CSV contains all table data
- [ ] Headers match table columns
- [ ] Special characters handled properly
- [ ] Can open in Excel/LibreOffice

## Test 11: Large Dataset Performance

### Steps:
1. Test with course having:
   - 50+ students
   - 10+ activities
2. Time the report load
3. Check responsiveness

### Expected Results:
- [ ] Report loads within 10 seconds
- [ ] Table scrolls smoothly
- [ ] Charts render properly
- [ ] No browser freezing
- [ ] Export still works

## Test 12: No Activities Scenario

### Steps:
1. Access report in course with no Code Sandbox activities
2. Check display

### Expected Results:
- [ ] Appropriate message displayed
- [ ] "No code sandbox activities found"
- [ ] No JavaScript errors
- [ ] Can navigate away normally

## Test 13: Permission Testing

### Steps:
1. Log in as student
2. Try to access report URL directly
3. Log in as non-editing teacher
4. Access report

### Expected Results:
- [ ] Students cannot access report
- [ ] Permission error displayed
- [ ] Teachers can access
- [ ] Managers can access

## Test 14: Real-time Data Updates

### Steps:
1. Open report in one browser
2. In another browser, have student submit code
3. Refresh report page
4. Verify new submission appears

### Expected Results:
- [ ] Refresh shows new data
- [ ] New submission visible
- [ ] Averages recalculated
- [ ] Charts updated
- [ ] No caching issues

## Test 15: Browser Compatibility

### Steps:
1. Test report in different browsers:
   - Chrome/Chromium
   - Firefox
   - Safari
   - Edge
2. Check all features work

### Expected Results:
- [ ] Report loads in all browsers
- [ ] Charts display correctly
- [ ] Table formatting consistent
- [ ] Export works
- [ ] No JavaScript errors

## Test 16: Mobile Responsiveness

### Steps:
1. Access report on mobile device
2. Test in portrait and landscape
3. Try all features

### Expected Results:
- [ ] Report is mobile-friendly
- [ ] Table scrolls horizontally
- [ ] Charts resize appropriately
- [ ] Summary cards stack vertically
- [ ] Export button accessible

## Test 17: Print View

### Steps:
1. Use browser print preview
2. Check layout
3. Print to PDF

### Expected Results:
- [ ] Print layout is readable
- [ ] Charts included
- [ ] Table fits page width
- [ ] No cut-off content
- [ ] Headers/footers appropriate

## Test 18: Accessibility Testing

### Steps:
1. Navigate using keyboard only
2. Use screen reader if available
3. Check color contrast
4. Test with high contrast mode

### Expected Results:
- [ ] Keyboard navigation works
- [ ] Table is readable by screen reader
- [ ] Sufficient color contrast
- [ ] Charts have text alternatives
- [ ] Focus indicators visible

## Test 19: Error Handling

### Steps:
1. Disable JavaScript and try to load
2. Disconnect network after initial load
3. Corrupt API response (if possible)

### Expected Results:
- [ ] Graceful degradation without JS
- [ ] Network errors shown clearly
- [ ] No white screen of death
- [ ] Can recover from errors

## Test 20: Data Sorting and Filtering

### Steps:
1. Click on column headers (if sortable)
2. Look for filter options
3. Test any interactive features

### Expected Results:
- [ ] Sorting works if implemented
- [ ] Data remains consistent
- [ ] Visual feedback for sort state
- [ ] Performance remains good

## Integration Testing

### Test 21: Gradebook Consistency

### Steps:
1. Open gradebook for same course
2. Compare grades with dashboard
3. Check for discrepancies

### Expected Results:
- [ ] Grades match exactly
- [ ] Same calculation methods
- [ ] Manual overrides shown
- [ ] No data inconsistencies

## Performance Benchmarks

| Students × Activities | Load Time Target |
|----------------------|------------------|
| 10 × 5              | < 2 seconds      |
| 30 × 10             | < 5 seconds      |
| 100 × 20            | < 10 seconds     |

## Common Issues and Solutions

### Issue: Charts not displaying
- **Check**: JavaScript enabled
- **Check**: Chart.js loaded properly
- **Check**: No JavaScript errors
- **Try**: Clear browser cache

### Issue: Data not loading
- **Check**: API service working
- **Check**: Web services enabled
- **Check**: User permissions

### Issue: Export not working
- **Check**: Pop-up blocker
- **Check**: Download permissions
- **Try**: Different browser

## Sign-off

- [ ] All tests completed successfully
- [ ] Dashboard provides valuable insights
- [ ] Performance is acceptable
- [ ] User experience is smooth
- [ ] Data accuracy verified

**Tester Name**: _________________

**Date**: _________________

**Notes**: _________________