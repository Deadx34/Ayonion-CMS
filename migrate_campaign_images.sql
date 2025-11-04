-- Migration: Add image fields to campaigns table
-- Date: 2025-11-04

-- Add evidence_image_url column if it doesn't exist
ALTER TABLE campaigns ADD COLUMN IF NOT EXISTS evidence_image_url TEXT DEFAULT NULL;

-- Add creative_image_url column if it doesn't exist
ALTER TABLE campaigns ADD COLUMN IF NOT EXISTS creative_image_url TEXT DEFAULT NULL;
