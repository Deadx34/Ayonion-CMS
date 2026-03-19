-- Ayonion CMS base schema
-- Import this file into a newly created database before first app login.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS month_end_logs;
DROP TABLE IF EXISTS invoice_items;
DROP TABLE IF EXISTS invoices;
DROP TABLE IF EXISTS documents;
DROP TABLE IF EXISTS campaigns;
DROP TABLE IF EXISTS content_credits;
DROP TABLE IF EXISTS settings;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS clients;

CREATE TABLE clients (
    id BIGINT UNSIGNED NOT NULL,
    partner_id VARCHAR(100) NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    renewal_date DATE NULL,
    subscription_months INT NOT NULL DEFAULT 12,
    subscription_start_date DATE NULL,
    subscription_end_date DATE NULL,
    package_credits INT NOT NULL DEFAULT 0,
    managing_platforms TEXT NULL,
    industry VARCHAR(150) NULL,
    logo_url TEXT NULL,
    extra_credits INT NOT NULL DEFAULT 0,
    carried_forward_credits INT NOT NULL DEFAULT 0,
    used_credits INT NOT NULL DEFAULT 0,
    total_ad_budget DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total_spent DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    is_paused TINYINT(1) NOT NULL DEFAULT 0,
    pause_start_date DATE NULL,
    pause_end_date DATE NULL,
    last_carry_forward DATETIME NULL,
    last_carry_forward_date DATE NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_clients_partner_id (partner_id),
    KEY idx_clients_renewal_date (renewal_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin','marketer','finance','moderator') NOT NULL DEFAULT 'marketer',
    is_temp_password TINYINT(1) NOT NULL DEFAULT 1,
    full_name VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_users_username (username),
    KEY idx_users_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE settings (
    id TINYINT UNSIGNED NOT NULL,
    company_name VARCHAR(255) NOT NULL,
    logo_url TEXT NULL,
    logo_light TEXT NULL,
    logo_dark TEXT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(50) NULL,
    address TEXT NULL,
    website VARCHAR(255) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE content_credits (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id BIGINT UNSIGNED NOT NULL,
    credit_type VARCHAR(100) NOT NULL,
    credits INT NOT NULL DEFAULT 0,
    date DATE NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'In Progress',
    published_date DATE NULL,
    content_url TEXT NULL,
    image_url TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_content_credits_client_id (client_id),
    KEY idx_content_credits_date (date),
    CONSTRAINT fk_content_credits_client
        FOREIGN KEY (client_id) REFERENCES clients(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE campaigns (
    id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    platform VARCHAR(100) NOT NULL,
    ad_name VARCHAR(255) NOT NULL,
    ad_id VARCHAR(100) NULL,
    result_type VARCHAR(100) NULL,
    results INT NOT NULL DEFAULT 0,
    cpr DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    reach INT NOT NULL DEFAULT 0,
    impressions INT NOT NULL DEFAULT 0,
    spend DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    campaign_start_date DATE NULL,
    campaign_start_time TIME NULL,
    campaign_end_date DATE NULL,
    campaign_end_time TIME NULL,
    quality_ranking VARCHAR(100) NULL,
    conversion_ranking VARCHAR(100) NULL,
    evidence_image_url TEXT NULL,
    creative_image_url TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_campaigns_client_id (client_id),
    KEY idx_campaigns_dates (campaign_start_date, campaign_end_date),
    CONSTRAINT fk_campaigns_client
        FOREIGN KEY (client_id) REFERENCES clients(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE documents (
    id BIGINT UNSIGNED NOT NULL,
    document_number VARCHAR(30) NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    client_name VARCHAR(255) NOT NULL,
    doc_type VARCHAR(30) NOT NULL,
    item_type TEXT NOT NULL,
    description TEXT NULL,
    quantity DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    unit_price DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    date DATE NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_documents_document_number (document_number),
    KEY idx_documents_client_id (client_id),
    KEY idx_documents_type_date (doc_type, date),
    CONSTRAINT fk_documents_client
        FOREIGN KEY (client_id) REFERENCES clients(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoices (
    id BIGINT UNSIGNED NOT NULL,
    client_id BIGINT UNSIGNED NOT NULL,
    total_amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    invoice_number VARCHAR(30) NOT NULL,
    due_date DATE NULL,
    notes TEXT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'draft',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_invoices_invoice_number (invoice_number),
    KEY idx_invoices_client_id (client_id),
    KEY idx_invoices_created_at (created_at),
    CONSTRAINT fk_invoices_client
        FOREIGN KEY (client_id) REFERENCES clients(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoice_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    invoice_id BIGINT UNSIGNED NOT NULL,
    campaign_id BIGINT UNSIGNED NULL,
    amount DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    description TEXT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_invoice_items_invoice_id (invoice_id),
    KEY idx_invoice_items_campaign_id (campaign_id),
    CONSTRAINT fk_invoice_items_invoice
        FOREIGN KEY (invoice_id) REFERENCES invoices(id)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_invoice_items_campaign
        FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE month_end_logs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    client_id BIGINT UNSIGNED NOT NULL,
    previous_available INT NOT NULL DEFAULT 0,
    credits_carried INT NOT NULL DEFAULT 0,
    credits_expired INT NOT NULL DEFAULT 0,
    new_renewal_date DATE NOT NULL,
    processed_date DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    KEY idx_month_end_logs_client_id (client_id),
    KEY idx_month_end_logs_processed_date (processed_date),
    CONSTRAINT fk_month_end_logs_client
        FOREIGN KEY (client_id) REFERENCES clients(id)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
