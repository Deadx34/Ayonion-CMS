-- Fix for campaigns table: Add missing image URL columns
-- Run this in phpMyAdmin for database: ayonions_ayonion_cms

ALTER TABLE campaigns 
ADD COLUMN evidence_image_url VARCHAR(500) DEFAULT NULL AFTER evidence_files;

ALTER TABLE campaigns 
ADD COLUMN creative_image_url VARCHAR(500) DEFAULT NULL AFTER evidence_image_url;
