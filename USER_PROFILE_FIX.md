# User Profile Migration Instructions

## Issue Fixed
The 500 Internal Server Error when accessing the Profile page has been temporarily fixed. The `handler_users.php` file now gracefully handles missing database columns.

## What Happened
The Profile feature requires two additional columns in the `users` table:
- `full_name` (VARCHAR 255)
- `email` (VARCHAR 255)

These columns don't exist in your database yet, which caused the 500 error.

## Temporary Fix Applied ✓
The `handler_users.php` file has been updated to:
- Load profile data even if columns are missing (shows NULL values)
- Show a helpful error message when trying to update profile before migration

## To Fully Enable Profile Features

### Option 1: Web-Based Migration (Recommended)
1. Upload the `run_user_profile_migration.php` file to your live server
2. Open it in your browser: `https://ayonion-cms.byethost33.com/ayonion-cms/run_user_profile_migration.php`
3. The script will automatically add the required columns
4. Return to your CMS and refresh

### Option 2: Manual SQL Execution
If you have phpMyAdmin or database access, run these SQL commands:

```sql
ALTER TABLE users ADD COLUMN full_name VARCHAR(255) DEFAULT NULL;
ALTER TABLE users ADD COLUMN email VARCHAR(255) DEFAULT NULL;
CREATE INDEX idx_users_email ON users(email);
```

### Option 3: Using the Migration File
1. Access your database management tool (phpMyAdmin, MySQL Workbench, etc.)
2. Open the `migrate_user_profile.sql` file
3. Copy and paste the SQL commands
4. Execute them

## After Migration
Once the migration is complete:
- Profile page will load without errors
- Users can update their full name and email
- All profile features will work correctly

## Files Modified
- `handler_users.php` - Added graceful handling for missing columns
- `run_user_profile_migration.php` - Created web-based migration script

## Need Help?
If you encounter any issues during migration, please check:
1. Database connection in `includes/config.php`
2. Your database user has ALTER TABLE permissions
3. The byethost33.com database is accessible

---
**Status:** Temporary fix applied ✓ | Migration pending ⏳
