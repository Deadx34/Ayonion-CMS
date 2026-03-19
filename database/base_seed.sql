-- Ayonion CMS base seed
-- Import AFTER base_schema.sql

START TRANSACTION;

-- Singleton settings row used by handler_settings.php
INSERT INTO settings (id, company_name, logo_url, logo_light, logo_dark, email, phone, address, website)
VALUES (1, 'Ayonion Studios', '', '', '', '', '', '', '')
ON DUPLICATE KEY UPDATE
    company_name = VALUES(company_name),
    logo_url = VALUES(logo_url),
    logo_light = VALUES(logo_light),
    logo_dark = VALUES(logo_dark),
    email = VALUES(email),
    phone = VALUES(phone),
    address = VALUES(address),
    website = VALUES(website);

-- Default admin for first login
-- IMPORTANT: change this password immediately after first login.
-- Temporary plain password is supported by existing login handler.
INSERT INTO users (id, username, password, role, is_temp_password, full_name, email)
VALUES (1, 'admin', 'admin123', 'admin', 1, 'System Admin', '')
ON DUPLICATE KEY UPDATE
    username = VALUES(username),
    role = VALUES(role),
    is_temp_password = VALUES(is_temp_password),
    full_name = VALUES(full_name),
    email = VALUES(email);

COMMIT;
