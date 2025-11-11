-- Migration: Add subscription tracking fields to clients table
-- Purpose: Enable automated carry forward system with subscription-based expiry

-- Add subscription tracking columns
ALTER TABLE clients 
ADD COLUMN IF NOT EXISTS subscription_months INT DEFAULT 12 COMMENT 'Duration of subscription in months',
ADD COLUMN IF NOT EXISTS subscription_start_date DATE COMMENT 'Date when subscription started',
ADD COLUMN IF NOT EXISTS subscription_end_date DATE COMMENT 'Date when subscription ends',
ADD COLUMN IF NOT EXISTS last_carry_forward_date DATE COMMENT 'Last date carry forward was processed';

-- Update existing clients with default subscription duration
UPDATE clients 
SET 
    subscription_months = 12,
    subscription_start_date = renewal_date,
    subscription_end_date = DATE_ADD(renewal_date, INTERVAL 12 MONTH)
WHERE subscription_months IS NULL;

-- Add indexes for performance
CREATE INDEX idx_renewal_date ON clients(renewal_date);
CREATE INDEX idx_subscription_end_date ON clients(subscription_end_date);
CREATE INDEX idx_last_carry_forward ON clients(last_carry_forward_date);

-- Verification query (comment out after running)
-- SELECT id, companyName, renewalDate, subscription_months, subscription_start_date, subscription_end_date, last_carry_forward_date 
-- FROM clients 
-- LIMIT 5;
