-- Fix for content_credits table: Add missing columns for content tracking
-- Run this in phpMyAdmin for database: ayonions_ayonion_cms

ALTER TABLE content_credits 
ADD COLUMN status VARCHAR(100) DEFAULT 'In Progress' AFTER credits;

ALTER TABLE content_credits 
ADD COLUMN published_date DATE DEFAULT NULL AFTER status;

ALTER TABLE content_credits 
ADD COLUMN content_url VARCHAR(500) DEFAULT NULL AFTER published_date;

ALTER TABLE content_credits 
ADD COLUMN image_url VARCHAR(500) DEFAULT NULL AFTER content_url;
