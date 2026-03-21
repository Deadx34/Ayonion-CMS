-- Add financial document customization fields to settings table
-- Run this on existing databases if these columns do not exist

ALTER TABLE settings ADD COLUMN bank_name VARCHAR(255) NULL;
ALTER TABLE settings ADD COLUMN bank_branch VARCHAR(255) NULL;
ALTER TABLE settings ADD COLUMN bank_account_name VARCHAR(255) NULL;
ALTER TABLE settings ADD COLUMN bank_account_number VARCHAR(100) NULL;
ALTER TABLE settings ADD COLUMN doc_thank_you_text TEXT NULL;
ALTER TABLE settings ADD COLUMN doc_payment_instructions TEXT NULL;
ALTER TABLE settings ADD COLUMN doc_bank_intro TEXT NULL;

UPDATE settings
SET
    bank_name = COALESCE(NULLIF(bank_name, ''), 'NDB Bank'),
    bank_branch = COALESCE(NULLIF(bank_branch, ''), 'Kadawatha Branch'),
    bank_account_name = COALESCE(NULLIF(bank_account_name, ''), 'Ayonion Studios (pvt) Ltd'),
    bank_account_number = COALESCE(NULLIF(bank_account_number, ''), '101001037178'),
    doc_thank_you_text = COALESCE(NULLIF(doc_thank_you_text, ''), 'Thank you for reaching out Ayonion Studios. We will deliver you the best service possible.'),
    doc_payment_instructions = COALESCE(NULLIF(doc_payment_instructions, ''), '• All cheques should be crossed and made payable to Ayonion Studios (pvt) Ltd.\n• A 50% of advance payment is required. (Excluding package payments)\n• The quotation is valid for two weeks from the day issued.\n• This is a computer generated quotation, No signature required.'),
    doc_bank_intro = COALESCE(NULLIF(doc_bank_intro, ''), 'Please deposit the advance payment to the below account')
WHERE id = 1;
