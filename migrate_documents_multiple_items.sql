-- Migration script to add item_order field to documents table
-- This allows multiple items per document with proper ordering

-- Add item_order column to documents table
ALTER TABLE documents ADD COLUMN item_order INT DEFAULT 0;

-- Update existing records to have item_order = 0 (they are single items)
UPDATE documents SET item_order = 0 WHERE item_order IS NULL;

-- Add index for better performance when querying documents by ID and order
CREATE INDEX idx_documents_id_order ON documents(id, item_order);
