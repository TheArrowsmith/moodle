# Frontend Modernization Specification: React Integration for Moodle

## Executive Summary

This specification provides a complete, self-contained guide for adding React components to an existing Moodle 3 installation. The approach creates a parallel, modern build system that operates independently of Moodle's legacy AMD/RequireJS system, requiring NO modifications to Moodle core files.

**Key Decision**: React components will be loaded via standard `<script>` tags, completely bypassing the AMD system for maximum simplicity.

## Prerequisites

- Access to Moodle 3 installation directory
- Node.js 18+ installed (separate from Moodle's Node 8.9)
- Basic knowledge of React and PHP
- Ability to modify Moodle's index.php or create a custom plugin

## Complete File Structure

After implementation, your directory structure should look like this:

```
moodle-3/                           # Your Moodle root directory
├── index.php                       # Modified to include React test
├── config.php                      # Your existing Moodle config
├── lib/
│   ├── react_helper.php           # NEW: PHP helper for React components
│   └── [other Moodle files]
├── react-apps/                    # NEW: React development directory
│   ├── package.json               # React project configuration
│   ├── vite.config.js            # Vite bundler configuration
│   ├── index.html                # Standalone development page
│   ├── .gitignore                # Ignore node_modules, etc.
│   ├── node_modules/             # Created by npm install
│   └── src/
│       ├── main.jsx              # React entry point & global API
│       └── components/
│           ├── HelloMoodle.jsx   # Test component
│           └── HelloMoodle.module.css  # Component styles
├── react-dist/                   # NEW: Production builds directory
│   ├── .gitignore               # Ignore built files in git
│   └── moodle-react.iife.js     # Created by npm run build
└── [other Moodle directories]
```

## Current State Analysis

### Existing Technology Stack
- **Build System**: Grunt (from 2014)
- **Module System**: AMD with RequireJS
- **Node Version**: 8.9 (End of Life since 2019)
- **CSS Processing**: SCSS/LESS compilation via Grunt
- **Legacy Code**: YUI modules still present
- **No Support For**: JSX, ES6+ modules, Hot Module Replacement, modern bundling

### Limitations
1. Outdated tooling prevents use of modern JavaScript features
2. No React/Vue/modern framework support
3. Slow development feedback loop (manual builds)
4. Complex integration requirements for new developers
5. Performance limitations from old bundling strategies

## Proposed Solution

### Overview
Implement a parallel, modern build system using React and Vite that operates independently of Moodle's AMD system. React components will be loaded via standard `<script>` tags and mount to designated DOM elements.

### Architecture

```
moodle-3/
├── react-apps/              # Modern React development environment
│   ├── package.json         # Latest Node.js and dependencies
│   ├── vite.config.js       # Build configuration
│   ├── index.html          # Standalone development page
│   └── src/
│       ├── main.jsx        # Entry point and global API
│       ├── components/     # React components
│       └── utils/          # Shared utilities
├── react-dist/             # Production bundles (git-ignored in dev)
│   ├── manifest.json       # Build manifest for cache busting
│   └── *.js               # Built component bundles
└── [existing moodle files]
```

### Technology Choices

#### Build Tool: Vite 5.x
- **Rationale**: 
  - Fastest build times in the ecosystem
  - Native ES modules in development
  - Excellent React support out of the box
  - Simple configuration
  - Built-in HMR and Fast Refresh

#### Framework: React 18.x
- **Rationale**:
  - Mature ecosystem
  - Excellent TypeScript support
  - Hooks provide clean component APIs
  - Concurrent features for better performance
  - Wide developer familiarity

#### Styling Strategy: CSS Modules
- **Rationale**:
  - Scoped styles prevent conflicts with Moodle
  - Works well with Vite
  - No runtime overhead
  - Familiar CSS syntax

## Implementation Plan

### Phase 1: Infrastructure Setup

#### 1.1 Create React Development Environment

**IMPORTANT**: Execute these commands from the Moodle root directory (where index.php is located).

```bash
# From moodle-3 root directory
mkdir -p react-apps/src/components
cd react-apps

# Initialize new Node.js project (separate from Moodle's package.json)
npm init -y

# Install React and build dependencies
npm install --save react@18 react-dom@18
npm install --save-dev vite@5 @vitejs/plugin-react@4

# Create .gitignore for react-apps
echo "node_modules/
dist/
.vite/
*.log" > .gitignore

# Create placeholder files
touch vite.config.js
touch src/main.jsx
touch src/components/HelloMoodle.jsx
touch src/components/HelloMoodle.module.css
touch index.html
```

#### 1.2 Complete package.json Configuration

Create this exact `package.json` in the `react-apps` directory:

```json
{
  "name": "moodle-react-apps",
  "version": "1.0.0",
  "private": true,
  "type": "module",
  "scripts": {
    "dev": "vite",
    "build": "vite build",
    "preview": "vite preview",
    "clean": "rm -rf ../react-dist/*"
  },
  "dependencies": {
    "react": "^18.2.0",
    "react-dom": "^18.2.0"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^4.2.0",
    "vite": "^5.0.0"
  }
}
```

#### 1.3 Vite Configuration

Create `vite.config.js` in the `react-apps` directory with this exact content:

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import { resolve } from 'path';

export default defineConfig({
  plugins: [react()],
  build: {
    outDir: '../react-dist',
    emptyOutDir: true,
    lib: {
      entry: resolve(__dirname, 'src/main.jsx'),
      formats: ['iife'],
      name: 'MoodleReact',
      fileName: (format) => `moodle-react.${format}.js`
    },
    rollupOptions: {
      external: [],
      output: {
        // Ensure React and ReactDOM are bundled
        globals: {},
        // Single file output
        inlineDynamicImports: true,
      }
    }
  },
  server: {
    port: 5173,
    cors: true,
    hmr: {
      protocol: 'ws',
      host: 'localhost'
    }
  }
});
```

#### 1.4 Development HTML Page

Create `index.html` in the `react-apps` directory for standalone development:

```html
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Moodle React Development</title>
</head>
<body>
  <h1>Moodle React Components Development</h1>
  <p>This page is for standalone development. Components will be integrated into Moodle.</p>
  
  <h2>Test Components:</h2>
  
  <div id="test-hello-moodle" 
       data-react-component="HelloMoodle" 
       data-react-props='{"userName":"Test User","courseName":"Development Course"}'>
  </div>
  
  <script type="module" src="/src/main.jsx"></script>
</body>
</html>
```

### Phase 2: Global API Development

#### 2.1 Main Entry Point

Create `src/main.jsx` with this exact content:

```javascript
// src/main.jsx
import React from 'react';
import ReactDOM from 'react-dom/client';

// Component imports - start with just HelloMoodle
import HelloMoodle from './components/HelloMoodle';
// Future components (commented out initially):
// import CourseGrid from './components/CourseGrid';
// import FileUploader from './components/FileUploader';

// Global API exposed to Moodle
window.MoodleReact = {
  components: {
    HelloMoodle,
    // CourseGrid,
    // FileUploader
  },
  
  mount(componentName, elementId, props = {}) {
    const Component = this.components[componentName];
    if (!Component) {
      console.error(`Component ${componentName} not found`);
      return;
    }
    
    const container = document.getElementById(elementId);
    if (!container) {
      console.error(`Element ${elementId} not found`);
      return;
    }
    
    const root = ReactDOM.createRoot(container);
    root.render(
      <React.StrictMode>
        <Component {...props} />
      </React.StrictMode>
    );
    
    // Store root for unmounting
    container._reactRoot = root;
  },
  
  unmount(elementId) {
    const container = document.getElementById(elementId);
    if (container && container._reactRoot) {
      container._reactRoot.unmount();
      delete container._reactRoot;
    }
  }
};

// Auto-mount components with data attributes
document.addEventListener('DOMContentLoaded', () => {
  const reactMounts = document.querySelectorAll('[data-react-component]');
  
  reactMounts.forEach(element => {
    const componentName = element.dataset.reactComponent;
    const props = element.dataset.reactProps ? 
      JSON.parse(element.dataset.reactProps) : {};
    
    window.MoodleReact.mount(componentName, element.id, props);
  });
});
```

### Phase 3: Test Component Implementation

#### 3.1 Hello Moodle Component

Create `src/components/HelloMoodle.jsx` with this exact content:

```jsx
// src/components/HelloMoodle.jsx
import React, { useState } from 'react';
import styles from './HelloMoodle.module.css';

const HelloMoodle = ({ userName = 'User', courseName = 'Course' }) => {
  const [count, setCount] = useState(0);
  const [showDetails, setShowDetails] = useState(false);

  return (
    <div className={styles.container}>
      <div className={styles.header}>
        <h3>🚀 React is working in Moodle!</h3>
        <span className={styles.badge}>Modern Frontend</span>
      </div>
      
      <div className={styles.content}>
        <p>Hello <strong>{userName}</strong>, you're in <strong>{courseName}</strong></p>
        
        <div className={styles.actions}>
          <button 
            className={styles.button}
            onClick={() => setCount(count + 1)}
          >
            Clicked {count} times
          </button>
          
          <button 
            className={styles.button}
            onClick={() => setShowDetails(!showDetails)}
          >
            {showDetails ? 'Hide' : 'Show'} Details
          </button>
        </div>
        
        {showDetails && (
          <div className={styles.details}>
            <h4>React Features Working:</h4>
            <ul>
              <li>✓ Component rendering</li>
              <li>✓ State management (count: {count})</li>
              <li>✓ Props passing (user: {userName})</li>
              <li>✓ Event handling</li>
              <li>✓ Conditional rendering</li>
              <li>✓ CSS Modules</li>
            </ul>
          </div>
        )}
      </div>
    </div>
  );
};

export default HelloMoodle;
```

#### 3.2 Component Styles

Create `src/components/HelloMoodle.module.css` with this exact content:

```css
/* src/components/HelloMoodle.module.css */
.container {
  background: #f8f9fa;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  padding: 20px;
  margin: 20px 0;
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

.header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.header h3 {
  margin: 0;
  color: #212529;
}

.badge {
  background: #007bff;
  color: white;
  padding: 4px 12px;
  border-radius: 16px;
  font-size: 12px;
  font-weight: 600;
}

.content p {
  color: #495057;
  margin-bottom: 16px;
}

.actions {
  display: flex;
  gap: 12px;
  margin-bottom: 16px;
}

.button {
  background: #007bff;
  color: white;
  border: none;
  padding: 8px 16px;
  border-radius: 4px;
  cursor: pointer;
  font-size: 14px;
  transition: background 0.2s;
}

.button:hover {
  background: #0056b3;
}

.details {
  background: white;
  padding: 16px;
  border-radius: 4px;
  border: 1px solid #dee2e6;
}

.details h4 {
  margin: 0 0 12px 0;
  color: #212529;
}

.details ul {
  margin: 0;
  padding-left: 20px;
  color: #495057;
}

.details li {
  margin-bottom: 8px;
}
```

### Phase 4: Moodle Integration

#### 4.1 Directory Structure for Production

Create the production directory:
```bash
# From Moodle root
mkdir -p react-dist
echo "# React production builds
*.js
*.map
manifest.json" > react-dist/.gitignore
```

#### 4.2 PHP Helper Function

Create a new file `lib/react_helper.php` with this exact content:

```php
// lib/react_helper.php
<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Render a React component in Moodle
 * 
 * @param string $component Component name as registered in window.MoodleReact.components
 * @param string $elementid HTML element ID for mounting (must be unique on page)
 * @param array $props Props to pass to the component
 * @param array $options Additional options
 * @return string HTML output
 */
function render_react_component($component, $elementid, $props = [], $options = []) {
    global $PAGE, $CFG;
    
    // Default options
    $options = array_merge([
        'wrapdiv' => true,
        'class' => '',
        'return' => false
    ], $options);
    
    // Load React bundle
    if (!empty($CFG->debug) && $CFG->debug >= DEBUG_DEVELOPER) {
        // Development mode - use Vite dev server
        $PAGE->requires->js(new moodle_url('http://localhost:5173/@vite/client'), true);
        $PAGE->requires->js(new moodle_url('http://localhost:5173/src/main.jsx'), true);
    } else {
        // Production mode - use built bundle
        $PAGE->requires->js(new moodle_url('/react-dist/moodle-react.iife.js'), true);
    }
    
    // Prepare props as JSON
    $propsjson = json_encode($props, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    
    // Build HTML
    $html = '';
    
    if ($options['wrapdiv']) {
        $class = !empty($options['class']) ? ' class="' . s($options['class']) . '"' : '';
        $html .= '<div id="' . s($elementid) . '"' . $class . ' data-react-component="' . s($component) . '" data-react-props=\'' . $propsjson . '\'></div>';
    }
    
    // Add mounting script
    $html .= '<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Wait for React to load
        var checkReact = setInterval(function() {
            if (window.MoodleReact && window.MoodleReact.mount) {
                clearInterval(checkReact);
                window.MoodleReact.mount(
                    "' . s($component) . '",
                    "' . s($elementid) . '",
                    ' . $propsjson . '
                );
            }
        }, 100);
    });
    </script>';
    
    if ($options['return']) {
        return $html;
    }
    
    echo $html;
}
```

#### 4.3 Integration Example - Site Homepage

To test the React integration, modify `index.php` in the Moodle root directory. Add this code after the main content output (typically after `echo $OUTPUT->footer();` is called):

```php
// In index.php, find the line that outputs the footer (around line 90-100)
// It will look like: echo $OUTPUT->footer();
// Add this code BEFORE that line:

// React component test
require_once($CFG->libdir . '/react_helper.php');

// Prepare props with real Moodle data
$props = [
    'userName' => fullname($USER),
    'courseName' => get_string('sitehome')
];

// Render the React component
echo '<div class="container-fluid mt-3">';
echo '<h2>React Integration Test</h2>';
render_react_component('HelloMoodle', 'react-hello-moodle', $props, [
    'class' => 'react-test-component'
]);
echo '</div>';

// Then the existing footer line:
echo $OUTPUT->footer();
```

**Alternative: Custom Block Integration**

If modifying index.php is not desired, create a custom HTML block:
1. Go to Site administration → Plugins → Blocks → HTML
2. Add a new HTML block to the front page
3. In the block content, add:
```html
<div id="react-hello-block"></div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // This assumes React is already loaded
    if (window.MoodleReact) {
        window.MoodleReact.mount('HelloMoodle', 'react-hello-block', {
            userName: 'Block User',
            courseName: 'HTML Block Test'
        });
    }
});
</script>
```

### Phase 5: Development Workflow

#### 5.1 Initial Setup Verification

Before starting development, verify your setup:

```bash
# From Moodle root, check structure
ls -la react-apps/
# Should show: package.json, vite.config.js, src/, index.html

