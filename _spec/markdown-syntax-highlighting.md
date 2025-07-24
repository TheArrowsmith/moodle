# Syntax Highlighting for Markdown Documents - Implementation Specification

## Overview

This specification details the implementation of syntax highlighting for code blocks within the Moodle markdownfile activity module. The solution uses the modern React/Vite frontend stack to enhance the existing server-side markdown rendering with client-side syntax highlighting.

## Current System Analysis

### Markdownfile Module Structure

The markdownfile module is located at `mod/markdownfile/` with the following key files:

```
mod/markdownfile/
├── view.php              # Main display logic (TARGET FOR MODIFICATION)
├── lib.php               # Core module functions
├── mod_form.php          # Activity creation/editing form
├── lang/en/markdownfile.php  # Language strings
├── db/
│   ├── install.xml       # Database schema
│   └── access.php        # Capabilities
└── classes/
    └── event/            # Event classes
```

### Database Schema

The `markdownfile` table contains:
- `id` (int): Primary key
- `course` (int): Course ID
- `name` (varchar): Activity name
- `intro` (text): Activity description
- `content` (text): **Markdown content stored here**
- `contentformat` (int): Content format (FORMAT_MARKDOWN)
- `display` (int): Display mode (0=auto, 1=embed, 2=download)
- `displayoptions` (text): Serialized display options
- `timemodified` (int): Last modification timestamp

### Current Rendering Pipeline

1. **Content Retrieval** (`view.php` lines 78-92):
   ```php
   // Check if content is stored in database
   if (!empty($markdownfile->content)) {
       $content = $markdownfile->content;
   } else {
       // Try to load from file
       $fs = get_file_storage();
       $files = $fs->get_area_files($context->id, 'mod_markdownfile', 'content', 0, 'sortorder DESC, id ASC', false);
       if (count($files) > 0) {
           $file = reset($files);
           $content = $file->get_content();
       }
   }
   ```

2. **Markdown Processing** (`view.php` lines 95-103):
   ```php
   $formatoptions = new stdClass;
   $formatoptions->noclean = true;
   $formatoptions->overflowdiv = true;
   $formatoptions->context = $context;
   
   // Convert markdown to HTML using Moodle's built-in markdown parser
   $html = format_text($content, FORMAT_MARKDOWN, $formatoptions);
   ```

3. **HTML Output** (`view.php` lines 106-111):
   ```php
   if ($markdownfile->display == 0 || $markdownfile->display == 1) {
       echo $OUTPUT->box_start('generalbox center clearfix');
       echo $html; // THIS IS WHERE WE INJECT OUR REACT COMPONENT
       echo $OUTPUT->box_end();
   }
   ```

### Modern Frontend Stack

The system includes a modern React/Vite frontend stack:

**Structure:**
```
react-apps/
├── package.json          # Dependencies and build scripts
├── vite.config.js        # Vite build configuration
├── src/
│   ├── main.jsx          # React entry point and component registry
│   ├── components/       # React components directory
│   └── styles/           # Global styles
└── index.html            # Development test page
```

**Integration Helper:**
- `lib/react_helper.php`: PHP function `render_react_component()` for seamless React integration
- Automatic dev/prod mode detection based on `$CFG->debug`
- Component mounting via `window.MoodleReact.mount()`

## Technical Architecture

### Progressive Enhancement Strategy

The solution follows a progressive enhancement approach:

1. **Base Functionality**: Server-side markdown rendering continues to work exactly as before
2. **Enhancement Layer**: React component enhances the HTML with syntax highlighting when JavaScript is available
3. **Graceful Degradation**: Full functionality without JavaScript, syntax highlighting is purely cosmetic enhancement

### Component Design

**MarkdownRenderer React Component**:
- **Purpose**: Enhance existing HTML with syntax highlighting
- **Input**: HTML content (already processed by server)
- **Processing**: Find `<pre><code>` blocks and apply highlight.js
- **Output**: Enhanced HTML with syntax-highlighted code blocks
- **Features**: 
  - Language detection
  - Copy-to-clipboard functionality
  - Multiple theme support
  - Mobile-responsive design

