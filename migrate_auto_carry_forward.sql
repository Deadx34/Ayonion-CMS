-- AYONION-CMS/migrate_auto_carry_forward.sql
-- Add column to track last auto carry forward date

ALTER TABLE clients ADD COLUMN IF NOT EXISTS last_carry_forward DATETIME DEFAULT NULL;

-- Add index for faster queries on renewal date
CREATE INDEX IF NOT EXISTS idx_renewal_date ON clients(renewal_date);

-- Update existing clients to set default package credits if not set
UPDATE clients SET package_credits = 40 WHERE package_credits = 0 OR package_credits IS NULL;
