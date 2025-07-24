# Multi-Language Support for Code Sandbox

The Code Sandbox module now supports three programming languages:
- Python
- Ruby
- Elixir

## Features

### For Instructors
- **Language Selection**: When creating a Code Sandbox activity, instructors can:
  - Choose the default language for the activity
  - Select which languages students are allowed to use
  - Provide language-specific starter code

### For Students
- **Language Dropdown**: Students can switch between allowed languages using a dropdown in the editor
- **Syntax Highlighting**: CodeMirror provides appropriate syntax highlighting for each language
- **Language Persistence**: The selected language is saved with submissions

## Technical Details

### Supported Languages and Versions
- **Python**: 3.8 (using `python:3.8-slim` Docker image)
- **Ruby**: 3.0 (using `ruby:3.0-slim` Docker image)
- **Elixir**: 1.13 (using `elixir:1.13-slim` Docker image)

### Database Changes
- Added `language` field to `mdl_codesandbox` table (default language for activity)
- Added `allowed_languages` field to `mdl_codesandbox` table (comma-separated list)
- Added `language` field to `mdl_codesandbox_submissions` table (tracks language per submission)

### API Changes
The execution API now accepts a `language` parameter:
```json
POST /execute
{
  "code": "print('Hello')",
  "language": "python"
}
```

### Security
- Each language runs in its own Docker container
- Same security restrictions apply to all languages:
  - Memory limit: 128MB
  - CPU limit: 1 core
  - No network access
  - 10-second execution timeout

## Setup Instructions

1. **Pull Docker Images**
   ```bash
   cd codesandbox-api
   ./pull-images.sh
   ```

2. **Update Database**
   - Navigate to Site Administration > Notifications
   - Run the database upgrade

3. **Clear Caches**
   - Site Administration > Development > Purge all caches

## Testing

Use the provided test script to verify all languages work:
```bash
cd codesandbox-api
python test_languages.py
```

## Limitations

- **Grading**: Currently, automatic grading only supports Python unit tests
- **Libraries**: Only standard libraries are available for each language
- **File I/O**: File operations are restricted to the container's temporary directory

## Future Enhancements

- Support for additional languages (JavaScript, Java, C++, etc.)
- Language-specific grading for Ruby and Elixir
- Package/dependency management per language
- Language-specific execution limits and configurations