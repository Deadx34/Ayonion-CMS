-- Create the database
-- CREATE DATABASE IF NOT EXISTS ayonion_db;
-- USE ayonion_db;

-- Table: clients
CREATE TABLE IF NOT EXISTS clients (
    id BIGINT PRIMARY KEY,
    partner_id VARCHAR(255) UNIQUE,
    company_name VARCHAR(255),
    renewal_date DATE,
    package_credits INT DEFAULT 0,
    managing_platforms VARCHAR(255),
    industry VARCHAR(255),
    logo_url VARCHAR(255),
    extra_credits INT DEFAULT 0,
    carried_forward_credits INT DEFAULT 0,
    used_credits INT DEFAULT 0,
    total_ad_budget DECIMAL(12,2) DEFAULT 0.00,
    total_spent DECIMAL(12,2) DEFAULT 0.00
);

-- Table: campaigns
CREATE TABLE IF NOT EXISTS campaigns (
    id BIGINT PRIMARY KEY,
    client_id BIGINT,
    platform VARCHAR(255),
    ad_name VARCHAR(255),
    ad_id VARCHAR(255),
    result_type VARCHAR(255),
    results INT DEFAULT 0,
    cpr DECIMAL(12,2) DEFAULT 0.00,
    reach INT DEFAULT 0,
    impressions INT DEFAULT 0,
    spend DECIMAL(12,2) DEFAULT 0.00,
    quality_ranking VARCHAR(255),
    conversion_ranking VARCHAR(255),
    evidence_urls TEXT,
    evidence_files TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Table: users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) UNIQUE,
    password VARCHAR(255),
    role VARCHAR(50),
    is_temp_password BOOLEAN DEFAULT 1
);

-- Table: documents
CREATE TABLE IF NOT EXISTS documents (
    id BIGINT PRIMARY KEY,
    client_id BIGINT,
    client_name VARCHAR(255),
    doc_type VARCHAR(50),
    item_type VARCHAR(255),
    description TEXT,
    quantity INT DEFAULT 0,
    unit_price DECIMAL(12,2) DEFAULT 0.00,
    total DECIMAL(12,2) DEFAULT 0.00,
    date DATE,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Table: content_credits
CREATE TABLE IF NOT EXISTS content_credits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id BIGINT,
    credit_type VARCHAR(255),
    credits INT DEFAULT 0,
    date DATE,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Table: settings (singleton row id=1)
CREATE TABLE IF NOT EXISTS settings (
    id TINYINT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    logo_url TEXT DEFAULT '',
    email VARCHAR(255) DEFAULT '',
    phone VARCHAR(100) DEFAULT '',
    address TEXT
);

-- Demo credentials for users table (bcrypt-hashed: password = "password")
-- NOTE: These example hashes correspond to the plaintext "password" and should be replaced in real deployments.
INSERT INTO users (username, password, role, is_temp_password) VALUES
  ('admin', '$2y$10$7G6qz3w5q0f2xW0zZ8qvluZV0b6F0cQw8z0jZ0t5yJx9e7i3Jr1Fe', 'admin', 1),
  ('marketer', '$2y$10$7G6qz3w5q0f2xW0zZ8qvluZV0b6F0cQw8z0jZ0t5yJx9e7i3Jr1Fe', 'marketer', 1),
  ('finance', '$2y$10$7G6qz3w5q0f2xW0zZ8qvluZV0b6F0cQw8z0jZ0t5yJx9e7i3Jr1Fe', 'finance', 1);
