# Moodle Template Rendering System

## Overview

Moodle uses a sophisticated template rendering system that separates presentation logic from business logic, similar to the MVC pattern but with its own unique implementation. Instead of traditional views rendered from controllers like in Rails, Moodle uses a combination of **renderers**, **Mustache templates**, **themes**, and **output components**.

## Key Architectural Components

### 1. **Renderers** (`lib/outputrenderers.php`)
Renderers are PHP classes that generate HTML output. They act as the bridge between Moodle's data/logic layer and the presentation layer.

- **Base Renderer** (`renderer_base`): The foundation class that all renderers extend
- **Core Renderer** (`core_renderer`): The main renderer for standard Moodle output
- **Plugin Renderers** (`plugin_renderer_base`): Base class for module/plugin-specific renderers
- **Theme Renderers**: Theme-specific renderers that can override core rendering methods

### 2. **Mustache Templates** (`templates/*.mustache`)
Moodle uses the Mustache templating engine for separating HTML structure from PHP logic.

- Templates are stored in `templates/` directories throughout the codebase
- Use `{{variable}}` syntax for variable substitution
- Support conditionals `{{#condition}}...{{/condition}}`
- Can include other templates via `{{> template_name}}`

### 3. **Output Components** (`lib/outputcomponents.php`)
These are PHP classes that represent renderable elements:

- **Renderable Interface**: Marks classes as suitable for rendering
- **Templatable Interface**: Allows classes to export data for Mustache templates
- Common components: `user_picture`, `pix_icon`, `action_menu`, etc.

### 4. **Page Object** (`$PAGE` / `lib/pagelib.php`)
The global `$PAGE` object manages page-level information and coordinates rendering:

- Manages page context, layout, theme selection
- Provides access to renderers via `$PAGE->get_renderer()`
- Controls page state (before header, in body, done)
- Manages JavaScript and CSS requirements

### 5. **Theme System** (`theme/*/config.php`)
Themes control the visual presentation and can override renderers and templates:

- Each theme has a `config.php` defining layouts, parent themes, and settings
- Themes can provide custom renderers to override core rendering
- Support for responsive design through Bootstrap (Boost theme)

## Step-by-Step Page Rendering Flow

### 1. **Request Initialization**
```php
// Example from index.php
require_once('config.php');              // Load Moodle configuration
$PAGE->set_url('/', $urlparams);       // Set the page URL
$PAGE->set_pagelayout('frontpage');    // Choose layout template
```

### 2. **Context and Permissions Setup**
```php
require_course_login($SITE);            // Verify access permissions
$context = context_course::instance(SITEID);
$PAGE->set_context($context);
```

### 3. **Page Configuration**
```php
$PAGE->set_title($SITE->fullname);      // Set HTML title
$PAGE->set_heading($SITE->fullname);    // Set page heading
$PAGE->set_pagetype('site-index');      // Set page type for CSS classes
```

### 4. **Renderer Acquisition**
```php
// Get the appropriate renderer for the component
$renderer = $PAGE->get_renderer('core', 'course');
// For themes: $OUTPUT is a pre-initialized core_renderer
```

### 5. **Header Output**
```php
echo $OUTPUT->header();                  // Renders page header
```
This triggers:
- Theme layout file selection (e.g., `theme/boost/layout/columns2.php`)
- Template context preparation
- Mustache template rendering

### 6. **Content Rendering**
Content is rendered using various methods:

```php
// Direct renderer method
echo $renderer->course_section_cm_list($course, $section);

// Mustache template rendering
echo $OUTPUT->render_from_template('mod_forum/discussion_list', $data);

// Renderable component
$userpicture = new user_picture($user);
echo $OUTPUT->render($userpicture);
```

### 7. **Footer Output**
```php
echo $OUTPUT->footer();                  // Renders page footer
```

## Template Rendering Process

### 1. **Data Preparation**
```php
// Prepare data for template
$templatecontext = [
    'username' => $user->username,
    'courses' => array_map(function($course) {
        return ['name' => $course->fullname, 'id' => $course->id];
    }, $courses)
];
```

### 2. **Template Loading**
The renderer loads and compiles Mustache templates:
```php
// In renderer_base::render_from_template()
$mustache = $this->get_mustache();
$template = $mustache->loadTemplate($templatename);
```

### 3. **Template Compilation and Caching**
- Templates are compiled to PHP code for performance
- Cached in `localcachedir/mustache/` directory
- Cache is invalidated when theme revision changes

### 4. **Rendering**
```php
$html = $template->render($context);
```

## Theme Override Mechanism

Themes can override rendering at multiple levels:

### 1. **Renderer Overrides**
```php
// In theme/boost/classes/output/core_renderer.php
class core_renderer extends \core_renderer {
    public function header() {
        // Custom header implementation
    }
}
```

### 2. **Template Overrides**
- Place modified template in `theme/themename/templates/`
- Theme templates take precedence over core templates

### 3. **Layout Selection**
```php
// In theme/boost/config.php
$THEME->layouts = [
    'frontpage' => [
        'file' => 'columns2',
        'regions' => ['side-pre'],
        'defaultregion' => 'side-pre'
    ]
];
```

## Mustache Template Helpers

Moodle provides several helpers for use in templates:

- **`{{#str}}`**: String translation helper
- **`{{#pix}}`**: Image/icon helper
- **`{{#js}}`**: JavaScript inclusion helper
- **`{{#shortentext}}`**: Text truncation helper
- **`{{#userdate}}`**: Date formatting helper

Example:
```mustache
<h2>{{#str}}welcomemessage, mod_forum{{/str}}</h2>
<img src="{{#pix}}i/user, core{{/pix}}" alt="">
{{#js}}
require(['mod_forum/discussion'], function(Discussion) {
    Discussion.init();
});
{{/js}}
```

## Performance Considerations

1. **Template Caching**: Compiled templates are cached to improve performance
2. **Renderer Reuse**: Renderers are instantiated once per request
3. **Lazy Loading**: Components are loaded only when needed
4. **Theme Revision**: CSS and JS are versioned for effective caching

## Developer Best Practices

1. **Use Renderers**: Always use renderers for HTML generation, not direct echo statements
2. **Implement Templatable**: Make data classes implement `templatable` for easy template integration
3. **Theme Compatibility**: Test rendering with multiple themes
4. **Context Preparation**: Prepare all data before passing to templates
5. **Avoid Logic in Templates**: Keep templates simple, move logic to renderers

## Common Rendering Patterns

### Rendering a List
```php
$items = $DB->get_records('forum_posts', ['discussion' => $discussionid]);
$data = new stdClass();
$data->posts = array_map(function($item) use ($OUTPUT) {
    return [
        'message' => format_text($item->message),
        'author' => fullname($item->author),
        'timecreated' => userdate($item->timecreated)
    ];
}, $items);

echo $OUTPUT->render_from_template('mod_forum/post_list', $data);
```

### Custom Renderer Method
```php
class mod_forum_renderer extends plugin_renderer_base {
    public function display_discussion($discussion) {
        $data = $this->prepare_discussion_data($discussion);
        return $this->render_from_template('mod_forum/discussion', $data);
    }
}
```

## Summary

Moodle's rendering system provides a flexible, themeable architecture that separates concerns effectively:

- **PHP Renderers** handle logic and data preparation
- **Mustache Templates** define HTML structure
- **Themes** customize appearance and behavior
- **Output Components** provide reusable UI elements

This architecture allows for maintainable, testable code while supporting extensive customization through themes and plugins.