# React Integration Guide for Moodle 3

## Overview

This guide documents the integration approach for adding React components to Moodle 3, including the PHP helper function, error handling, and best practices.

## Integration Architecture

### 1. PHP Helper Function (`lib/react_helper.php`)

The `render_react_component()` function provides a clean PHP API for rendering React components in Moodle pages.

```php
render_react_component($component, $elementid, $props = [], $options = []);
```

**Parameters:**
- `$component` (string): Component name as registered in `window.MoodleReact.components`
- `$elementid` (string): HTML element ID for mounting (must be unique on page)
- `$props` (array): Props to pass to the component
- `$options` (array): Additional options
  - `wrapdiv` (bool): Whether to create wrapper div (default: true)
  - `class` (string): CSS class for wrapper div
  - `return` (bool): Return HTML instead of echoing (default: false)

### 2. Script Loading Strategy

The helper automatically handles development vs production modes:

- **Development Mode** (`$CFG->debug >= DEBUG_DEVELOPER`):
  - Loads from Vite dev server at `http://localhost:5173`
  - Enables Hot Module Replacement (HMR)
  - Shows detailed error messages

- **Production Mode**:
  - Loads minified bundle from `/react-dist/moodle-react.iife.js`
  - Optimized for performance
  - Error boundaries prevent component crashes from affecting Moodle

### 3. Component Mounting Process

1. React bundle loads and creates `window.MoodleReact` global
2. PHP outputs mounting div with data attributes
3. JavaScript waits for React to be available
4. Component mounts with provided props
5. Error boundaries catch any component errors

## Integration Patterns

### Pattern 1: In Page Content

```php
// In any Moodle page (e.g., course/view.php)
require_once($CFG->libdir . '/react_helper.php');

$props = [
    'userName' => fullname($USER),
    'courseId' => $course->id,
    'courseName' => format_string($course->fullname)
];

render_react_component('CourseHeader', 'course-header-react', $props);
```

### Pattern 2: In Renderer Output

```php
// In a renderer class
class mod_mymodule_renderer extends plugin_renderer_base {
    public function render_activity_header($activity) {
        global $CFG;
        require_once($CFG->libdir . '/react_helper.php');
        
        $props = [
            'activityName' => $activity->name,
            'activityType' => $activity->modname
        ];
        
        return render_react_component('ActivityHeader', 'activity-' . $activity->id, $props, [
            'return' => true,
            'class' => 'activity-header-react'
        ]);
    }
}
```

### Pattern 3: In Blocks

```php
// In a block's get_content() method
public function get_content() {
    global $CFG;
    
    if ($this->content !== null) {
        return $this->content;
    }
    
    require_once($CFG->libdir . '/react_helper.php');
    
    $this->content = new stdClass;
    $this->content->text = render_react_component('DashboardWidget', 'block-dashboard-' . $this->instance->id, [
        'blockId' => $this->instance->id,
        'userId' => $USER->id
    ], ['return' => true]);
    
    return $this->content;
}
```

### Pattern 4: AJAX Loaded Content

```php
// In AJAX script
require_once($CFG->libdir . '/react_helper.php');

$html = render_react_component('AjaxComponent', 'ajax-component-' . uniqid(), $props, [
    'return' => true
]);

echo json_encode(['html' => $html, 'success' => true]);
```

## Error Handling

### 1. Component Not Found

The helper includes error handling for missing components:

```javascript
if (!Component) {
    console.error(`Component ${componentName} not found`);
    return;
}
```

### 2. Mount Point Not Found

Handles missing DOM elements gracefully:

```javascript
if (!container) {
    console.error(`Element ${elementId} not found`);
    return;
}
```

### 3. React Loading Timeout

The mounting script includes a polling mechanism with implicit timeout:

```javascript
var checkReact = setInterval(function() {
    if (window.MoodleReact && window.MoodleReact.mount) {
        clearInterval(checkReact);
        // Mount component
    }
}, 100);
```

### 4. Component Errors

Implement error boundaries in your React components:

```jsx
class ErrorBoundary extends React.Component {
    state = { hasError: false };
    
    static getDerivedStateFromError(error) {
        return { hasError: true };
    }
    
    componentDidCatch(error, errorInfo) {
        console.error('React component error:', error, errorInfo);
    }
    
    render() {
        if (this.state.hasError) {
            return <div className="alert alert-danger">Component failed to load</div>;
        }
        return this.props.children;
    }
}
```

## Compatibility Considerations

### 1. Theme Compatibility

React components should respect Moodle themes:

```jsx
// Use Moodle's Bootstrap classes
<div className="card">
    <div className="card-header">
        <h3 className="card-title">{title}</h3>
    </div>
    <div className="card-body">
        {content}
    </div>
</div>
```

### 2. RTL Support

Handle right-to-left languages:

