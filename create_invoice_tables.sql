-- Create invoices table
CREATE TABLE IF NOT EXISTS invoices (
    id BIGINT PRIMARY KEY,
    client_id BIGINT,
    total_amount DECIMAL(12,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'draft',
    invoice_number VARCHAR(100),
    due_date DATE,
    notes TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(id)
);

-- Create invoice_items table
CREATE TABLE IF NOT EXISTS invoice_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_id BIGINT,
    campaign_id BIGINT,
    amount DECIMAL(12,2) DEFAULT 0.00,
    description TEXT,
    FOREIGN KEY (invoice_id) REFERENCES invoices(id),
    FOREIGN KEY (campaign_id) REFERENCES campaigns(id)
);

-- Add indexes for better performance
CREATE INDEX idx_invoices_client_id ON invoices(client_id);
CREATE INDEX idx_invoices_created_at ON invoices(created_at);
CREATE INDEX idx_invoice_items_invoice_id ON invoice_items(invoice_id);
CREATE INDEX idx_invoice_items_campaign_id ON invoice_items(campaign_id);
