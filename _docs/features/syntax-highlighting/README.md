# Syntax Highlighting for Markdown Documents

## The Problem

Code blocks in Moodle's markdown documents were displayed as plain monospace text without any visual distinction between different code elements. This made it difficult to:

- Quickly identify keywords, functions, and variable names
- Distinguish between strings, comments, and code
- Read and understand complex code examples
- Copy code snippets without manually selecting all the text

For programming courses, where code examples are essential, this lack of syntax highlighting made learning materials less effective and harder to follow.

## The New Feature

The Syntax Highlighting enhancement adds automatic code colorization and copy functionality to all code blocks in markdown documents. Key features include:

- **Automatic Language Detection**: Intelligently identifies programming languages
- **180+ Language Support**: Highlights syntax for all major programming languages
- **One-Click Copy**: Copy button on every code block for easy code extraction
- **Professional Themes**: GitHub-style syntax coloring for familiar appearance
- **Progressive Enhancement**: Works perfectly without JavaScript, enhanced when available
- **Mobile Optimized**: Touch-friendly copy buttons and responsive design

### Key Benefits:

- **Improved Readability**: Color-coded syntax makes code easier to understand
- **Faster Learning**: Students can quickly identify code patterns and structures
- **Easy Code Sharing**: One-click copy saves time and prevents errors
- **Professional Appearance**: Code looks like modern development environments
- **Zero Configuration**: Works automatically on all markdown documents

## Implementation Notes

The syntax highlighting is implemented as a progressive enhancement to the existing markdown module:

### Technical Architecture

- **Library**: highlight.js v11.9.0 (industry-standard syntax highlighter)
- **Integration**: Direct JavaScript enhancement in `mod/markdownfile/view.php`
- **Delivery**: CDN-based for optimal performance and caching
- **Styling**: Custom CSS for copy buttons and responsive layout

### Implementation Details

1. **JavaScript Enhancement**: 
    - Automatically finds all `<pre><code>` blocks after page load
    - Applies highlight.js to each code block
    - Adds copy button with click handler

2. **Copy Functionality**:
    - Uses modern Clipboard API
    - Fallback for older browsers
    - Visual feedback on successful copy

3. **Styling**:
    - Copy buttons positioned absolutely within code blocks
    - Hover effects for better UX
    - Print-friendly (buttons hidden when printing)

4. **Performance**:
    - Lazy initialization after DOM ready
    - Minimal overhead (~45KB total)
    - CDN caching for repeat visits

## How to Test

### 1. Create Content with Code Blocks

1. Create a new Markdown file activity
2. Add content with various programming languages:

````markdown
# Programming Examples

## Python Function
```python
def calculate_fibonacci(n):
    """Calculate the nth Fibonacci number."""
    if n <= 1:
        return n
    return calculate_fibonacci(n-1) + calculate_fibonacci(n-2)

# Test the function
for i in range(10):
    print(f"F({i}) = {calculate_fibonacci(i)}")
```

## JavaScript Class
```javascript
class TodoList {
    constructor() {
        this.items = [];
    }
    
    addItem(text) {
        this.items.push({
            id: Date.now(),
            text: text,
            completed: false
        });
    }
    
    toggleItem(id) {
        const item = this.items.find(i => i.id === id);
        if (item) {
            item.completed = !item.completed;
        }
    }
}
```

## SQL Query
```sql
SELECT 
    u.firstname, 
    u.lastname,
    COUNT(DISTINCT c.id) as course_count,
    AVG(gg.finalgrade) as avg_grade
FROM mdl_user u
JOIN mdl_user_enrolments ue ON u.id = ue.userid
JOIN mdl_enrol e ON ue.enrolid = e.id
JOIN mdl_course c ON e.courseid = c.id
LEFT JOIN mdl_grade_grades gg ON gg.userid = u.id
GROUP BY u.id, u.firstname, u.lastname
HAVING course_count > 3
ORDER BY avg_grade DESC;
```
````

### 2. Verify Syntax Highlighting

1. Save and view the activity
2. Check that code blocks show:
   - **Keywords** in bold/different color (def, class, SELECT)
   - **Strings** in a distinct color (usually green)
   - **Comments** in italics or gray
   - **Functions/Methods** highlighted
   - **Numbers** in a different color

### 3. Test Copy Functionality

1. Hover over any code block
2. Notice the "Copy" button appears in top-right
3. Click the "Copy" button
4. See it change to "✅ Copied!" briefly
5. Paste into a text editor
6. Verify code is copied without any formatting or extra characters

### 4. Test Language Auto-Detection

Create a code block without specifying language:

````markdown
```
# This should be detected as a shell script
#!/bin/bash

echo "Hello, World!"
for i in {1..5}; do
    echo "Number: $i"
done
```
````

The syntax highlighter should automatically detect and highlight appropriately.

### 5. Test Multiple Code Blocks

Create a document with many code blocks:

1. Add 10+ code blocks in different languages
2. Verify all are highlighted correctly
3. Check page performance remains smooth
4. Each copy button works independently

### 6. Test Edge Cases

1. **Empty code blocks**: Should display without errors
2. **Very long lines**: Should scroll horizontally
3. **Special characters**: `< > & " '` should display correctly
4. **Mixed languages in one document**: Each highlighted independently

### 9. Quick Functionality Check

- ✅ Code blocks automatically highlighted on page load
- ✅ Multiple programming languages detected correctly
- ✅ Copy buttons appear on hover
- ✅ Copy functionality works with visual feedback
- ✅ Mobile responsive with touch support
- ✅ Graceful degradation without JavaScript
- ✅ Performance remains fast with many code blocks
