-- Ayonion CMS base seed
-- Import AFTER base_schema.sql

START TRANSACTION;

-- Singleton settings row used by handler_settings.php
INSERT INTO settings (id, company_name, logo_url, logo_light, logo_dark, email, phone, address, website, bank_name, bank_branch, bank_account_name, bank_account_number, doc_thank_you_text, doc_payment_instructions, doc_bank_intro)
VALUES (1, 'Ayonion Studios', '', '', '', '', '', '', '', 'NDB Bank', 'Kadawatha Branch', 'Ayonion Studios (pvt) Ltd', '101001037178', 'Thank you for reaching out Ayonion Studios. We will deliver you the best service possible.', '• All cheques should be crossed and made payable to Ayonion Studios (pvt) Ltd.\n• A 50% of advance payment is required. (Excluding package payments)\n• The quotation is valid for two weeks from the day issued.\n• This is a computer generated quotation, No signature required.', 'Please deposit the advance payment to the below account')
ON DUPLICATE KEY UPDATE
    company_name = VALUES(company_name),
    logo_url = VALUES(logo_url),
    logo_light = VALUES(logo_light),
    logo_dark = VALUES(logo_dark),
    email = VALUES(email),
    phone = VALUES(phone),
    address = VALUES(address),
    website = VALUES(website),
    bank_name = VALUES(bank_name),
    bank_branch = VALUES(bank_branch),
    bank_account_name = VALUES(bank_account_name),
    bank_account_number = VALUES(bank_account_number),
    doc_thank_you_text = VALUES(doc_thank_you_text),
    doc_payment_instructions = VALUES(doc_payment_instructions),
    doc_bank_intro = VALUES(doc_bank_intro);

-- Default admin for first login
-- IMPORTANT: change this password immediately after first login.
-- Plain password for this hash is: admin123
INSERT INTO users (id, username, password, role, is_temp_password, full_name, email)
VALUES (1, 'admin', '$2y$12$Om6mSr3CP.GAi2Qs4k4eR.MCz691OzARO.koD1qtvFEkPmEwJDxrK', 'admin', 1, 'System Admin', '')
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    password = VALUES(password),
    role = VALUES(role),
    is_temp_password = VALUES(is_temp_password),
    full_name = VALUES(full_name),
    email = VALUES(email);

COMMIT;
