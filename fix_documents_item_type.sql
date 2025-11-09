-- Fix documents table item_type column to support larger JSON data
-- This migration changes item_type from VARCHAR(255) to TEXT
-- to properly store multiple items with descriptions in financial documents

USE ayonion_cms;

-- Change item_type column to TEXT to accommodate large JSON arrays
ALTER TABLE documents 
MODIFY COLUMN item_type TEXT;

-- Verify the change
DESCRIBE documents;
