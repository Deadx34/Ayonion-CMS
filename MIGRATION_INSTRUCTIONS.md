# Database Migration Instructions

## Issue
The content credit system has been updated with new fields (image upload and URL), but the database needs to be updated to include these new columns.

## Solution
Run the migration script to add the required columns to the `content_credits` table.

## Steps to Fix

### Option 1: Web-based Migration (Recommended)
1. Open your browser and navigate to: `https://ayonion-cms.byethost33.com/ayonion-cms/migrate_database.php`
2. The script will automatically add the required columns
3. You should see a success message

### Option 2: Manual Database Update
If the web migration doesn't work, run this SQL in your database:

```sql
ALTER TABLE content_credits 
ADD COLUMN content_url VARCHAR(500) DEFAULT NULL,
ADD COLUMN image_url VARCHAR(500) DEFAULT NULL,
ADD COLUMN status VARCHAR(50) DEFAULT 'In Progress',
ADD COLUMN published_date DATE DEFAULT NULL;

UPDATE content_credits SET status = 'In Progress' WHERE status IS NULL;
```

## New Columns Added
- `content_url` - Stores the URL to published content
- `image_url` - Stores the path to uploaded content images
- `status` - Content status (In Progress, Published, etc.)
- `published_date` - Date when content was published

## After Migration
Once the migration is complete, the content credit system will support:
- Image uploads for content
- URL links to published content
- Status tracking
- Published date tracking

The system will work with both old and new content records.
