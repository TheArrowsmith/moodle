# Markdown Documents for Moodle 3.5

## The Problem

Traditional content delivery in Moodle was limited to basic HTML text areas and file uploads. Instructors who wanted to share formatted technical documentation, programming tutorials, or other structured content had to:
- Use the HTML editor which was cumbersome for writing structured documents
- Upload PDF files that weren't mobile-friendly or searchable
- Create external web pages and link to them
- Manually format content using complex HTML

This made it difficult to create professional-looking technical documentation directly within Moodle, especially for courses that needed clean, well-formatted instructional content.

## The New Feature

The Markdown Document activity module allows instructors to create and share beautifully formatted content using Markdown syntax. Key features include:

- **Markdown Editing**: Write content in simple Markdown syntax
- **File Upload Support**: Upload existing .md, .markdown, or .txt files
- **Flexible Display**: Choose between embedded display or download-only
- **Mobile Responsive**: Content adapts to all screen sizes

### Key Benefits:

- **Developer-Friendly**: Use familiar Markdown syntax instead of WYSIWYG editors
- **Version Control Ready**: Markdown files can be tracked in Git
- **Fast Content Creation**: Write documentation quickly without formatting hassles
- **Consistent Styling**: All markdown documents have uniform, professional appearance

## Implementation Notes

The feature is implemented as a standard Moodle activity module:

### Core Module (`mod_markdownfile`)

- Standard Moodle activity module structure
- Supports both database content storage and file uploads
- Uses Moodle's built-in Markdown parser (FORMAT_MARKDOWN)
- Provides three display modes: Automatic, Embed, and Force Download

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

### 4. Test File Upload

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

4. Save and verify the content displays correctly

### 5. Mobile Testing

1. Access the activity on a mobile device
2. Verify:
   - Content is readable without horizontal scrolling
   - Text size is appropriate
   - Display modes work correctly on mobile

### 6. Quick Functionality Check

- ✅ Create new Markdown file activity
- ✅ Enter markdown content including formatted text
- ✅ View the activity - see properly formatted content
- ✅ Upload .md file - content displays correctly
- ✅ View on mobile - responsive layout works
- ✅ Try different display modes - all work as expected