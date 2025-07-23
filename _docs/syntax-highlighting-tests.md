# Syntax Highlighting in Forums - Acceptance Tests

## Prerequisites
- Boost theme active (or modified theme with integration)
- Prism.js files downloaded and placed in `/theme/boost/javascript/prism/`
- Forum module enabled
- Test course with forum activity
- Teacher and student accounts
- Knowledge of basic code syntax in multiple languages

## Test 1: Theme Integration Verification

### Steps:
1. Check `/theme/boost/javascript/prism/` directory exists
2. Verify presence of:
   - prism.js
   - prism.css
   - prism-custom.css (from implementation)
3. Clear theme caches: Site administration → Development → Purge all caches

### Expected Results:
- [ ] Directory structure correct
- [ ] All required files present
- [ ] Cache clearing completes
- [ ] No error messages

## Test 2: Forum Post with Python Code

### Steps:
1. Navigate to a forum in test course
2. Create new discussion topic
3. In the post, include:
   ```
   Here's a Python function:
   
   ```python
   def fibonacci(n):
       if n <= 1:
           return n
       return fibonacci(n-1) + fibonacci(n-2)
   
   # Test the function
   for i in range(10):
       print(f"F({i}) = {fibonacci(i)}")
   ```
   ```
4. Submit the post

### Expected Results:
- [ ] Code block is highlighted
- [ ] Python keywords (def, if, return) are colored
- [ ] Comments are in different color
- [ ] Strings are highlighted
- [ ] Line structure preserved

## Test 3: Multiple Language Support

### Steps:
1. Create a forum post with multiple code blocks:

   JavaScript:
   ```javascript
   function greet(name) {
       console.log(`Hello, ${name}!`);
       return name.length;
   }
   ```
   
   PHP:
   ```php
   <?php
   class User {
       private $name;
       
       public function __construct($name) {
           $this->name = $name;
       }
       
       public function getName() {
           return $this->name;
       }
   }
   ?>
   ```
   
   SQL:
   ```sql
   SELECT u.firstname, u.lastname, g.grade
   FROM mdl_user u
   JOIN mdl_grade_grades g ON u.id = g.userid
   WHERE g.grade > 80
   ORDER BY g.grade DESC;
   ```

### Expected Results:
- [ ] Each language highlighted appropriately
- [ ] JavaScript: functions, strings, templates highlighted
- [ ] PHP: variables ($), keywords, classes highlighted
- [ ] SQL: keywords (SELECT, FROM, JOIN) highlighted
- [ ] Different color schemes for each language

## Test 4: Code Without Language Specification

### Steps:
1. Post code without language tag:
   ```
   ```
   for i in range(10):
       if i % 2 == 0:
           print(i)
   ```
   ```

### Expected Results:
- [ ] Code still displayed in monospace
- [ ] Basic formatting preserved
- [ ] May have generic highlighting
- [ ] No errors displayed

## Test 5: Inline Code

### Steps:
1. Create post with inline code:
   "Use the `print()` function in Python or `console.log()` in JavaScript"
2. Mix with code blocks

### Expected Results:
- [ ] Inline code has different styling
- [ ] Background color for inline code
- [ ] Monospace font
- [ ] Distinguishable from regular text

## Test 6: Special Characters in Code

### Steps:
1. Post code with special characters:
   ```python
   # Special characters test
   print("Hello <world>")
   print('Testing "quotes" and \'apostrophes\'')
   print("Unicode: 你好 🌍")
   html = "<div class='test'>&nbsp;</div>"
   ```

### Expected Results:
- [ ] Special characters display correctly
- [ ] No HTML injection
- [ ] Quotes properly escaped
- [ ] Unicode characters preserved
- [ ] HTML entities shown as code

## Test 7: Large Code Blocks

### Steps:
1. Post a large code block (100+ lines)
2. Include various language constructs

### Expected Results:
- [ ] Highlighting performs well
- [ ] No browser lag
- [ ] Scrolling works smoothly
- [ ] All code highlighted consistently

## Test 8: Edit and Re-save

### Steps:
1. Edit a post with highlighted code
2. Make changes to the code
3. Save the post
4. View the updated post

### Expected Results:
- [ ] Highlighting reapplied after edit
- [ ] Changes reflected properly
- [ ] No formatting lost
- [ ] Editor preserves code blocks

## Test 9: Different Forum Types

### Steps:
1. Test in different forum types:
   - Standard forum
   - Simple single discussion
   - Q&A forum
   - Blog-style forum
2. Post code in each

### Expected Results:
- [ ] Highlighting works in all forum types
- [ ] Consistent appearance
- [ ] No conflicts with forum features
- [ ] Reply posts also highlighted

