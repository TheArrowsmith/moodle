<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Theme functions.
 *
 * @package    theme_boost
 * @copyright  2016 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Post process the CSS tree.
 *
 * @param string $tree The CSS tree.
 * @param theme_config $theme The theme config object.
 */
function theme_boost_css_tree_post_processor($tree, $theme) {
    $prefixer = new theme_boost\autoprefixer($tree);
    $prefixer->prefix();
}

/**
 * Inject additional SCSS.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_boost_get_extra_scss($theme) {
    $content = '';
    $imageurl = $theme->setting_file_url('backgroundimage', 'backgroundimage');

    // Sets the background image, and its settings.
    if (!empty($imageurl)) {
        $content .= 'body { ';
        $content .= "background-image: url('$imageurl'); background-size: cover;";
        $content .= ' }';
    }

    // Always return the background image with the scss when we have it.
    return !empty($theme->settings->scss) ? $theme->settings->scss . ' ' . $content : $content;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_boost_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {
    if ($context->contextlevel == CONTEXT_SYSTEM && ($filearea === 'logo' || $filearea === 'backgroundimage')) {
        $theme = theme_config::load('boost');
        // By default, theme files must be cache-able by both browsers and proxies.
        if (!array_key_exists('cacheability', $options)) {
            $options['cacheability'] = 'public';
        }
        return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
    } else {
        send_file_not_found();
    }
}

/**
 * Returns the main SCSS content.
 *
 * @param theme_config $theme The theme config object.
 * @return string
 */
function theme_boost_get_main_scss_content($theme) {
    global $CFG;

    $scss = '';
    $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;
    $fs = get_file_storage();

    $context = context_system::instance();
    if ($filename == 'default.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    } else if ($filename == 'plain.scss') {
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/plain.scss');
    } else if ($filename && ($presetfile = $fs->get_file($context->id, 'theme_boost', 'preset', 0, '/', $filename))) {
        $scss .= $presetfile->get_content();
    } else {
        // Safety fallback - maybe new installs etc.
        $scss .= file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
    }

    return $scss;
}

/**
 * Get compiled css.
 *
 * @return string compiled css
 */
function theme_boost_get_precompiled_css() {
    global $CFG;
    return file_get_contents($CFG->dirroot . '/theme/boost/style/moodle.css');
}

/**
 * Get SCSS to prepend.
 *
 * @param theme_config $theme The theme config object.
 * @return array
 */
function theme_boost_get_pre_scss($theme) {
    global $CFG;

    $scss = '';
    $configurable = [
        // Config key => [variableName, ...].
        'brandcolor' => ['primary'],
    ];

    // Prepend variables first.
    foreach ($configurable as $configkey => $targets) {
        $value = isset($theme->settings->{$configkey}) ? $theme->settings->{$configkey} : null;
        if (empty($value)) {
            continue;
        }
        array_map(function($target) use (&$scss, $value) {
            $scss .= '$' . $target . ': ' . $value . ";\n";
        }, (array) $targets);
    }

    // Prepend pre-scss.
    if (!empty($theme->settings->scsspre)) {
        $scss .= $theme->settings->scsspre;
    }

    if (!empty($theme->settings->fontsize)) {
        $scss .= '$font-size-base: ' . (1 / 100 * $theme->settings->fontsize) . "rem !default;\n";
    }

    return $scss;
}

/**
 * Inject additional theme assets for syntax highlighting
 *
 * @return string HTML to be placed before standard HTML head
 */
function theme_boost_before_standard_html_head() {
    global $PAGE;
    
    // Add Prism CSS
    $prismcss = new moodle_url('/theme/boost/javascript/prism/prism.css');
    
    $html = '';
    
    // Check if files exist before including
    if (file_exists($PAGE->theme->dir . '/javascript/prism/prism.css')) {
        $PAGE->requires->css($prismcss);
    }
    
    // Only include custom CSS if it exists
    if (file_exists($PAGE->theme->dir . '/javascript/prism/prism-custom.css')) {
        $customcss = new moodle_url('/theme/boost/javascript/prism/prism-custom.css');
        $PAGE->requires->css($customcss);
    }
    
    return $html;
}

/**
 * Add JavaScript to footer for syntax highlighting
 *
 * @return string HTML to be placed before footer
 */
function theme_boost_before_footer() {
    global $PAGE;
    
    // Add Prism JS if it exists
    $prismjs = $PAGE->theme->dir . '/javascript/prism/prism.js';
    if (file_exists($prismjs)) {
        $PAGE->requires->js(new moodle_url('/theme/boost/javascript/prism/prism.js'), true);
        
        // Add initialization script
        $PAGE->requires->js_init_code('
            document.addEventListener("DOMContentLoaded", function() {
                if (typeof Prism !== "undefined") {
                    // Function to process code blocks and add language classes
                    function processCodeBlocks() {
                        // Find all pre > code blocks without language class
                        var codeBlocks = document.querySelectorAll("pre > code:not([class*=\"language-\"])");
                        codeBlocks.forEach(function(block) {
                            var content = block.textContent || block.innerText;
                            var detectedLang = "";
                            
                            // Simple language detection based on content
                            if (content.match(/\bdef\s+\w+\s*\(|import\s+\w+|print\s*\(/)) {
                                detectedLang = "python";
                            } else if (content.match(/\bfunction\s+\w+\s*\(|console\.log|var\s+\w+|let\s+\w+|const\s+\w+/)) {
                                detectedLang = "javascript";
                            } else if (content.match(/<\?php|\$\w+|echo\s+|class\s+\w+/)) {
                                detectedLang = "php";
                            } else if (content.match(/SELECT\s+|FROM\s+|WHERE\s+|INSERT\s+INTO/i)) {
                                detectedLang = "sql";
                            } else if (content.match(/public\s+class|private\s+\w+|System\.out\.println/)) {
                                detectedLang = "java";
                            } else if (content.match(/#include|int\s+main|printf\s*\(/)) {
                                detectedLang = "c";
                            }
                            
                            // Add the language class
                            if (detectedLang) {
                                block.className = "language-" + detectedLang;
                            } else {
                                block.className = "language-none";
                            }
                        });
                        
                        // Also process code blocks that might have language info in text
                        var preBlocks = document.querySelectorAll("pre");
                        preBlocks.forEach(function(pre) {
                            // Check if there is a language hint before the pre block
                            var prevText = pre.previousSibling;
                            if (prevText && prevText.nodeType === 3) {
                                var match = prevText.textContent.match(/```(\w+)\s*$/);
                                if (match) {
                                    var code = pre.querySelector("code");
                                    if (code && !code.className) {
                                        code.className = "language-" + match[1];
                                    }
                                }
                            }
                        });
                    }
                    
                    // Process code blocks first
                    processCodeBlocks();
                    
                    // Auto-highlight on page load
                    Prism.highlightAll();
                    
                    // Re-highlight after AJAX content loads
                    if (typeof M !== "undefined" && M.util && M.util.js_complete) {
                        var originalJsComplete = M.util.js_complete;
                        M.util.js_complete = function(id) {
                            originalJsComplete.apply(this, arguments);
                            // Small delay to ensure DOM is updated
                            setTimeout(function() {
                                processCodeBlocks();
                                Prism.highlightAll();
                            }, 100);
                        };
                    }
                    
                    // Re-highlight when forum posts are loaded via AJAX
                    if (typeof Y !== "undefined") {
                        Y.on("contentchange", function() {
                            processCodeBlocks();
                            Prism.highlightAll();
                        }, document.body);
                    }
                }
            });
        ', false);
    }
    
    return '';
}
