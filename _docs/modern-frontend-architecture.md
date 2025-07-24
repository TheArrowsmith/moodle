# Modern Frontend Architecture for Moodle 3

## Overview

This document describes the modern React-based frontend architecture that operates alongside Moodle's legacy AMD/RequireJS system. This approach allows gradual modernization without breaking existing functionality.

## Architecture Summary

```
moodle-3/
├── react-apps/          # Modern React development environment
│   ├── src/            # React components and entry point
│   ├── package.json    # Node.js 18+ dependencies
│   └── vite.config.js  # Build configuration
├── react-dist/         # Production bundles
│   └── moodle-react.iife.js  # Single bundled file
└── lib/
    └── react_helper.php  # PHP integration helper
```

### Key Design Decisions

1. **Complete Independence**: React system operates independently of AMD/RequireJS
2. **Script Tag Loading**: Components load via `<script>` tags, not AMD modules
3. **Global API**: `window.MoodleReact` provides mounting/unmounting interface
4. **IIFE Bundle**: Single self-contained bundle includes React, ReactDOM, and all components

## Development Workflow

### Starting Development

```bash
cd react-apps
npm run dev
# Visit http://localhost:5173 for standalone testing
```

### Building for Production

```bash
cd react-apps
npm run build
# Creates react-dist/moodle-react.iife.js
```

## Adding New React Components

### 1. Create Component File

Create `react-apps/src/components/YourComponent.jsx`:

```jsx
import React, { useState, useEffect } from 'react';
import styles from './YourComponent.module.css';

const YourComponent = ({ userId, courseName }) => {
  const [data, setData] = useState(null);
  
  useEffect(() => {
    // Component logic here
  }, [userId]);
  
  return (
    <div className={styles.container}>
      {/* Component JSX */}
    </div>
  );
};

export default YourComponent;
```

### 2. Create Component Styles

Create `react-apps/src/components/YourComponent.module.css`:

```css
.container {
  /* Scoped styles - won't conflict with Moodle */
}
```

### 3. Register Component

Edit `react-apps/src/main.jsx`:

```javascript
// Add import
import YourComponent from './components/YourComponent';

// Register in global API
window.MoodleReact = {
  components: {
    HelloMoodle,
    YourComponent, // Add here
  },
  // ... rest of API
};
```

### 4. Use in Moodle

In any PHP file:

```php
require_once($CFG->libdir . '/react_helper.php');

render_react_component('YourComponent', 'unique-element-id', [
    'userId' => $USER->id,
    'courseName' => $COURSE->fullname
]);
```

## Adding New Dependencies

### Installing NPM Packages

```bash
cd react-apps
npm install package-name

# For dev dependencies
npm install --save-dev package-name
```

**Important**: All dependencies are bundled into the IIFE, so size matters!

### Common Dependencies Examples

```bash
# State management
npm install zustand

# HTTP requests
npm install axios

# UI components
npm install @headlessui/react

# Date handling
npm install date-fns

# Forms
npm install react-hook-form
```

## Migrating AMD Modules to React

### Understanding AMD Module Structure

Moodle's AMD modules are located in various `/amd/src/` directories:
- `theme/boost/amd/src/`
- `lib/amd/src/`
- Plugin-specific AMD directories

### Migration Strategy

1. **Identify AMD Module**
   ```javascript
   // Example: lib/amd/src/modal.js
   define(['jquery', 'core/notification'], function($, Notification) {
     return {
       init: function() { /* ... */ }
     };
   });
   ```

2. **Create React Equivalent**
   ```jsx
   // react-apps/src/components/Modal.jsx
   import React, { useState } from 'react';
   
   const Modal = ({ isOpen, onClose, children }) => {
     // Modern React implementation
   };
   ```

3. **Handle Moodle Integration**
   ```javascript
   // In component, publish events for Moodle
   useEffect(() => {
     if (window.M && window.M.util && window.M.util.js_pending) {
       window.M.util.js_pending('moodle-react-modal');
       // Component initialization
       window.M.util.js_complete('moodle-react-modal');
     }
   }, []);
   ```

## Integrating with Moodle APIs

### Using Moodle's AJAX/Web Services

```javascript
// In React component
const fetchCourses = async () => {
  const response = await fetch('/lib/ajax/service.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      methodname: 'core_course_get_courses',
      args: {},
      sesskey: window.M.cfg.sesskey
    })
  });
  
  const data = await response.json();
  return data;
};
```

### Using Custom APIs

For the Course API example:
```javascript
// react-apps/src/utils/courseApi.js
export const CourseAPI = {
  async getCourses(token) {
    const response = await fetch('/local/courseapi/api/', {
      headers: {
        'Authorization': `Bearer ${token}`
      }
    });
    return response.json();
  }
};
```

## Component Patterns

### 1. Moodle-Aware Components

Components that integrate with Moodle's JavaScript APIs:

