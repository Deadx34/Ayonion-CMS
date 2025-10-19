-- AYONION-CMS/migrate_content_images.sql - Add image and URL fields to content_credits table

-- Add new columns to content_credits table
ALTER TABLE content_credits 
ADD COLUMN content_url VARCHAR(500) DEFAULT NULL,
ADD COLUMN image_url VARCHAR(500) DEFAULT NULL,
ADD COLUMN status VARCHAR(50) DEFAULT 'In Progress',
ADD COLUMN published_date DATE DEFAULT NULL;

-- Update existing records to have default status
UPDATE content_credits SET status = 'In Progress' WHERE status IS NULL;