ls -la react-apps/src/components/
# Should show: HelloMoodle.jsx, HelloMoodle.module.css

# Install dependencies if not done
cd react-apps
npm install
```

#### 5.2 Development Mode

**Step 1: Enable Moodle Debug Mode**

In `config.php`, add or modify:
```php
$CFG->debug = 32767;  // Show all debugging messages
$CFG->debugdisplay = 1;  // Display errors on screen
```

**Step 2: Start Development Servers**

```bash
# Terminal 1: Your existing Moodle server (Apache/Nginx/MAMP/etc)
# Moodle should be accessible at http://localhost (or your configured URL)

# Terminal 2: Start React dev server
cd react-apps
npm run dev

# Output should show:
# VITE v5.x.x  ready in xxx ms
# ➜  Local:   http://localhost:5173/
# ➜  Network: use --host to expose
```

**Step 3: Test Standalone First**

1. Open http://localhost:5173 in browser
2. You should see the test page with HelloMoodle component
3. Verify the counter button works (HMR test)
4. Make a change to HelloMoodle.jsx and save
5. Component should update without page refresh

**Step 4: Test in Moodle**

1. Open your Moodle site (e.g., http://localhost/moodle)
2. Navigate to the homepage
3. You should see "React Integration Test" section
4. The HelloMoodle component should appear with real user data
5. Changes in React code will auto-refresh in Moodle too

#### 5.3 Production Build

```bash
# From react-apps directory
npm run build

