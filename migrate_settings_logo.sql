-- Migration script to fix logo_url column for data URLs
-- Run this SQL command in your database to fix the truncated logo URLs

ALTER TABLE settings MODIFY COLUMN logo_url TEXT DEFAULT '';

-- If the above doesn't work, try this alternative:
-- ALTER TABLE settings CHANGE logo_url logo_url TEXT DEFAULT '';