```jsx
const MoodleIntegratedComponent = () => {
  useEffect(() => {
    // Wait for Moodle's JS to be ready
    const checkMoodle = setInterval(() => {
      if (window.M && window.require) {
        clearInterval(checkMoodle);
        
        // Use Moodle's require for AMD modules if needed
        window.require(['core/str'], function(str) {
          str.get_string('loading', 'core').then(setLoadingText);
        });
      }
    }, 100);
  }, []);
};
```

### 2. Standalone Components

Pure React components with no Moodle dependencies:

```jsx
const PureComponent = ({ data }) => {
  // Standard React - easier to test and maintain
  return <div>{data}</div>;
};
```

### 3. Hybrid Components

Components that work both standalone and in Moodle:

```jsx
const HybridComponent = ({ moodleConfig }) => {
  const inMoodle = typeof window.M !== 'undefined';
  
  return (
    <div>
      {inMoodle ? (
        <MoodleSpecificFeature config={moodleConfig} />
      ) : (
        <StandaloneFeature />
      )}
    </div>
  );
};
```

## Best Practices

### 1. Component Organization

```
react-apps/src/
├── components/          # UI components
│   ├── common/         # Reusable components
│   ├── course/         # Course-related components
│   └── user/           # User-related components
├── hooks/              # Custom React hooks
├── utils/              # Helper functions
└── styles/             # Global styles
```

### 2. State Management

For simple state:
```javascript
// Use React's built-in hooks
const [state, setState] = useState();
```

For complex state:
```javascript
// Use Zustand for simplicity
import { create } from 'zustand';

const useStore = create((set) => ({
  courses: [],
  setCourses: (courses) => set({ courses }),
}));
```

### 3. Performance Optimization

```javascript
// Lazy load heavy components
const HeavyComponent = React.lazy(() => import('./HeavyComponent'));

// Use in component
<Suspense fallback={<div>Loading...</div>}>
  <HeavyComponent />
</Suspense>
```

### 4. Error Boundaries

```jsx
class ErrorBoundary extends React.Component {
  componentDidCatch(error, errorInfo) {
    // Log to Moodle's debugging if available
    if (window.M && window.M.cfg && window.M.cfg.developerdebug) {
      console.error('React Error:', error, errorInfo);
    }
  }
  
  render() {
    if (this.state.hasError) {
      return <div>Something went wrong. Please refresh the page.</div>;
    }
    return this.props.children;
  }
}
```

## Debugging

### Development Mode

1. Enable Moodle debugging in `config.php`:
   ```php
   $CFG->debug = 32767;
   $CFG->debugdisplay = 1;
   ```

2. React DevTools work normally in development mode

3. Check browser console for:
   - `window.MoodleReact` - Global API
   - `window.M` - Moodle's global object
   - Network requests to Vite dev server

### Production Debugging

1. Source maps are included in development builds
2. Use browser DevTools to inspect bundled code
3. Add console logs wrapped in development checks:
   ```javascript
   if (process.env.NODE_ENV === 'development') {
     console.log('Debug info:', data);
   }
   ```

## Common Issues and Solutions

### Issue: Component Not Rendering

1. Check element ID exists in DOM
2. Verify `window.MoodleReact` is loaded
3. Check browser console for errors
4. Ensure PHP helper is included

### Issue: Styles Not Applied

1. Verify CSS modules are imported
2. Check for CSS class name conflicts
3. Use more specific selectors if needed
4. Consider CSS-in-JS for complex styling

### Issue: Build Failures

```bash
# Clear caches and rebuild
rm -rf node_modules/.vite
npm run build

# If persist, reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

## Future Enhancements

### Planned Improvements

1. **TypeScript Support**
   ```bash
   npm install --save-dev typescript @types/react @types/react-dom
   ```

2. **Testing Framework**
   ```bash
   npm install --save-dev vitest @testing-library/react
   ```

3. **Storybook Integration**
   ```bash
   npx storybook@latest init
   ```

4. **Progressive Web App Features**
   - Service workers for offline support
   - Web push notifications
   - Background sync

### Migration Roadmap

1. **Phase 1**: Basic components (current)
2. **Phase 2**: Form components replacing YUI
3. **Phase 3**: Navigation and menus
4. **Phase 4**: Complex features (gradebook, quiz)
5. **Phase 5**: Full theme using React

## Maintenance

### Updating Dependencies

```bash
# Check outdated packages
npm outdated

# Update all dependencies
npm update

# Update specific package
npm install package-name@latest
```

### Security Considerations

1. Always escape user input
2. Use Moodle's sesskey for CSRF protection
3. Validate permissions server-side
4. Sanitize HTML content with DOMPurify if needed

## Resources

- [React Documentation](https://react.dev)
- [Vite Documentation](https://vitejs.dev)
- [Moodle Developer Documentation](https://moodledev.io)
- [CSS Modules](https://github.com/css-modules/css-modules)

## Contact

For questions about this architecture:
1. Check this documentation first
2. Review existing components for examples
3. Consult Moodle's developer forums
4. Consider the gradual migration approach

Remember: The goal is gradual modernization while maintaining stability!