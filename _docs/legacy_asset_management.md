# Moodle Frontend Asset Management

This document provides a comprehensive guide to how Moodle manages, builds, and serves frontend assets including JavaScript, CSS, fonts, and images.

## Table of Contents

1. [Overview](#overview)
2. [Asset Organization](#asset-organization)
3. [Build System](#build-system)
4. [Asset Serving Mechanism](#asset-serving-mechanism)
5. [JavaScript Loading Flow](#javascript-loading-flow)
6. [Development Workflow](#development-workflow)
7. [Performance Optimizations](#performance-optimizations)
8. [Configuration Options](#configuration-options)

## Overview

Moodle uses a combination of AMD (Asynchronous Module Definition) modules with RequireJS, legacy YUI modules, and a Grunt-based build system to manage frontend assets. The system is designed for performance with extensive caching while maintaining backward compatibility.

### Key Components

- **Build Tool**: Grunt for asset compilation and minification
- **Module Systems**: AMD (modern) and YUI (legacy)
- **CSS Preprocessors**: SCSS (Boost theme) and LESS (Bootstrap Base theme)
- **Asset Serving**: PHP scripts that handle caching, versioning, and delivery
- **Caching**: Aggressive file and browser caching with revision-based URLs

## Asset Organization

### JavaScript Structure

#### AMD Modules (Recommended)
- **Source Location**: `*/amd/src/*.js`
- **Built Location**: `*/amd/build/*.min.js`
- **Core Modules**: `/lib/amd/src/` contains essential modules like:
  - `ajax.js` - AJAX handling
  - `modal.js` - Modal dialogs
  - `templates.js` - Template rendering
  - `notification.js` - User notifications

Example AMD module structure:
```javascript
// In /mod/forum/amd/src/discussion.js
define(['jquery', 'core/ajax'], function($, Ajax) {
    return {
        init: function(params) {
            // Module initialization
        }
    };
});
```

#### YUI Modules (Legacy)
- **Source Location**: `*/yui/src/*/js/*.js`
- **Built Location**: `*/yui/build/`
- Still used in some legacy components

#### External Libraries
- **jQuery**: `/lib/jquery/`
- **RequireJS**: `/lib/requirejs/`
- **Other vendors**: Various subdirectories in `/lib/`

### CSS/Styling Structure

#### Boost Theme (Modern - Bootstrap 4)
- **Location**: `/theme/boost/scss/`
- **Main Files**:
  - `preset/default.scss` - Default theme preset
  - `moodle.scss` - Core Moodle styles
- **Framework**: Bootstrap 4 with custom Moodle extensions

#### Bootstrap Base Theme (Legacy - Bootstrap 2.3)
- **Location**: `/theme/bootstrapbase/less/`
- **Framework**: Bootstrap 2.3
- Being phased out in favor of Boost

#### Component Styles
- Individual CSS files in module directories
- Example: `/blocks/*/styles.css`

### Font and Icon Assets

#### Font Awesome 4.7.0
- **Location**: `/lib/fonts/font-awesome-4.7.0/`
- **Formats**: EOT, WOFF, WOFF2, TTF, SVG
- **Integration**: Through `icon_system_fontawesome` class

#### Image Organization
```
/pix/
├── a/      # Action icons
├── e/      # Editor icons
├── f/      # File type icons (24, 32, 48, 64, 72, 80, 96, 128, 256px)
├── i/      # Interface icons
├── s/      # Smiley emoticons
└── t/      # Tiny icons
```

Theme-specific images: `/theme/<themename>/pix/`
Plugin images: `/<plugintype>/<pluginname>/pix/`

## Build System

### Grunt Configuration

The build system is configured in `/Gruntfile.js` and requires:
- Node.js (version >=8.9 <9)
- npm for package management

### Build Tasks

```bash
# Build AMD modules (lint + minify)
grunt amd

# Build YUI modules
grunt yui

# Compile CSS (SCSS and LESS)
grunt css

# Build all JavaScript
grunt js

# Run default task (context-aware)
grunt

# Watch for changes
grunt watch
```

### Build Process Details

#### AMD Module Building
1. **Linting**: ESLint checks code quality
2. **Minification**: UglifyJS creates minified versions
3. **Output**: Places `.min.js` files in `amd/build/`

#### CSS Compilation
1. **SCSS**: Compiled using grunt-sass (Boost theme)
2. **LESS**: Compiled using grunt-contrib-less (Bootstrap Base)
3. **Linting**: Stylelint for code quality
4. **Output**: Compiled CSS in theme directories

### Important Note on Building

**Moodle does NOT build assets on-the-fly**. You must:
1. Make changes to source files (`amd/src/`, `scss/`)
2. Run appropriate Grunt tasks
3. Built files are committed to version control
4. Production uses pre-built files only

## Asset Serving Mechanism

### JavaScript Serving

#### AMD Modules via `/lib/requirejs.php`
- **URL Pattern**: `/lib/requirejs.php/{revision}/{component}/{module}.js`
- **Features**:
  - Module concatenation for performance
  - Module name injection into `define()` calls
  - Caching in `$CFG->localcachedir/requirejs/`
  - Lazy loading support with `-lazy.js` suffix

#### Standard JavaScript via `/lib/javascript.php`
- **URL Pattern**: `/lib/javascript.php/{revision}/path/to/file.js`
- **Features**:
  - On-the-fly minification
  - Caching in `$CFG->localcachedir/js/`
  - ETag support

### CSS Serving via `/theme/styles.php`
- **URL Pattern**: `/theme/styles.php/{themename}/{revision}/all`
- **Features**:
  - SCSS compilation on first request
  - Caching in `$CFG->localcachedir/theme/{revision}/{themename}/css/`
  - RTL support (`all-rtl` type)
  - Chunking for legacy IE browsers
  - Lock mechanism prevents duplicate generation

### Image Serving via `/theme/image.php`
- **URL Pattern**: `/theme/image.php/{theme}/{component}/{revision}/{image}`
- **Features**:
  - SVG preference with PNG/GIF/JPG fallback
  - Caching in `$CFG->localcachedir/theme/{revision}/{themename}/pix/`
  - 404 caching to prevent repeated lookups

### Font Serving via `/theme/font.php`
- **URL Pattern**: `/theme/font.php/{theme}/{component}/{revision}/{font}`
- **Supports**: WOFF2, WOFF, TTF, OTF, EOT, SVG fonts
- **Caching**: Similar to image caching strategy

## JavaScript Loading Flow

### Step-by-Step Process

1. **PHP Registration**
   ```php
   // In PHP code
   $PAGE->requires->js_call_amd('mod_forum/discussion', 'init', [$params]);
   ```

2. **Page Rendering**
   - Header: Basic HTML setup, no JavaScript yet
   - Content: Page-specific content generation
   - Footer: JavaScript assembly and output

3. **JavaScript Assembly** (in footer)
   ```html
   <!-- 1. Load RequireJS -->
   <script src="/lib/javascript.php/1/lib/requirejs/require.min.js"></script>

   <!-- 2. Configure RequireJS -->
   <script>
   M.cfg = {"wwwroot":"http://localhost","sesskey":"..."};
   require.config({
       baseUrl: '/lib/requirejs.php/1',
       paths: {
           'jquery': 'https://code.jquery.com/jquery-3.5.1.min'
       }
   });

   <!-- 3. Load AMD modules -->
   require(['core/first'], function() {
       require(['jquery'], function($) {
           // AMD calls executed here
           M.util.js_pending('mod_forum/discussion');
           require(['mod_forum/discussion'], function(amd) {
               amd.init(params);
               M.util.js_complete('mod_forum/discussion');
           });
       });
   });
   </script>
   ```

4. **Module Loading**
   - RequireJS requests: `/lib/requirejs.php/1/mod_forum/discussion.js`
   - Server locates file:
     - Production: `/mod/forum/amd/build/discussion.min.js`
     - Debug: `/mod/forum/amd/src/discussion.js`
   - Module content returned with proper headers

5. **Execution**
   - Module's `init()` function called with parameters
   - Dependencies resolved automatically by RequireJS

### Key Classes

- **`page_requirements_manager`** (`/lib/outputrequirementslib.php`)
  - Manages all page asset requirements
  - Methods: `js_call_amd()`, `css()`, `js_init_code()`
  
- **`core_requirejs`** (`/lib/classes/requirejs.php`)
  - Handles AMD module resolution and serving

## Development Workflow

### Development Setup

1. **Enable Development Mode**
   ```php
   // In config.php
   $CFG->cachejs = false;  // Disable JS caching
   $CFG->debug = 32767;    // Maximum debug level
   $CFG->debugdisplay = 1; // Display debug messages
   ```

2. **Development Workflow**
   - Edit source files in `amd/src/`
   - With `cachejs = false`, changes are immediate
   - No need to run Grunt during development
   - Browser refresh shows latest code

3. **Production Preparation**
   ```bash
   # Before committing or deploying
   grunt amd        # Build AMD modules
   grunt css        # Compile CSS
   grunt           # Or run all tasks
   ```

### Best Practices

1. **Always use AMD modules** for new JavaScript
2. **Namespace properly**: `componentname/modulename`
3. **Declare dependencies** explicitly in `define()`
4. **Test in production mode** before deploying
5. **Run Grunt** before committing changes

## Performance Optimizations

### Caching Strategy

1. **Revision Numbers**
   - `$CFG->jsrev` - JavaScript revision
   - `$CFG->themerev` - Theme/CSS revision
   - URLs include revision for cache busting

2. **Cache Locations**
   ```
   $CFG->localcachedir/
   ├── js/           # Minified JavaScript
   ├── requirejs/    # Combined AMD modules
   └── theme/        # Compiled CSS and assets
       └── {rev}/
           └── {themename}/
               ├── css/
               ├── pix/
               └── fonts/
   ```

3. **Browser Caching**
   - Long cache headers (90 days)
   - ETags for validation
   - Revision URLs force refresh

### Optimization Features

- **Minification**: JavaScript automatically minified
- **Combination**: Multiple AMD modules in single request
- **Compression**: Gzip via web server
- **SVG Preference**: Vector images when available
- **X-Sendfile Support**: Efficient file serving
- **Error Caching**: Prevents repeated 404 lookups

## Configuration Options

### Key Configuration Variables

```php
// JavaScript caching
$CFG->cachejs = true;          // Enable JS caching (default)
$CFG->jsrev = 1;               // JS revision number

// Theme settings
$CFG->themerev = 1;            // Theme revision number
$CFG->themedesignermode = false; // Disable theme caching

// Performance
$CFG->yuicomboloading = true;  // YUI combo loading
$CFG->slasharguments = 1;      // Use slash arguments in URLs

// Development
$CFG->debug = 0;               // Debug level
$CFG->debugdisplay = 0;        // Display debug info
```

### Cache Management

1. **Clear all caches**
   - Admin: Site administration → Development → Purge all caches
   - CLI: `php admin/cli/purge_caches.php`

2. **JavaScript-only cache clear**
   ```php
   require_once('config.php');
   js_reset_all_caches();
   ```

### Debugging

1. **View generated URLs**: Enable developer debug mode
2. **Check cache files**: Look in `$CFG->localcachedir`
3. **Disable caching**: Set `$CFG->cachejs = false`
4. **Network inspection**: Use browser dev tools

## Troubleshooting

### Common Issues

1. **Changes not appearing**
   - Did you run Grunt after modifying source files?
   - Clear browser cache
   - Increment `$CFG->jsrev`

2. **404 errors for modules**
   - Check module name matches file location
   - Ensure built files exist in `amd/build/`
   - Verify component name is correct

3. **JavaScript errors**
   - Check browser console for details
   - Verify dependencies are correct
   - Test in debug mode for better error messages

4. **Build failures**
   - Check Node.js version (8.9 required)
   - Run `npm install` to update dependencies
   - Check for JSHint/ESLint errors

### Development Tips

1. Use browser dev tools Network tab to see asset requests
2. Check Moodle's JavaScript console (`M.cfg`, `Y.config`)
3. Use `M.util.js_pending()` and `js_complete()` for debugging
4. Monitor `$CFG->localcachedir` for generated files

## Summary

Moodle's asset management system prioritizes performance and maintainability through:
- Pre-built assets with Grunt
- Aggressive caching with revision-based URLs
- Modern AMD modules with RequireJS
- Backward compatibility with legacy code
- Clear separation between development and production modes

Understanding this system is crucial for effective Moodle development and debugging asset-related issues.