# Output:
# vite v5.x.x building for production...
# ✓ xxx modules transformed.
# ../react-dist/moodle-react.iife.js  xxx kB

# Verify build output
ls -la ../react-dist/
# Should show: moodle-react.iife.js

# Test production build
# 1. Turn off debug mode in config.php
# 2. Reload Moodle - should load from react-dist/
```

#### 5.4 Troubleshooting Guide

**React component not appearing:**
1. Check browser console for errors
2. Verify `window.MoodleReact` exists in console
3. Check Network tab for script loading
4. Ensure element ID matches in PHP and JavaScript

**Development server issues:**
```bash
# Port already in use
npx kill-port 5173
npm run dev

# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

**Production build issues:**
```bash
# Clean and rebuild
npm run clean
npm run build

# Check build output for errors
# Ensure react-dist directory exists and is writable
```

## Future Components Roadmap

### Priority 1 - Essential Components
1. **EnhancedFileUploader**
   - Drag & drop interface
   - Progress indicators
   - File preview
   - Integration with Moodle's file API

2. **RealTimeNotifications**
   - WebSocket connection
   - Toast notifications
   - Notification center dropdown

3. **InteractiveCourseGrid**
   - Card-based layout
   - Filtering and search
   - Favorite courses
   - Progress indicators

