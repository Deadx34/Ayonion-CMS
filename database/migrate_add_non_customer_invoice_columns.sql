-- Migration: Add non-customer invoice support to invoices table
-- This adds columns to support non-customer invoices

ALTER TABLE invoices ADD COLUMN customer_name VARCHAR(255) NULL AFTER client_id;
ALTER TABLE invoices ADD COLUMN is_non_customer BOOLEAN DEFAULT FALSE AFTER customer_name;

-- Add index on is_non_customer for faster queries
CREATE INDEX idx_is_non_customer ON invoices(is_non_customer);
CREATE INDEX idx_client_id_nonnull ON invoices(client_id) WHERE client_id IS NOT NULL;
