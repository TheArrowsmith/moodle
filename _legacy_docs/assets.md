# Modern Asset Management & Frontend Architecture

## Overview

This document describes the modern frontend architecture that operates alongside Moodle's legacy AMD/RequireJS system. The new system provides a complete build pipeline for modern JavaScript, React components, CSS preprocessing, and asset optimization while maintaining full backward compatibility.

## Architecture Summary

The modern frontend system consists of several key components:

```
moodle-3/
├── react-apps/              # Modern development environment
│   ├── package.json         # Node.js dependencies & scripts
│   ├── vite.config.js       # Build configuration
│   ├── src/                 # Source code
│   │   ├── main.jsx         # React entry point
│   │   ├── components/      # React components
│   │   ├── utils/           # JavaScript utilities
│   │   └── styles/          # Global styles
│   └── index.html           # Development test page
├── react-dist/              # Production builds
│   ├── moodle-react.iife.js # Bundled React components
│   └── style.css            # Compiled styles
├── lib/
│   └── react_helper.php     # PHP integration helper
└── _docs/                   # Documentation
    ├── assets.md            # This document
    └── react-component-cookbook.md
```

## Key Design Principles

1. **Dual System**: Modern and legacy systems coexist without interference
2. **Progressive Enhancement**: New features use modern tools, existing code unchanged
3. **Zero Dependencies**: Modern system requires no changes to Moodle core
4. **Build-Time Optimization**: Assets are processed and optimized during build
5. **Development Experience**: Hot reload, modern debugging, and tooling

## Development Environment Setup

### Prerequisites