### Priority 2 - Enhanced UX
1. **ModernForumInterface**
   - Threaded discussions
   - Real-time updates
   - Rich text editor
   - Inline media

2. **GradebookDataTable**
   - Sortable columns
   - Inline editing
   - Export functionality
   - Visualizations

3. **CalendarWidget**
   - Month/week/day views
   - Drag & drop events
   - Quick add functionality

### Priority 3 - Advanced Features
1. **CollaborativeEditor**
   - Real-time collaboration
   - Version history
   - Comments and suggestions

2. **AnalyticsDashboard**
   - Interactive charts
   - Custom date ranges
   - Export capabilities

## Migration Strategy

### Gradual Adoption
1. Start with non-critical UI enhancements
2. Gather user feedback on React components
3. Develop React versions of high-impact interfaces
4. Maintain both versions during transition
5. Deprecate AMD versions once React is proven

### Compatibility Maintenance
- React components are opt-in
- No changes to core Moodle functionality
- Existing AMD modules continue to work
- Theme compatibility preserved

## Success Metrics

### Technical Metrics
- Page load time improvement
- Development velocity increase
- Bundle size optimization
- Browser compatibility coverage

### Developer Experience
- Time to implement new features
- Onboarding time for new developers
- Code reusability metrics
- Development feedback cycle time