## Implementation Details

### Step 1: Install Dependencies

**File**: `react-apps/package.json`

Add to dependencies:
```json
{
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0",
    "highlight.js": "^11.9.0"
  }
}
```

Run: `cd react-apps && npm install`

### Step 2: Create MarkdownRenderer Component

**File**: `react-apps/src/components/MarkdownRenderer.jsx`

```jsx
import React, { useEffect, useRef } from 'react';
import hljs from 'highlight.js';
import 'highlight.js/styles/github.css'; // Default theme
import styles from './MarkdownRenderer.module.css';

const MarkdownRenderer = ({ htmlContent, theme = 'github' }) => {
  const containerRef = useRef(null);

  useEffect(() => {
    if (!containerRef.current) return;

    // Find all code blocks
    const codeBlocks = containerRef.current.querySelectorAll('pre code');
    
    codeBlocks.forEach((block) => {
      // Apply syntax highlighting
      hljs.highlightElement(block);
      
      // Add copy button
      const copyButton = document.createElement('button');
      copyButton.className = styles.copyButton;
      copyButton.innerHTML = '📋 Copy';
      copyButton.onclick = () => copyCodeToClipboard(block, copyButton);
      
      // Wrap in container for positioning
      const wrapper = document.createElement('div');
      wrapper.className = styles.codeBlockWrapper;
      block.parentNode.insertBefore(wrapper, block.parentNode);
      wrapper.appendChild(block.parentNode);
      wrapper.appendChild(copyButton);
    });
  }, [htmlContent]);

  const copyCodeToClipboard = async (codeBlock, button) => {
    try {
      await navigator.clipboard.writeText(codeBlock.textContent);
      button.innerHTML = '✅ Copied!';
      setTimeout(() => {
        button.innerHTML = '📋 Copy';
      }, 2000);
    } catch (err) {
      console.error('Failed to copy code:', err);
      button.innerHTML = '❌ Failed';
      setTimeout(() => {
        button.innerHTML = '📋 Copy';
      }, 2000);
    }
  };

  return (
    <div 
      ref={containerRef}
      className={styles.markdownContent}
      dangerouslySetInnerHTML={{ __html: htmlContent }}
    />
  );
};

export default MarkdownRenderer;
```

### Step 3: Component Styles

**File**: `react-apps/src/components/MarkdownRenderer.module.css`

```css
.markdownContent {
  /* Base styling for enhanced markdown content */
}

.codeBlockWrapper {
  position: relative;
  margin: 1rem 0;
}

.codeBlockWrapper pre {
  margin: 0;
  border-radius: 8px;
  overflow-x: auto;
  background: #f6f8fa;
  border: 1px solid #e1e4e8;
}

.copyButton {
  position: absolute;
  top: 8px;
  right: 8px;
  background: rgba(255, 255, 255, 0.9);
  border: 1px solid #e1e4e8;
  border-radius: 4px;
  padding: 4px 8px;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s ease;
  z-index: 10;
}

.copyButton:hover {
  background: white;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.codeBlockWrapper:hover .copyButton {
  opacity: 1;
}

/* Responsive design */
@media (max-width: 768px) {
  .copyButton {
    position: static;
    display: block;
    margin: 8px 0;
    width: 100%;
  }
  
  .codeBlockWrapper pre {
    border-radius: 8px 8px 0 0;
  }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
  .codeBlockWrapper pre {
    background: #0d1117;
    border-color: #30363d;
  }
  
  .copyButton {
    background: rgba(0, 0, 0, 0.9);
    color: white;
    border-color: #30363d;
  }
}
```

### Step 4: Register Component

**File**: `react-apps/src/main.jsx`

Add the import and register the component:

```jsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import HelloMoodle from './components/HelloMoodle';
import MarkdownRenderer from './components/MarkdownRenderer'; // ADD THIS

// Component registry
const components = {
  HelloMoodle,
  MarkdownRenderer, // ADD THIS
};

// Global API
window.MoodleReact = {
  components,
  mount: (componentName, selector, props = {}) => {
    const element = document.querySelector(selector);
    if (!element) {
      console.error(`Element not found: ${selector}`);
      return;
    }

    const Component = components[componentName];
    if (!Component) {
      console.error(`Component not found: ${componentName}`);
      return;
    }

    const root = createRoot(element);
    root.render(React.createElement(Component, props));
    return root;
  }
};

console.log('MoodleReact initialized with components:', Object.keys(components));
```

### Step 5: Integrate with PHP

**File**: `mod/markdownfile/view.php`

**BEFORE** (lines 106-111):
```php
if ($markdownfile->display == 0 || $markdownfile->display == 1) {
    // Auto or embed - display the content
    echo $OUTPUT->box_start('generalbox center clearfix');
    echo $html;
    echo $OUTPUT->box_end();
}
```

**AFTER**:
```php
if ($markdownfile->display == 0 || $markdownfile->display == 1) {
    // Auto or embed - display the content
    echo $OUTPUT->box_start('generalbox center clearfix');
    
    // Check if modern frontend is available and enabled
    if (file_exists(__DIR__ . '/../../lib/react_helper.php')) {
        require_once(__DIR__ . '/../../lib/react_helper.php');
        
        // Create unique element ID for this instance
        $elementId = 'markdown-content-' . $markdownfile->id;
        
        // Render with React enhancement
        render_react_component('MarkdownRenderer', $elementId, [
            'htmlContent' => $html,
            'theme' => 'github' // Could be made configurable
        ], [
            'class' => 'enhanced-markdown-content'
        ]);
    } else {
        // Fallback to standard HTML output
        echo $html;
    }
    
    echo $OUTPUT->box_end();
}
```

### Step 6: Build Assets

**Development**:
```bash
cd react-apps
npm run dev
```

**Production**:
```bash
cd react-apps
npm run build
```

This creates/updates:
- `react-dist/moodle-react.iife.js` (production bundle)
- `react-dist/style.css` (compiled styles)

## Configuration Options

### Theme Selection

The component supports multiple highlight.js themes. To change themes, modify the import in `MarkdownRenderer.jsx`:

```jsx
// Available themes:
import 'highlight.js/styles/github.css';           // GitHub (light)
import 'highlight.js/styles/github-dark.css';      // GitHub (dark)
import 'highlight.js/styles/vs.css';               // Visual Studio
import 'highlight.js/styles/atom-one-dark.css';    // Atom One Dark
import 'highlight.js/styles/monokai.css';          // Monokai
```

### Language Support

By default, highlight.js includes all languages. For smaller bundle size, you can import specific languages:

```jsx
import hljs from 'highlight.js/lib/core';
import javascript from 'highlight.js/lib/languages/javascript';
import python from 'highlight.js/lib/languages/python';
import php from 'highlight.js/lib/languages/php';
import sql from 'highlight.js/lib/languages/sql';

hljs.registerLanguage('javascript', javascript);
hljs.registerLanguage('python', python);
hljs.registerLanguage('php', php);
hljs.registerLanguage('sql', sql);
```

### Display Mode Integration

The enhancement works with all existing display modes:
- **Auto (0)**: Enhanced rendering with syntax highlighting
- **Embed (1)**: Enhanced rendering with syntax highlighting  
- **Download (2)**: No changes, continues to show download link

## Testing Strategy

### Manual Testing Scenarios

1. **Basic Functionality**:
   - Create markdownfile activity with code blocks
   - Verify syntax highlighting appears
   - Test copy-to-clipboard functionality
   - Check mobile responsiveness

