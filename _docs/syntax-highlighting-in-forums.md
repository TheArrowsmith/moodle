### **Feature 6: Syntax Highlighting in Forums**

*   **Objective:** To improve the readability of code shared in forums, making it easier for students and instructors to discuss technical problems.
*   **User Story:** "As a user, when I paste a block of code into a forum post, I want it to be automatically color-coded and formatted correctly, just like in a real code editor."
*   **Acceptance Criteria:**
    1.  Code snippets posted inside `<pre><code>` tags in forum posts are automatically rendered with syntax highlighting.
    2.  The highlighting works for common languages like Python and JavaScript.
    3.  The solution does not noticeably slow down page load times.

*   **Technical Specification:**
    *   **Moodle Component:** Modification to the active Moodle theme (e.g., "Boost"). This is a pure frontend change.
    *   **Technology:** [Prism.js](https://prismjs.com/), a lightweight and fast syntax highlighting library.
    *   **Implementation:**
        1.  Download a customized `prism.js` and `prism.css` file including support for common languages.
        2.  Add these files to the theme's directory.
        3.  In the theme's `lib.php` or a similar configuration file, add code to inject the CSS file into the page header and the JS file into the page footer.
        4.  A small, custom JS snippet will be added that calls `Prism.highlightAll()` on page load to activate the highlighting.

# Implementation Plan

## Overview
This feature adds automatic syntax highlighting to code snippets in forum posts using Prism.js, improving code readability for technical discussions.

## Existing Code Analysis

### Relevant Database Tables
No database changes required - this is a pure frontend enhancement.

### Key Code Components
1. **Forum Rendering**:
   - `/mod/forum/lib.php` - `forum_print_post()` function
   - Text formatting via `format_text()`
   - Forum templates in `/mod/forum/templates/`

2. **Theme System**:
   - `/theme/boost/` - Default theme structure
   - `/theme/boost/lib.php` - Theme callback functions
   - `/theme/boost/config.php` - Theme configuration

3. **Asset Injection**:
   - `$PAGE->requires` API for CSS/JS
   - Filter system for text transformation
   - Theme callbacks for global assets

4. **Text Processing**:
   - `/lib/weblib.php` - `format_text()` function
   - `/lib/filterlib.php` - Filter manager
   - Format types: FORMAT_HTML, FORMAT_PLAIN, FORMAT_MARKDOWN

## Current Code Flow

### Forum Post Display Flow
1. Post content retrieved from database
2. File URLs rewritten for proper linking
3. `format_text()` applies:
   - Security cleaning
   - Format conversion (if needed)
   - Active filters
4. HTML output to page

### Theme Asset Loading Flow
1. Theme config defines base SCSS/CSS
2. Modules add specific CSS/JS via `$PAGE->requires`
3. Theme callbacks can inject additional assets
4. Assets compiled and served to browser

## Implementation Approach

We have three options for implementing syntax highlighting:

### Option 1: Theme Integration (Recommended)
Add Prism.js globally via theme modifications. This ensures highlighting works everywhere, not just forums.

### Option 2: Custom Filter Plugin
Create a filter that detects code blocks and applies highlighting. More modular but requires plugin installation.

### Option 3: Forum-Specific Integration
Modify only the forum module. Limited scope but doesn't affect other areas.

## Implementation Details (Option 1: Theme Integration)

### Modified Files

1. **Theme Library Functions** (`/theme/boost/lib.php`):
   ```php
   /**
    * Inject additional theme assets
    *
    * @param moodle_page $page The page we are outputting to
    */
   function theme_boost_before_standard_html_head() {
       global $PAGE;
       
       // Add Prism CSS
       $PAGE->requires->css(new moodle_url('/theme/boost/javascript/prism/prism.css'));
       
       return '';
   }
   
   /**
    * Add JavaScript to footer
    */
   function theme_boost_before_footer() {
       global $PAGE;
       
       // Add Prism JS
       $PAGE->requires->js(new moodle_url('/theme/boost/javascript/prism/prism.js'), true);
       
       // Add initialization script
       $PAGE->requires->js_init_code('
           document.addEventListener("DOMContentLoaded", function() {
               if (typeof Prism !== "undefined") {
                   // Auto-highlight on page load
                   Prism.highlightAll();
                   
                   // Re-highlight after AJAX content loads
                   if (typeof M !== "undefined" && M.util && M.util.js_complete) {
                       M.util.js_complete(function() {
                           Prism.highlightAll();
                       });
                   }
               }
           });
       ', true);
       
       return '';
   }
   ```

2. **Theme Configuration** (`/theme/boost/config.php`):
   ```php
   // Add callbacks if not already present
   $THEME->prescsscallback = 'theme_boost_get_pre_scss';
   $THEME->extrascsscallback = 'theme_boost_get_extra_scss';
   $THEME->rendererfactory = 'theme_overridden_renderer_factory';
   $THEME->beforestandardhead = 'theme_boost_before_standard_html_head';
   $THEME->beforefooter = 'theme_boost_before_footer';
   ```

3. **Prism Assets Structure**:
   ```
   /theme/boost/javascript/prism/
   ├── prism.js          # Core + selected languages
   ├── prism.css         # Default theme
   └── prism-custom.css  # Moodle-specific overrides
   ```

4. **Custom Prism Configuration**:
   
   Download Prism.js with these components:
   - **Core**
   - **Languages**: Python, JavaScript, PHP, Java, C, C++, SQL, Bash, CSS, HTML
   - **Plugins**: Line Numbers, Copy to Clipboard
   - **Theme**: Default or custom theme matching Moodle

5. **Custom CSS Overrides** (`prism-custom.css`):
   ```css
   /* Ensure proper styling within Moodle forums */
   .forumpost .content pre[class*="language-"] {
       margin: 1em 0;
       border-radius: 4px;
       font-size: 0.9em;
   }
   
   .forumpost .content code[class*="language-"] {
       font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
   }
   
   /* Dark theme compatibility */
   .theme-dark pre[class*="language-"] {
       background: #1e1e1e;
   }
   
   /* Mobile responsive */
   @media (max-width: 768px) {
       pre[class*="language-"] {
           font-size: 0.8em;
       }
   }
   ```

### Alternative Implementation: Custom Filter Plugin

If theme modification isn't preferred, create a filter plugin:

```
/filter/syntaxhighlight/
├── version.php
├── filter.php
├── lib.php
├── lang/en/
│   └── filter_syntaxhighlight.php
└── thirdparty/
    └── prism/
        ├── prism.js
        └── prism.css
```

**Filter Class** (`filter.php`):
```php
class filter_syntaxhighlight extends moodle_text_filter {
    public function filter($text, array $options = array()) {
        // Find code blocks
        $pattern = '/<pre><code(.*?)>(.*?)<\/code><\/pre>/is';
        
        return preg_replace_callback($pattern, function($matches) {
            $attrs = $matches[1];
            $code = $matches[2];
            
            // Extract language from class attribute if present
            if (preg_match('/class=["\']language-(\w+)["\']/', $attrs, $langmatch)) {
                $lang = $langmatch[1];
            } else {
                $lang = 'none';
            }
            
            // Return with Prism classes
            return '<pre><code class="language-' . $lang . '">' . 
                   htmlspecialchars_decode($code) . '</code></pre>';
        }, $text);
    }
    
    public function setup($page, $context) {
        // Inject Prism assets
        $page->requires->css(new moodle_url('/filter/syntaxhighlight/thirdparty/prism/prism.css'));
        $page->requires->js(new moodle_url('/filter/syntaxhighlight/thirdparty/prism/prism.js'), true);
        $page->requires->js_init_call('Prism.highlightAll', array(), true);
    }
}
```

## Usage Patterns

### Markdown Format
```markdown
```python
def hello_world():
    print("Hello, World!")
```
```

### HTML Format
```html
<pre><code class="language-javascript">
function greet(name) {
    console.log(`Hello, ${name}!`);
}
</code></pre>
```

### Plain Text with Auto-Detection
```html
<pre><code>
# This will use auto-detection
for i in range(10):
    print(i)
</code></pre>
```

## Security Considerations

1. **XSS Prevention**:
   - Prism.js operates on already-escaped HTML
   - No additional escaping needed
   - Works with Moodle's existing security measures

2. **Performance**:
   - Lazy loading for large pages
   - Minimal CSS/JS footprint (~30KB gzipped)
   - No server-side processing

3. **Accessibility**:
   - Maintains semantic HTML structure
   - Screen reader friendly
   - Keyboard navigation preserved

## Development Checklist

1. [ ] Download customized Prism.js bundle
2. [ ] Create directory structure in theme
3. [ ] Add Prism files to theme
4. [ ] Modify theme/boost/lib.php
5. [ ] Update theme/boost/config.php
6. [ ] Create custom CSS overrides
7. [ ] Test in forums with various code snippets
8. [ ] Test with different text formats
9. [ ] Verify mobile responsiveness
10. [ ] Test with theme caching enabled
11. [ ] Add theme version bump
12. [ ] Document supported languages

## Testing Approach

1. **Forum Testing**:
   - Create posts with various language snippets
   - Test inline code vs code blocks
   - Verify nested code in quotes
   - Test editing and preview

2. **Format Testing**:
   - HTML format with explicit languages
   - Markdown format code blocks
   - Plain text auto-detection

3. **Theme Compatibility**:
   - Test with Boost theme variants
   - Verify dark mode compatibility
   - Check RTL language support

4. **Performance Testing**:
   - Page load time with/without highlighting
   - Large forum threads
   - Multiple code blocks per page

5. **Cross-browser Testing**:
   - Modern browsers (Chrome, Firefox, Safari, Edge)
   - Mobile browsers
   - Check console for errors
