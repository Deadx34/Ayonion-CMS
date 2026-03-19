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