- Node.js 18+ (independent of Moodle's legacy Node 8.9)
- npm or yarn package manager
- Modern code editor with JavaScript/React support

### Initial Setup

```bash
# Navigate to the react-apps directory
cd /path/to/moodle/react-apps

# Install dependencies
npm install

# Start development server
npm run dev

# In another terminal, build for production
npm run build
```

### Available Scripts

```json
{
  "dev": "vite",                    // Start development server with HMR
  "build": "vite build",            // Build production bundles
  "preview": "vite preview",        // Preview production build locally
  "clean": "rm -rf ../react-dist/*" // Clean build directory
}
```

## Build Pipeline Architecture

### Development Mode

In development mode (`npm run dev`):

1. **Vite Dev Server** runs on `http://localhost:5173`
2. **Hot Module Replacement** provides instant updates
3. **ES Modules** are served directly to the browser
4. **Source Maps** enable easy debugging
5. **CSS Processing** happens in real-time

**Debug Mode Detection:**
```php
// In config.php
$CFG->debug = 32767; // Enables development mode
```

When debug mode is enabled, the PHP helper loads assets from the dev server:
```html
<script type="module" src="http://localhost:5173/@vite/client"></script>
<script type="module" src="http://localhost:5173/src/main.jsx"></script>
```

### Production Mode

In production mode (`npm run build`):

1. **Tree Shaking** removes unused code
2. **Code Splitting** optimizes loading
3. **Minification** reduces file sizes
4. **Asset Hashing** enables cache busting
5. **CSS Extraction** creates separate stylesheets

**Output Structure:**
```
react-dist/
├── moodle-react.iife.js    # Self-contained React bundle (~144KB gzipped)
├── style.css               # Extracted CSS (~1.5KB)
└── [hash].chunk.js         # Additional chunks (if code-split)
```

## Asset Types & Processing

### JavaScript/TypeScript

**Modern JavaScript Features:**
- ES2020+ syntax (async/await, optional chaining, etc.)
- ES Modules (import/export)
- Dynamic imports for code splitting
- Top-level await support

**Example:**
```javascript
// Modern JavaScript in src/utils/api.js
import { z } from 'zod';

export const fetchCourses = async (userId) => {
  const response = await fetch(`/api/courses?user=${userId}`);
  const data = await response.json();
  
  // Runtime validation with Zod
  const coursesSchema = z.array(z.object({
    id: z.number(),
    name: z.string(),
    progress: z.number().min(0).max(100)
  }));
  
  return coursesSchema.parse(data);
};
```

### CSS & Styling

**Supported Approaches:**

1. **CSS Modules** (Recommended for components)
```css
/* src/components/CourseCard.module.css */
.card {
  background: var(--moodle-primary-color, #0f6fc5);
  border-radius: 8px;
  padding: 1rem;
}

.card:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
```

2. **Global CSS**
```css
/* src/styles/global.css */
:root {
  --moodle-primary-color: #0f6fc5;
  --moodle-secondary-color: #6c757d;
}

.modern-button {
  /* Global utility class */
}
```

3. **CSS-in-JS** (for dynamic styling)
```javascript
// Using styled-components or emotion
const StyledButton = styled.button`
  background: ${props => props.theme.primary};
  color: white;
`;
```

### Asset Imports

**Images & Media:**
```javascript
// Vite handles asset imports automatically
import logoUrl from './assets/logo.png';
import iconSvg from './assets/icon.svg?inline'; // Inline SVG
import cssUrl from './styles/component.css?url'; // Get CSS URL
```

**JSON & Data:**
```javascript
import configData from './config.json';
import localeStrings from './locales/en.json';
```

## Integration Patterns

### 1. React Components

**Creating a New React Component:**

```jsx
// src/components/GradeDisplay.jsx
import React, { useState, useEffect } from 'react';
import styles from './GradeDisplay.module.css';

const GradeDisplay = ({ courseId, userId, format = 'percentage' }) => {
  const [grades, setGrades] = useState([]);
  const [loading, setLoading] = useState(true);
  
  useEffect(() => {
    fetchGrades(courseId, userId).then(setGrades);
  }, [courseId, userId]);
  
  return (
    <div className={styles.container}>
      {grades.map(grade => (
        <div key={grade.id} className={styles.gradeItem}>
          <span className={styles.activity}>{grade.activity}</span>
          <span className={styles.score}>
            {formatGrade(grade.score, format)}
          </span>
        </div>
      ))}
    </div>
  );
};

export default GradeDisplay;
```

**Registration:**
```javascript
// Add to src/main.jsx
import GradeDisplay from './components/GradeDisplay';

window.MoodleReact = {
  components: {
    HelloMoodle,
    GradeDisplay, // Register new component
  },
  // ... rest of API
};
```

**Usage in Moodle:**
```php
// In any PHP file
require_once($CFG->libdir . '/react_helper.php');

render_react_component('GradeDisplay', 'grade-display-' . $course->id, [
    'courseId' => $course->id,
    'userId' => $USER->id,
    'format' => 'letter'
]);
```

### 2. Vanilla JavaScript Modules

For non-React functionality:

```javascript
// src/utils/notifications.js
export class NotificationManager {
  constructor(containerId) {
    this.container = document.getElementById(containerId);
    this.notifications = new Map();
  }
  
  show(message, type = 'info', duration = 5000) {
    const notification = this.create(message, type);
    this.container.appendChild(notification);
    
    if (duration > 0) {
      setTimeout(() => this.hide(notification), duration);
    }
    
    return notification;
  }
  
  create(message, type) {
    const div = document.createElement('div');
    div.className = `notification notification--${type}`;
    div.innerHTML = `
      <div class="notification__content">${message}</div>
      <button class="notification__close" onclick="this.parentElement.remove()">×</button>
    `;
    return div;
  }
}

// Auto-initialize if container exists
document.addEventListener('DOMContentLoaded', () => {
  const container = document.getElementById('moodle-notifications');
  if (container) {
    window.MoodleNotifications = new NotificationManager('moodle-notifications');
  }
});
```

**Loading in Moodle:**
```php
// Method 1: Include in main build
// Add import to src/main.jsx:
import './utils/notifications.js';

// Method 2: Dynamic loading
echo '<script type="module">
  import("/react-dist/notifications.js").then(module => {
    window.notifications = new module.NotificationManager("notifications");
  });
</script>';
```

### 3. CSS-Only Enhancements

For pure CSS improvements:

```css
/* src/styles/form-enhancements.css */
.mform .form-group {
  position: relative;
  margin-bottom: 1.5rem;
}

.mform .form-control:focus {
  border-color: var(--moodle-primary-color);
  box-shadow: 0 0 0 0.125rem rgba(15, 111, 197, 0.25);
}

/* Modern file upload styling */
.mform input[type="file"] {
  display: none;
}

.mform .file-upload-zone {
  border: 2px dashed #ccc;
  padding: 2rem;
  text-align: center;
  cursor: pointer;
  transition: all 0.3s ease;
}

.mform .file-upload-zone:hover {
  border-color: var(--moodle-primary-color);
  background-color: rgba(15, 111, 197, 0.05);
}
```

**Loading CSS:**
```php
// Method 1: Include in build
// Import in src/main.jsx or component

// Method 2: Direct inclusion
$PAGE->requires->css('/react-dist/form-enhancements.css');
```

## Migration Patterns

### From AMD to Modern ES Modules

**Before (AMD):**
```javascript
// lib/amd/src/modal.js
define(['jquery', 'core/notification'], function($, Notification) {
  return {
    init: function(config) {
      $('.modal-trigger').on('click', function() {
        // Modal logic
      });
    }
  };
});
```

**After (Modern):**
```javascript
// src/utils/modal.js
export class Modal {
  constructor(selector, options = {}) {
    this.elements = document.querySelectorAll(selector);
    this.options = { backdrop: true, keyboard: true, ...options };
    this.init();
  }
  
  init() {
    this.elements.forEach(element => {
      element.addEventListener('click', this.show.bind(this));
    });
  }
  
  show(event) {
    const modal = this.create(event.target.dataset);
    document.body.appendChild(modal);
    modal.classList.add('show');
  }
  
  // Modern methods using native DOM APIs
}

// Auto-initialize
document.addEventListener('DOMContentLoaded', () => {
  new Modal('.modal-trigger');
});
```

### From YUI to Modern JavaScript

**Before (YUI):**
```javascript
// Legacy YUI module
YUI().use('node', 'event', function(Y) {
  Y.on('click', function(e) {
    e.preventDefault();
    // YUI-specific code
  }, '.button');
});
```

**After (Modern):**
```javascript
// Modern event handling
document.addEventListener('click', (event) => {
  if (event.target.matches('.button')) {
    event.preventDefault();
    // Modern vanilla JS or using modern libraries
  }
});

// Or with modern libraries
import { delegate } from 'delegated-events';

delegate('.button', 'click', (event) => {
  event.preventDefault();
  // Delegated event handling
});
```

### From SCSS to Modern CSS

**Enhanced SCSS Processing:**
```scss
// src/styles/theme.scss
@use 'sass:math';

:root {
  --moodle-spacing-unit: #{math.div(1rem, 4)};
  --moodle-border-radius: calc(var(--moodle-spacing-unit) * 2);
}

.course-card {
  // Modern CSS features
  container-type: inline-size;
  
  @container (min-width: 300px) {
    .card-title {
      font-size: 1.25rem;
    }
  }
  
  // CSS Grid with fallback
  display: grid;
  grid-template-areas: 
    "image title"
    "image meta"
    "actions actions";
  gap: var(--moodle-spacing-unit);
  
  @supports not (display: grid) {
    display: flex;
    flex-wrap: wrap;
  }
}
```

## Advanced Patterns

### 1. Code Splitting & Lazy Loading

**Route-based Splitting:**
```javascript
// src/main.jsx
import { lazy, Suspense } from 'react';

const GradebookApp = lazy(() => import('./apps/GradebookApp'));
const QuizBuilder = lazy(() => import('./apps/QuizBuilder'));

const apps = {
  GradebookApp: (props) => (
    <Suspense fallback={<div>Loading gradebook...</div>}>
      <GradebookApp {...props} />
    </Suspense>
  ),
  QuizBuilder: (props) => (
    <Suspense fallback={<div>Loading quiz builder...</div>}>
      <QuizBuilder {...props} />
    </Suspense>
  )
};

window.MoodleReact = {
  components: { ...components, ...apps },
  // ... rest of API
};
```

**Dynamic Imports:**
```javascript
// Load heavy features on demand
const loadAdvancedEditor = async () => {
  const { AdvancedEditor } = await import('./components/AdvancedEditor');
  return AdvancedEditor;
};

// Usage
document.getElementById('enable-advanced').addEventListener('click', async () => {
  const EditorComponent = await loadAdvancedEditor();
  // Initialize editor
});
```

### 2. State Management

**For Simple State:**
```javascript
// src/hooks/useLocalStorage.js
import { useState, useEffect } from 'react';

export const useLocalStorage = (key, defaultValue) => {
  const [value, setValue] = useState(() => {
    try {
      const item = window.localStorage.getItem(key);
      return item ? JSON.parse(item) : defaultValue;
    } catch (error) {
      return defaultValue;
    }
  });
  
  useEffect(() => {
    try {
      window.localStorage.setItem(key, JSON.stringify(value));
    } catch (error) {
      console.error('Failed to save to localStorage:', error);
    }
  }, [key, value]);
  
  return [value, setValue];
};
```

**For Complex State (Zustand):**
```javascript
// src/stores/courseStore.js
import { create } from 'zustand';
import { devtools } from 'zustand/middleware';

export const useCourseStore = create(devtools((set, get) => ({
  courses: [],
  selectedCourse: null,
  loading: false,
  
  fetchCourses: async (userId) => {
    set({ loading: true });
    try {
      const courses = await fetchCoursesAPI(userId);
      set({ courses, loading: false });
    } catch (error) {
      set({ loading: false });
      throw error;
    }
  },
  
  selectCourse: (courseId) => {
    const course = get().courses.find(c => c.id === courseId);
    set({ selectedCourse: course });
  }
})));
```

### 3. API Integration

**Moodle Web Services:**
```javascript
// src/utils/moodleApi.js
class MoodleAPI {
  constructor(baseUrl = '', sesskey = window.M?.cfg?.sesskey) {
    this.baseUrl = baseUrl;
    this.sesskey = sesskey;
  }
  
  async call(methodname, args = {}) {
    const response = await fetch(`${this.baseUrl}/lib/ajax/service.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        methodname,
        args,
        sesskey: this.sesskey
      })
    });
    
    if (!response.ok) {
      throw new Error(`API call failed: ${response.statusText}`);
    }
    
    const data = await response.json();
    
    if (data.error) {
      throw new Error(data.error);
    }
    
    return data;
  }
  
  // Specific methods
  async getCourses(userid) {
    return this.call('core_course_get_enrolled_courses_by_timeline_classification', {
      userid,
      classification: 'all'
    });
  }
  
  async getGrades(courseid, userid) {
    return this.call('gradereport_user_get_grade_items', {
      courseid,
      userid
    });
  }
}

