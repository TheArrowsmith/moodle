# Database Upgrade Required

If you're seeing errors about missing language fields, you need to upgrade the database:

1. **Navigate to Site Administration > Notifications**
   - Log in as administrator
   - Go to Site Administration > Notifications
   - Moodle will detect the database changes and show an upgrade screen

2. **Run the upgrade**
   - Click "Upgrade Moodle database now"
   - Wait for the upgrade to complete

3. **Clear caches**
   - Go to Site Administration > Development > Purge all caches
   - Click "Purge all caches"

The upgrade will add the following fields:
- `language` field to the `mdl_codesandbox` table
- `allowed_languages` field to the `mdl_codesandbox` table  
- `language` field to the `mdl_codesandbox_submissions` table

After the upgrade, the language selection features will work properly.