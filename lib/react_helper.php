<?php
// lib/react_helper.php
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
    
    // Load React bundle - inject directly since $PAGE->requires happens after header
    $scriptTags = '';
    // Always use production bundle for now (Vite dev server not always running)
    $scriptTags = '
    <link rel="stylesheet" href="' . $CFG->wwwroot . '/react-dist/style.css">
    <script src="' . $CFG->wwwroot . '/react-dist/moodle-react.iife.js"></script>';
    
    // Prepare props as JSON
    $propsjson = json_encode($props, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    
    // Build HTML
    $html = '';
    
    if ($options['wrapdiv']) {
        $class = !empty($options['class']) ? ' class="' . s($options['class']) . '"' : '';
        $html .= '<div id="' . s($elementid) . '"' . $class . ' data-react-component="' . s($component) . '" data-react-props=\'' . $propsjson . '\'></div>';
    }
    
    // Add script tags after the div element
    $html .= $scriptTags;
    
    // Add mounting script
    $html .= '<script>
    (function() {
        console.log("Looking for element: ' . s($elementid) . '");
        console.log("All elements with react-hello:", document.querySelectorAll("[id*=react-hello]"));
        
        // Wait for React to load (element already exists since script comes after div)
        var checkReact = setInterval(function() {
            if (window.MoodleReact && window.MoodleReact.mount) {
                clearInterval(checkReact);
                var element = document.getElementById("' . s($elementid) . '");
                console.log("Found element:", element);
                if (element) {
                    console.log("Mounting React component");
                    window.MoodleReact.mount(
                        "' . s($component) . '",
                        "#' . s($elementid) . '",
                        ' . $propsjson . '
                    );
                } else {
                    console.error("Element not found: ' . s($elementid) . '");
                    console.log("Available elements:", document.querySelectorAll("div"));
                }
            }
        }, 100);
    })();
    </script>';
    
    if ($options['return']) {
        return $html;
    }
    
    echo $html;
}