export const moodleAPI = new MoodleAPI();
```

**Custom API Integration:**
```javascript
// src/utils/customApi.js
import { z } from 'zod';

const apiResponseSchema = z.object({
  success: z.boolean(),
  data: z.any(),
  error: z.string().optional()
});

export class CustomAPI {
  constructor(baseUrl, token) {
    this.baseUrl = baseUrl;
    this.token = token;
  }
  
  async request(endpoint, options = {}) {
    const url = `${this.baseUrl}/${endpoint}`;
    const config = {
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${this.token}`,
        ...options.headers
      },
      ...options
    };
    
    const response = await fetch(url, config);
    const rawData = await response.json();
    
    // Validate response structure
    const validatedData = apiResponseSchema.parse(rawData);
    
    if (!validatedData.success) {
      throw new Error(validatedData.error || 'API request failed');
    }
    
    return validatedData.data;
  }
}
```

## Performance Optimization

### Bundle Analysis

```bash
# Analyze bundle size
npx vite-bundle-analyzer

# Check for duplicate dependencies
npx duplicate-package-checker-webpack-plugin
```

### Optimization Strategies

1. **Tree Shaking:**
```javascript
// Import only what you need
import { debounce } from 'lodash-es'; // ✅ Tree-shakeable
import * as _ from 'lodash'; // ❌ Imports entire library
```

2. **Code Splitting:**
```javascript
// Split vendor libraries
export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        manualChunks: {
          vendor: ['react', 'react-dom'],
          utils: ['lodash-es', 'date-fns']
        }
      }
    }
  }
});
```

3. **Asset Optimization:**
```javascript
// vite.config.js
export default defineConfig({
  build: {
    rollupOptions: {
      output: {
        assetFileNames: (assetInfo) => {
          const info = assetInfo.name.split('.');
          const extType = info[info.length - 1];
          if (/png|jpe?g|svg|gif|tiff|bmp|ico/i.test(extType)) {
            return `assets/images/[name]-[hash][extname]`;
          }
          if (/css/i.test(extType)) {
            return `assets/css/[name]-[hash][extname]`;
          }
          return `assets/[name]-[hash][extname]`;
        }
      }
    }
  }
});
```

## Testing Strategy

### Unit Testing

```javascript
// src/components/__tests__/GradeDisplay.test.jsx
import { render, screen, waitFor } from '@testing-library/react';
import { vi } from 'vitest';
import GradeDisplay from '../GradeDisplay';

vi.mock('../utils/api', () => ({
  fetchGrades: vi.fn(() => Promise.resolve([
    { id: 1, activity: 'Quiz 1', score: 85 },
    { id: 2, activity: 'Assignment 1', score: 92 }
  ]))
}));

describe('GradeDisplay', () => {
  it('renders grades correctly', async () => {
    render(<GradeDisplay courseId={1} userId={123} />);
    
    await waitFor(() => {
      expect(screen.getByText('Quiz 1')).toBeInTheDocument();
      expect(screen.getByText('85%')).toBeInTheDocument();
    });
  });
});
```

### Integration Testing

```javascript
// src/__tests__/integration.test.js
import { vi } from 'vitest';

describe('Moodle Integration', () => {
  beforeEach(() => {
    // Mock Moodle globals
    window.M = {
      cfg: { sesskey: 'test-session-key' }
    };
  });
  
  it('should mount component to DOM element', () => {
    document.body.innerHTML = '<div id="test-mount"></div>';
    
    window.MoodleReact.mount('HelloMoodle', '#test-mount', {
      userName: 'Test User'
    });
    
    expect(document.querySelector('#test-mount')).not.toBeEmpty();
  });
});
```

## Deployment

### Production Build Process

```bash
# 1. Clean previous builds
npm run clean

# 2. Run tests
npm test

# 3. Build production assets
npm run build

# 4. Verify build output
ls -la ../react-dist/

# 5. Test production build
npm run preview
```

### Build Configuration

```javascript
// vite.config.js - Production optimizations
export default defineConfig({
  define: {
    'process.env.NODE_ENV': '"production"'
  },
  build: {
    minify: 'terser',
    terserOptions: {
      compress: {
        drop_console: true, // Remove console.log in production
        drop_debugger: true
      }
    },
    rollupOptions: {
      output: {
        // Optimize chunk sizes
        manualChunks(id) {
          if (id.includes('node_modules')) {
            return 'vendor';
          }
          if (id.includes('src/components')) {
            return 'components';
          }
        }
      }
    }
  }
});
```

## Debugging

### Development Tools

1. **React DevTools**: Available in development mode
2. **Vite DevTools**: Built-in HMR and error overlay
3. **Source Maps**: Full source mapping in development

### Common Issues & Solutions

**1. Component Not Mounting:**
```javascript
// Add debugging to PHP helper
console.log('Looking for element:', elementId);
console.log('Element found:', document.getElementById(elementId));
console.log('MoodleReact available:', !!window.MoodleReact);
```

**2. Module Loading Issues:**
```javascript
// Check Vite dev server accessibility
fetch('http://localhost:5173/@vite/client')
  .then(r => console.log('Dev server accessible'))
  .catch(e => console.error('Dev server not accessible:', e));
```

**3. Build Issues:**
```bash
# Clear all caches
rm -rf node_modules/.vite
rm -rf node_modules
npm install
npm run build
```

## Security Considerations

### Content Security Policy

Update Moodle's CSP to allow Vite dev server:

```php
// In config.php or theme
$CFG->forced_plugin_settings['tool_policy']['csp'] = 
  "default-src 'self'; " .
  "script-src 'self' 'unsafe-inline' http://localhost:5173; " .
  "connect-src 'self' ws://localhost:5173 http://localhost:5173;";
```

### Input Sanitization

Always sanitize props passed to React components:

```php
// In PHP helper
$props = [
    'userName' => s(fullname($USER)), // Sanitize output
    'courseId' => (int)$COURSE->id,   // Type casting
    'message' => format_text($message, FORMAT_HTML) // Proper formatting
];
```

### XSS Prevention

```javascript
// In React components, avoid dangerouslySetInnerHTML
// Use proper escaping for user content
const SafeContent = ({ userContent }) => {
  return <div>{userContent}</div>; // React auto-escapes
};

// If HTML is needed, sanitize first
import DOMPurify from 'dompurify';

const SafeHTML = ({ htmlContent }) => {
  const sanitized = DOMPurify.sanitize(htmlContent);
  return <div dangerouslySetInnerHTML={{ __html: sanitized }} />;
};
```

## Future Enhancements

### Planned Features

1. **TypeScript Support**: Gradual migration to TypeScript
2. **PWA Features**: Service workers, offline support
3. **Micro-frontends**: Separate apps for different Moodle areas
4. **Theme Integration**: Better integration with Moodle themes
5. **Performance Monitoring**: Real-time performance tracking

### Migration Roadmap

**Phase 1: Foundation** (Complete)
- Basic React integration
- Build pipeline
- Development workflow

**Phase 2: Core Components**
- Form components
- Navigation elements
- Basic interactions

**Phase 3: Advanced Features**
- Complex widgets (gradebook, quiz builder)
- Real-time features
- Advanced state management

**Phase 4: Full Integration**
- Theme-wide modernization
- Legacy system deprecation
- Performance optimization

## Best Practices Summary

### Do's ✅

- Use CSS Modules for component styling
- Follow React hooks patterns
- Implement proper error boundaries
- Write tests for critical components
- Use TypeScript for better maintainability
- Optimize bundle sizes
- Follow accessibility guidelines
- Sanitize all user inputs

### Don'ts ❌

- Don't modify Moodle core files
- Don't bypass Moodle's permission system
- Don't ignore browser compatibility
- Don't forget to test in production mode
- Don't mix AMD and ES module patterns
- Don't expose sensitive data to client-side
- Don't skip performance testing

## Support & Resources

- **Vite Documentation**: https://vitejs.dev/
- **React Documentation**: https://react.dev/
- **Moodle Developer Docs**: https://moodledev.io/
- **Project Documentation**: `/moodle/_docs/`

For questions or issues with this architecture, check existing documentation first, then consult the broader development community.

---

**Remember**: This architecture is designed for gradual adoption. Start small, test thoroughly, and expand incrementally!