```jsx
const isRTL = document.documentElement.dir === 'rtl';

<div style={{ textAlign: isRTL ? 'right' : 'left' }}>
    {content}
</div>
```

### 3. Accessibility

Ensure WCAG 2.1 AA compliance:

```jsx
<button 
    onClick={handleClick}
    aria-label={ariaLabel}
    aria-pressed={isPressed}
>
    {buttonText}
</button>
```

### 4. Mobile Responsiveness

Use responsive design patterns:

```jsx
<div className="container-fluid">
    <div className="row">
        <div className="col-12 col-md-6 col-lg-4">
            {content}
        </div>
    </div>
</div>
```

## Performance Optimization

### 1. Lazy Loading

For large components, implement lazy loading:

```jsx
const HeavyComponent = React.lazy(() => import('./HeavyComponent'));

function MyComponent() {
    return (
        <React.Suspense fallback={<div>Loading...</div>}>
            <HeavyComponent />
        </React.Suspense>
    );
}
```

### 2. Code Splitting

Configure Vite for automatic code splitting:

```javascript
// vite.config.js
build: {
    rollupOptions: {
        output: {
            manualChunks: {
                'vendor': ['react', 'react-dom'],
                'utils': ['./src/utils/index.js']
            }
        }
    }
}
```

### 3. Caching

Implement proper cache headers:

```php
// In production mode
$PAGE->requires->js(new moodle_url('/react-dist/moodle-react.iife.js'), true);
// Moodle handles cache headers automatically
```

## Security Considerations

### 1. XSS Protection

Props are automatically escaped by the helper:

```php
$propsjson = json_encode($props, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
```

### 2. CSRF Protection

For form submissions, include sesskey:

```jsx
function submitForm(data) {
    const formData = new FormData();
    formData.append('sesskey', M.cfg.sesskey);
    formData.append('data', JSON.stringify(data));
    
    fetch('process.php', {
        method: 'POST',
        body: formData
    });
}
```

### 3. Capability Checks

Always verify permissions in PHP:

```php
require_capability('mod/mymodule:view', $context);

$props = [
    'canEdit' => has_capability('mod/mymodule:edit', $context),
    'canDelete' => has_capability('mod/mymodule:delete', $context)
];
```

## Debugging Tips

### 1. Enable React DevTools

In development mode, React DevTools work automatically.

### 2. Check Console

Common issues to look for:
- "Component X not found" - Component not exported in main.jsx
- "Element X not found" - ID mismatch or timing issue
- Network errors - Vite server not running or wrong port

### 3. Verify Props

Add debugging to components:

```jsx
useEffect(() => {
    console.log('Component mounted with props:', props);
}, []);
```

### 4. Check Script Loading

In browser DevTools Network tab:
- Development: Should load from localhost:5173
- Production: Should load from /react-dist/

## Migration Strategy

### Phase 1: Pilot Components
1. Start with non-critical UI elements
2. Gather performance metrics
3. Collect user feedback

### Phase 2: Gradual Expansion
1. Convert high-impact interfaces
2. Maintain parallel implementations
3. A/B test where possible

### Phase 3: Full Adoption
1. Deprecate legacy implementations
2. Provide migration tools
3. Update documentation

## Best Practices

1. **Keep Components Pure**: Avoid direct DOM manipulation
2. **Use Moodle APIs**: Leverage existing Moodle functions via AJAX
3. **Follow Moodle Coding Standards**: Maintain consistency
4. **Test Across Themes**: Ensure compatibility with popular themes
5. **Document Props**: Use PropTypes or TypeScript
6. **Handle Loading States**: Show appropriate feedback
7. **Implement Error Boundaries**: Prevent cascade failures
8. **Use Semantic HTML**: Maintain accessibility
9. **Optimize Bundle Size**: Monitor and minimize
10. **Version Control**: Track React app separately if needed

## Troubleshooting Checklist

- [ ] React dev server running (port 5173)?
- [ ] Correct component name in main.jsx exports?
- [ ] Unique element ID on page?
- [ ] Props properly formatted (no circular references)?
- [ ] Debug mode enabled in config.php?
- [ ] No JavaScript errors in console?
- [ ] Network requests successful?
- [ ] Component visible in React DevTools?
- [ ] Styles loading correctly?
- [ ] Mobile responsive working?

## Future Enhancements

1. **TypeScript Support**: Add type safety
2. **Storybook Integration**: Component documentation
3. **Testing Framework**: Jest + React Testing Library
4. **CI/CD Pipeline**: Automated builds
5. **Component Library**: Shared UI components
6. **State Management**: Context API or Zustand
7. **Internationalization**: React-intl integration
8. **Performance Monitoring**: React Profiler API
9. **SSR Consideration**: Next.js for specific pages
10. **WebSocket Support**: Real-time features