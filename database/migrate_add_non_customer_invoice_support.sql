-- Migration: Add support for non-customer invoices
-- This migration adds fields to the invoices table to support creating invoices for non-registered customers

ALTER TABLE invoices 
ADD COLUMN customer_name VARCHAR(255) NULL AFTER client_id,
ADD COLUMN is_non_customer BOOLEAN DEFAULT FALSE AFTER customer_name;

-- Update existing invoices to mark them as customer invoices
UPDATE invoices SET is_non_customer = FALSE WHERE is_non_customer IS NULL OR client_id IS NOT NULL;