### User Experience
- User satisfaction scores
- Feature adoption rates
- Performance perception
- Accessibility compliance

## Risk Mitigation

### Identified Risks
1. **Browser Compatibility**
   - Mitigation: Transpile to ES5, polyfill as needed
   
2. **Theme Conflicts**
   - Mitigation: CSS Modules, scoped styles
   
3. **Performance Impact**
   - Mitigation: Code splitting, lazy loading
   
4. **Maintenance Burden**
   - Mitigation: Clear documentation, automated testing

## Complete Implementation Checklist

Use this checklist to ensure successful implementation:

### Prerequisites Check
- [ ] Moodle 3 installation working and accessible
- [ ] Node.js 18+ installed (`node --version` shows 18.0.0 or higher)
- [ ] npm installed (`npm --version`)
- [ ] Write access to Moodle directory
- [ ] Ability to edit config.php and index.php

### Phase 1: Setup
- [ ] Created `/react-apps/` directory in Moodle root
- [ ] Created all subdirectories (`src/components/`)
- [ ] Created `package.json` with exact content from 1.2
- [ ] Created `vite.config.js` with exact content from 1.3
- [ ] Created `index.html` with exact content from 1.4
- [ ] Ran `npm install` successfully
- [ ] Created `.gitignore` in react-apps

