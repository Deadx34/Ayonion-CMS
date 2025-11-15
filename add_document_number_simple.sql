-- Simple Migration for document_number column
-- Copy and paste this entire script into phpMyAdmin SQL tab

-- Add the document_number column
ALTER TABLE documents ADD COLUMN document_number VARCHAR(50) NULL AFTER id;

-- Add unique constraint
ALTER TABLE documents ADD UNIQUE KEY `document_number` (`document_number`);

-- Create indexes
CREATE INDEX idx_doc_type_date ON documents(doc_type, date);
