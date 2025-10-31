-- Migration: Add profile fields to users table
-- Date: 2025-10-31

-- Add full_name column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS full_name VARCHAR(255) DEFAULT NULL;

-- Add email column if it doesn't exist
ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(255) DEFAULT NULL;

-- Add index for faster email lookups
CREATE INDEX IF NOT EXISTS idx_users_email ON users(email);
