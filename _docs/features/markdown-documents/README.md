# Markdown Documents for Moodle 3.5

## The Problem

Traditional content delivery in Moodle was limited to basic HTML text areas and file uploads. Instructors who wanted to share formatted technical documentation, programming tutorials, or other structured content had to:
- Use the HTML editor which was cumbersome for code examples
- Upload PDF files that weren't mobile-friendly or searchable
- Create external web pages and link to them
- Manually format code blocks without syntax highlighting

This made it difficult to create professional-looking technical documentation directly within Moodle, especially for programming courses where code examples are essential.

## The New Feature

The Markdown Document activity module allows instructors to create and share beautifully formatted content using Markdown syntax. Key features include:

- **Markdown Editing**: Write content in simple Markdown syntax
- **File Upload Support**: Upload existing .md, .markdown, or .txt files
- **Syntax Highlighting**: Automatic syntax highlighting for code blocks with language detection
- **Copy Code Functionality**: One-click copy buttons on all code blocks
- **Flexible Display**: Choose between embedded display or download-only
- **Mobile Responsive**: Content adapts to all screen sizes

### Key Benefits:

- **Developer-Friendly**: Use familiar Markdown syntax instead of WYSIWYG editors
- **Professional Code Display**: Syntax highlighting makes code examples readable and attractive
- **Version Control Ready**: Markdown files can be tracked in Git
- **Fast Content Creation**: Write documentation quickly without formatting hassles
- **Consistent Styling**: All markdown documents have uniform, professional appearance

## Implementation Notes

The feature is implemented as a standard Moodle activity module with modern enhancements:

### Core Module (`mod_markdownfile`)

- Standard Moodle activity module structure
- Supports both database content storage and file uploads
- Uses Moodle's built-in Markdown parser (FORMAT_MARKDOWN)
- Provides three display modes: Automatic, Embed, and Force Download

### Syntax Highlighting Enhancement

- Uses highlight.js library (v11.9.0) loaded from CDN
- Applied via JavaScript progressive enhancement
- Adds copy-to-clipboard buttons to all code blocks
- Supports automatic language detection for unmarked code
- Gracefully degrades if JavaScript is disabled

### Security Features

- All content is processed through Moodle's text formatting system
- HTML is sanitized to prevent XSS attacks
- File uploads restricted to safe extensions (.md, .markdown, .txt)
- Standard Moodle capability checks (view, addinstance)

## How to Test

### 1. Create a Markdown Document Activity

1. Turn editing on in a course
2. Click "Add an activity or resource"
3. Select "Markdown file" from the activity list
4. Give it a name (e.g., "Python Programming Guide")
5. Choose content source:
   - **Enter markdown text**: Type or paste markdown directly
   - **Upload file**: Upload an existing .md file

### 2. Add Content with Code Examples

In the content area, add markdown like this:

````markdown
# Python Basics

## Variables and Data Types

Python is dynamically typed. Here's how to work with basic data types:

```python
# Numbers
age = 25
price = 19.99
complex_num = 3 + 4j

# Strings
name = "Alice"
message = 'Hello, World!'
multiline = """This is a
multiline string"""

# Lists
fruits = ["apple", "banana", "orange"]
mixed = [1, "two", 3.0, True]

# Dictionaries
person = {
    "name": "Bob",
    "age": 30,
    "city": "New York"
}
```

## Functions

Here's how to define and use functions:

```python
def greet(name, greeting="Hello"):
    """Generate a personalized greeting."""
    return f"{greeting}, {name}!"

# Usage
print(greet("Alice"))           # Output: Hello, Alice!
print(greet("Bob", "Hi"))       # Output: Hi, Bob!
```
````

### 3. Test Display Options

- **Automatic**: Shows content inline (default)
- **Embed**: Forces inline display
- **Force download**: Shows download link only

### 4. Verify Syntax Highlighting

1. Save the activity and view it as a student
2. Verify that:
   - Code blocks have syntax highlighting with colors
   - Python keywords (def, return, import) are highlighted
   - Strings and comments have different colors
   - Each code block has a "Copy" button in the top-right
   - Clicking "Copy" changes to "✅ Copied!" briefly

### 5. Test Copy Functionality

1. Click the "Copy" button on any code block
2. Paste into a text editor
3. Verify the code is copied correctly without formatting

### 6. Test File Upload

1. Create a new Markdown file activity
2. Select "Upload file" option
3. Upload a .md file containing:

```markdown
# Test Document

This is a **test** of the markdown file upload.

## Code Example

```javascript
function fibonacci(n) {
    if (n <= 1) return n;
    return fibonacci(n - 1) + fibonacci(n - 2);
}

console.log(fibonacci(10)); // 55
```
```

4. Save and verify the content displays with highlighting

### 7. Mobile Testing

1. Access the activity on a mobile device
2. Verify:
   - Content is readable without horizontal scrolling
   - Code blocks can be scrolled horizontally if needed
   - Copy buttons are accessible
   - Text size is appropriate

### 8. Test Different Languages

Create content with multiple programming languages:

````markdown
## SQL Query
```sql
SELECT u.firstname, u.lastname, c.fullname
FROM mdl_user u
JOIN mdl_user_enrolments ue ON u.id = ue.userid
JOIN mdl_enrol e ON ue.enrolid = e.id
JOIN mdl_course c ON e.courseid = c.id
WHERE c.id = 2;
```

## PHP Function
```php
<?php
function calculate_grade($scores) {
    $total = array_sum($scores);
    $average = $total / count($scores);
    
    if ($average >= 90) return 'A';
    if ($average >= 80) return 'B';
    if ($average >= 70) return 'C';
    if ($average >= 60) return 'D';
    return 'F';
}
?>
```

## HTML/CSS
```html
<!DOCTYPE html>
<html>
<head>
    <style>
        .highlight {
            background-color: yellow;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <p class="highlight">Important text</p>
</body>
</html>
```
````

Each language should have appropriate syntax highlighting with language-specific keywords and constructs colored correctly.

### 9. Quick Functionality Check

- ✅ Create new Markdown file activity
- ✅ Enter markdown content with code blocks
- ✅ View the activity - see syntax highlighting
- ✅ Click "Copy" button - code copied to clipboard
- ✅ Upload .md file - content displays correctly
- ✅ View on mobile - responsive layout works
- ✅ Try different display modes - all work as expected