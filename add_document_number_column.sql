-- Migration: Add document_number column to documents table
-- Date: November 15, 2025
-- Purpose: Support new document numbering format (Q10P001202511, I10P001202511, R10P001202511)

-- Add document_number column if it doesn't exist
ALTER TABLE documents 
ADD COLUMN IF NOT EXISTS document_number VARCHAR(50) UNIQUE AFTER id;

-- Create index for faster lookups
CREATE INDEX IF NOT EXISTS idx_document_number ON documents(document_number);

-- Create index for counting documents by type and month
CREATE INDEX IF NOT EXISTS idx_doc_type_date ON documents(doc_type, date);
