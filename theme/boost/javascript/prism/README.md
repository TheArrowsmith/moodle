# Prism.js for Moodle

This directory contains Prism.js files for syntax highlighting in Moodle forums.

## Installation

1. Download Prism.js from https://prismjs.com/download.html with the following components:
   - Core
   - Languages: Python, JavaScript, PHP, Java, C, C++, SQL, Bash, CSS, HTML, JSON, XML
   - Plugins: Line Numbers, Copy to Clipboard
   - Theme: Default (or Okaidia for dark theme)

2. Save the files as:
   - prism.js
   - prism.css

3. The theme modifications in lib.php will automatically load these files.

## Custom CSS

The prism-custom.css file contains Moodle-specific overrides to ensure proper integration with forum posts and theme compatibility.