-- Add subscription pause/resume columns to clients table
ALTER TABLE clients 
ADD COLUMN is_paused BOOLEAN DEFAULT 0 AFTER subscription_end_date,
ADD COLUMN pause_start_date DATE DEFAULT NULL AFTER is_paused,
ADD COLUMN pause_end_date DATE DEFAULT NULL AFTER pause_start_date;