2. **Language Detection**:
   ```markdown
   ```javascript
   function hello() {
       console.log("Hello, World!");
   }
   ```
   
   ```python
   def hello():
       print("Hello, World!")
   ```
   
   ```php
   <?php
   function hello() {
       echo "Hello, World!";
   }
   ?>
   ```

3. **Graceful Degradation**:
   - Disable JavaScript in browser
   - Verify content still displays correctly
   - Check that functionality remains intact

4. **Development vs Production**:
   - Test with `$CFG->debug >= DEBUG_DEVELOPER` (dev mode)
   - Test with `$CFG->debug < DEBUG_DEVELOPER` (production mode)
   - Verify both load appropriate assets

### Browser Compatibility

**Minimum Requirements**:
- Chrome 88+
- Firefox 85+
- Safari 14+
- Edge 88+

**Fallback Support**:
- Internet Explorer: No syntax highlighting, base functionality works
- Older browsers: Progressive enhancement ensures core functionality

### Performance Considerations

**Bundle Size**:
- highlight.js: ~45KB gzipped (all languages)
- React component: ~2KB gzipped
- Total impact: ~47KB additional JavaScript

**Optimization Options**:
- Language-specific builds: Reduce to ~15KB gzipped
- Dynamic imports: Load languages on-demand
- CSS-only fallback: Basic styling without JavaScript

## Deployment Instructions

### Step-by-Step Implementation

1. **Install Dependencies**:
   ```bash
   cd /path/to/moodle/react-apps
   npm install highlight.js
   ```

2. **Create Component Files**:
   - Create `react-apps/src/components/MarkdownRenderer.jsx`
   - Create `react-apps/src/components/MarkdownRenderer.module.css`
   - Update `react-apps/src/main.jsx`

3. **Build Assets**:
   ```bash
   npm run build
   ```

4. **Update PHP**:
   - Modify `mod/markdownfile/view.php` as specified
   - Test integration

5. **Verify Installation**:
   - Create test markdownfile activity
   - Add code blocks with different languages
   - Check syntax highlighting and copy functionality

### Configuration Files

**vite.config.js** (no changes needed, but for reference):
```javascript
import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../react-dist',
    lib: {
      entry: 'src/main.jsx',
      name: 'MoodleReact',
      fileName: 'moodle-react',
      formats: ['iife']
    },
    rollupOptions: {
      external: [],
      output: {
        globals: {}
      }
    }
  }
})
```

### Troubleshooting

**Common Issues**:

1. **Component Not Loading**:
   - Check browser console for errors
   - Verify `window.MoodleReact` is available
   - Ensure build completed successfully

2. **Styles Not Applied**:
   - Check `react-dist/style.css` exists
   - Verify CSS import in component
   - Check for CSS conflicts

3. **Copy Button Not Working**:
   - Verify HTTPS context (clipboard API requirement)
   - Check browser permissions
   - Test fallback behavior

4. **Development Server Issues**:
   - Ensure Vite dev server is running on port 5173
   - Check `$CFG->debug` setting
   - Verify Content Security Policy allows localhost:5173

**Debug Mode**:
Enable debug logging in the component:
```jsx
const MarkdownRenderer = ({ htmlContent, theme = 'github', debug = false }) => {
  if (debug) {
    console.log('MarkdownRenderer props:', { htmlContent, theme });
  }
  // ... rest of component
};
```

## Future Enhancements

### Potential Improvements

1. **Theme Selection UI**:
   - Add theme selector to activity settings
   - User preference storage
   - Dynamic theme switching

2. **Advanced Copy Features**:
   - Copy with/without line numbers
   - Copy specific line ranges
   - Export to file functionality

3. **Language Enhancement**:
   - Custom language definitions
   - Syntax error detection
   - Code validation integration

4. **Accessibility**:
   - Keyboard navigation for code blocks
   - Screen reader optimizations
   - High contrast mode support

5. **Performance**:
   - Virtual scrolling for large code blocks
   - Lazy loading of highlight.js languages
   - Web Workers for syntax highlighting

### Integration Opportunities

1. **Activity Settings**:
   - Theme selection in mod_form.php
   - Language preferences
   - Copy button toggle

2. **Site Administration**:
   - Global theme defaults
   - Language subset configuration
   - Performance settings

3. **Mobile App**:
   - Responsive enhancements
   - Touch-friendly copy buttons
   - Offline functionality

## Conclusion

This implementation provides a robust, progressive enhancement to the existing markdownfile module. It maintains full backward compatibility while adding modern syntax highlighting capabilities using the established React/Vite frontend architecture. The solution is performant, accessible, and provides a solid foundation for future enhancements.