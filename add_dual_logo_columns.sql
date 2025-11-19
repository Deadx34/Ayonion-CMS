-- Migration: Add dual logo support (light and dark backgrounds)
-- Run this SQL in your database to add logo_light and logo_dark columns

ALTER TABLE settings 
ADD COLUMN logo_light VARCHAR(500) DEFAULT '' AFTER logo_url,
ADD COLUMN logo_dark VARCHAR(500) DEFAULT '' AFTER logo_light;

-- Migrate existing logo_url to both light and dark if it exists
UPDATE settings 
SET logo_light = logo_url, logo_dark = logo_url 
WHERE logo_url != '' AND logo_url IS NOT NULL;

-- Note: logo_url column is kept for backward compatibility
-- You can manually set different logos for light and dark backgrounds through the UI