## Test 10: Theme Compatibility

### Steps:
1. Switch to other themes if available
2. Check if highlighting still works
3. Return to Boost theme

### Expected Results:
- [ ] Boost theme: full highlighting
- [ ] Other themes: graceful degradation
- [ ] No JavaScript errors
- [ ] Basic code formatting preserved

## Test 11: AJAX Loading

### Steps:
1. In a forum with pagination
2. Post code on page 2
3. Navigate between pages
4. Use AJAX features if available

### Expected Results:
- [ ] Code highlighted on all pages
- [ ] AJAX-loaded content highlighted
- [ ] No need to refresh page
- [ ] Smooth transitions

## Test 12: Print View

### Steps:
1. View forum post with code
2. Use browser print preview
3. Check code appearance

### Expected Results:
- [ ] Code blocks visible in print
- [ ] Syntax colors preserved (if color printing)
- [ ] Layout remains readable
- [ ] No cut-off code

## Test 13: Mobile View

### Steps:
1. Access forum on mobile device
2. View posts with code
3. Try different orientations

### Expected Results:
- [ ] Code blocks responsive
- [ ] Horizontal scrolling for wide code
- [ ] Font size readable
- [ ] Touch scrolling works

## Test 14: Copy and Paste

### Steps:
1. Select highlighted code
2. Copy to clipboard
3. Paste into:
   - Text editor
   - IDE
   - Another forum post

### Expected Results:
- [ ] Code copies without formatting
- [ ] Line breaks preserved
- [ ] No extra characters
- [ ] Can paste as plain text

## Test 15: Performance Testing

### Steps:
1. Create forum thread with many code posts
2. Time page load
3. Monitor browser performance

### Expected Results:
- [ ] Page loads within 3 seconds
- [ ] No browser freezing
- [ ] Smooth scrolling
- [ ] Memory usage acceptable

## Test 16: Dark Theme Compatibility

### Steps:
1. If dark theme available, switch to it
2. View code highlighting
3. Check contrast and readability

### Expected Results:
- [ ] Code visible on dark background
- [ ] Good color contrast
- [ ] Syntax colors adjusted for dark theme
- [ ] No readability issues

## Test 17: Language Auto-Detection

### Steps:
1. Post code without language specification
2. Use obvious syntax from different languages
3. Check if correctly detected

### Expected Results:
- [ ] Common patterns recognized
- [ ] Reasonable detection accuracy
- [ ] Falls back gracefully
- [ ] No false positives

## Test 18: Forum Search

### Steps:
1. Search forum for code content
2. View search results
3. Click through to posts

### Expected Results:
- [ ] Code snippets in search results
- [ ] Highlighting in search context
- [ ] Full highlighting when viewing post
- [ ] Search terms highlighted in code

## Test 19: Nested Code Blocks

### Steps:
1. Try to nest code blocks
2. Post markdown inside code blocks
3. Test edge cases

### Expected Results:
- [ ] Proper handling of nesting
- [ ] No broken formatting
- [ ] Inner code displayed as text
- [ ] Clear block boundaries

## Test 20: Accessibility

### Steps:
1. Use screen reader on code blocks
2. Navigate with keyboard only
3. Check color contrast ratios
4. Test with browser zoom

### Expected Results:
- [ ] Screen reader announces code blocks
- [ ] Keyboard navigation works
- [ ] Sufficient color contrast
- [ ] Zoom doesn't break layout

## Common Issues and Solutions

### Issue: No highlighting visible
- **Check**: Prism files loaded correctly
- **Check**: JavaScript console for errors
- **Clear**: Browser cache
- **Verify**: Theme cache cleared

### Issue: Wrong language detected
- **Solution**: Always specify language
- **Example**: Use ` ```python` not just ` ``` `

### Issue: Performance problems
- **Limit**: Code block size
- **Check**: Number of blocks per page
- **Consider**: Lazy loading

## Browser-Specific Tests

### Chrome/Edge
- [ ] Full highlighting support
- [ ] Dev tools show no errors
- [ ] Performance profiling acceptable

### Firefox
- [ ] Highlighting works
- [ ] No console warnings
- [ ] Print preview correct

### Safari
- [ ] Basic highlighting functional
- [ ] Mobile Safari works
- [ ] No rendering issues

## Sign-off

- [ ] All tests completed successfully
- [ ] Highlighting enhances readability
- [ ] No performance degradation
- [ ] Cross-browser compatibility verified
- [ ] Accessibility standards met

**Tester Name**: _________________

**Date**: _________________

**Notes**: _________________