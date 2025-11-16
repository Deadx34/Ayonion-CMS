-- Migration: Add website column to settings table
-- Date: November 17, 2025
-- Purpose: Add website field to company settings

-- Add website column if it doesn't exist
ALTER TABLE settings ADD COLUMN website VARCHAR(255) DEFAULT '' AFTER phone;
