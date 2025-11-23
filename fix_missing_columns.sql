-- FIX FOR: Unknown column 'subscription_months' in 'INSERT INTO'
-- Run this in phpMyAdmin to add the missing columns

ALTER TABLE clients 
ADD COLUMN subscription_months INT DEFAULT 12 AFTER renewal_date;

ALTER TABLE clients 
ADD COLUMN subscription_start_date DATE DEFAULT NULL AFTER subscription_months;

ALTER TABLE clients 
ADD COLUMN subscription_end_date DATE DEFAULT NULL AFTER subscription_start_date;

-- Verify columns were added
DESCRIBE clients;
