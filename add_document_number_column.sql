-- Migration: Add document_number column to documents table
-- Date: November 15, 2025
-- Purpose: Support new document numbering format (Q10P001202511, I10P001202511, R10P001202511)

-- Add document_number column (compatible with older MySQL versions)
-- Check if column exists before adding
SET @column_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'documents' AND COLUMN_NAME = 'document_number');

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE documents ADD COLUMN document_number VARCHAR(50) NULL AFTER id',
    'SELECT "Column already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add unique constraint if needed
SET @constraint_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'documents' AND CONSTRAINT_NAME = 'document_number');

SET @sql = IF(@constraint_exists = 0, 
    'ALTER TABLE documents ADD UNIQUE KEY document_number (document_number)',
    'SELECT "Unique constraint already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create index for faster lookups
SET @index_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'documents' AND INDEX_NAME = 'idx_document_number');

SET @sql = IF(@index_exists = 0, 
    'CREATE INDEX idx_document_number ON documents(document_number)',
    'SELECT "Index idx_document_number already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Create index for counting documents by type and month
SET @index_exists2 = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'documents' AND INDEX_NAME = 'idx_doc_type_date');

SET @sql = IF(@index_exists2 = 0, 
    'CREATE INDEX idx_doc_type_date ON documents(doc_type, date)',
    'SELECT "Index idx_doc_type_date already exists" as message');

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