### Phase 2: React Components
- [ ] Created `src/main.jsx` with exact content from 2.1
- [ ] Created `src/components/HelloMoodle.jsx` with exact content from 3.1
- [ ] Created `src/components/HelloMoodle.module.css` with exact content from 3.2

### Phase 3: Moodle Integration
- [ ] Created `/react-dist/` directory in Moodle root
- [ ] Created `lib/react_helper.php` with exact content from 4.2
- [ ] Added test code to `index.php` before footer output

### Phase 4: Testing
- [ ] Enabled debug mode in `config.php`
- [ ] Started React dev server (`npm run dev`)
- [ ] Verified standalone page works at http://localhost:5173
- [ ] Verified component appears on Moodle homepage
- [ ] Tested counter button functionality
- [ ] Verified HMR works (edit component, see changes without refresh)

### Phase 5: Production
- [ ] Ran `npm run build` successfully
- [ ] Verified `moodle-react.iife.js` exists in `/react-dist/`
- [ ] Disabled debug mode in `config.php`
- [ ] Verified production build loads correctly

### Verification Tests
- [ ] Console shows no JavaScript errors
- [ ] `window.MoodleReact` is defined in browser console
- [ ] Component renders with correct user data
- [ ] Styles are applied correctly (gray box with blue buttons)
- [ ] Both dev and production modes work

## Common Pitfalls to Avoid

1. **Wrong Directory**: Ensure all commands are run from correct directories
2. **Missing Dependencies**: Always run `npm install` after creating package.json
3. **Port Conflicts**: Ensure port 5173 is free for Vite
4. **File Paths**: Use exact file paths as specified (case-sensitive on Linux/Mac)
5. **PHP Syntax**: Don't forget semicolons and proper PHP tags
6. **Debug Mode**: Remember to enable for development, disable for production

## Expected Outcome

When successfully implemented, you will have:

1. A modern React development environment running alongside Moodle
2. Hot Module Replacement for instant development feedback
3. A test component visible on the Moodle homepage showing:
   - "🚀 React is working in Moodle!" heading
   - User's actual name and "Site home" as course
   - Working counter button
   - Show/Hide details functionality
4. Production builds that work without the dev server
5. A foundation for adding more React components

## Next Steps After Implementation

1. Create additional React components following the HelloMoodle pattern
2. Add components to existing Moodle pages using `render_react_component()`
3. Build a component library for common UI patterns
4. Consider adding TypeScript for better type safety
5. Implement more complex state management if needed (Context API, Zustand)

## Conclusion

This specification provides a complete, step-by-step guide to modernizing Moodle's frontend with React. The approach:

- Requires NO modifications to Moodle core files (except index.php for testing)
- Works completely independently of the legacy AMD/RequireJS system
- Provides modern development experience with HMR
- Maintains full backward compatibility
- Can be removed cleanly if needed

The implementation creates a sustainable path for gradually modernizing Moodle's UI while maintaining stability and compatibility with existing functionality.