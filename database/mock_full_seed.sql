-- Mock full seed data for Ayonion CMS
-- Includes inserts for all tables and all fields
-- Company name set to: Creative studios

START TRANSACTION;

SET FOREIGN_KEY_CHECKS = 0;

DELETE FROM invoice_items;
DELETE FROM month_end_logs;
DELETE FROM invoices;
DELETE FROM documents;
DELETE FROM campaigns;
DELETE FROM content_credits;
DELETE FROM users;
DELETE FROM settings;
DELETE FROM clients;

SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO settings
(id, company_name, logo_url, logo_light, logo_dark, email, phone, address, website, created_at, updated_at)
VALUES
(1, 'Creative studios', '/uploads/logos/main.png', '/uploads/logos/light.png', '/uploads/logos/dark.png', 'info@ayonion.com', '+60-12-3456789', 'Kuala Lumpur, Malaysia', 'https://ayonion.com', '2026-03-01 09:00:00', '2026-03-01 09:00:00');

INSERT INTO users
(id, username, password, role, is_temp_password, full_name, email, created_at, updated_at)
VALUES
(1, 'admin', '$2y$12$Om6mSr3CP.GAi2Qs4k4eR.MCz691OzARO.koD1qtvFEkPmEwJDxrK', 'admin', 0, 'System Admin', 'admin@ayonion.com', '2026-03-01 09:05:00', '2026-03-01 09:05:00'),
(2, 'marketer1', '$2y$12$Om6mSr3CP.GAi2Qs4k4eR.MCz691OzARO.koD1qtvFEkPmEwJDxrK', 'marketer', 1, 'Maya Marketer', 'maya@ayonion.com', '2026-03-01 09:06:00', '2026-03-01 09:06:00'),
(3, 'finance1', '$2y$12$Om6mSr3CP.GAi2Qs4k4eR.MCz691OzARO.koD1qtvFEkPmEwJDxrK', 'finance', 1, 'Farid Finance', 'farid@ayonion.com', '2026-03-01 09:07:00', '2026-03-01 09:07:00'),
(4, 'moderator1', '$2y$12$Om6mSr3CP.GAi2Qs4k4eR.MCz691OzARO.koD1qtvFEkPmEwJDxrK', 'moderator', 1, 'Mona Moderator', 'mona@ayonion.com', '2026-03-01 09:08:00', '2026-03-01 09:08:00');

INSERT INTO clients
(id, partner_id, company_name, renewal_date, subscription_months, subscription_start_date, subscription_end_date, package_credits, managing_platforms, industry, logo_url, extra_credits, carried_forward_credits, used_credits, total_ad_budget, total_spent, is_paused, pause_start_date, pause_end_date, last_carry_forward, last_carry_forward_date, created_at, updated_at)
VALUES
(1001, 'PTN-ALPHA-001', 'Alpha Tech Sdn Bhd', '2026-12-31', 12, '2026-01-01', '2026-12-31', 120, 'Facebook,Instagram,TikTok', 'Technology', '/uploads/logos/alpha.png', 15, 20, 55, 50000.00, 18250.75, 0, NULL, NULL, '2026-02-28 23:59:59', '2026-02-28', '2026-01-01 10:00:00', '2026-03-10 15:00:00'),
(1002, 'PTN-BRAVO-002', 'Bravo Foods Sdn Bhd', '2026-09-30', 6, '2026-04-01', '2026-09-30', 80, 'Facebook,Google', 'F&B', '/uploads/logos/bravo.png', 10, 5, 22, 30000.00, 9450.00, 1, '2026-03-10', '2026-03-20', '2026-02-28 23:59:59', '2026-02-28', '2026-02-15 11:30:00', '2026-03-20 17:30:00');

INSERT INTO content_credits
(id, client_id, credit_type, credits, date, status, published_date, content_url, image_url, created_at, updated_at)
VALUES
(1, 1001, 'Social Post', 5, '2026-03-05', 'Published', '2026-03-06', 'https://facebook.com/alpha/posts/100', '/uploads/social-icons/post1.jpg', '2026-03-05 10:10:00', '2026-03-06 12:00:00'),
(2, 1001, 'Reel', 3, '2026-03-12', 'In Progress', NULL, NULL, '/uploads/social-icons/reel1.jpg', '2026-03-12 09:00:00', '2026-03-12 09:00:00'),
(3, 1002, 'Blog Article', 2, '2026-03-08', 'Published', '2026-03-09', 'https://bravofoods.com/blog/new-menu', '/uploads/social-icons/blog1.jpg', '2026-03-08 14:20:00', '2026-03-09 08:45:00');

INSERT INTO campaigns
(id, client_id, platform, ad_name, ad_id, result_type, results, cpr, reach, impressions, spend, campaign_start_date, campaign_start_time, campaign_end_date, campaign_end_time, quality_ranking, conversion_ranking, evidence_image_url, creative_image_url, created_at, updated_at)
VALUES
(2001, 1001, 'Facebook', 'Alpha Lead Gen Q1', 'FB-ALPHA-001', 'Leads', 120, 18.75, 45000, 98000, 2250.00, '2026-03-01', '09:00:00', '2026-03-15', '23:00:00', 'Above Average', 'Average', '/uploads/campaigns/evidence_alpha_1.png', '/uploads/campaigns/creative_alpha_1.png', '2026-03-01 08:30:00', '2026-03-16 09:00:00'),
(2002, 1002, 'Google', 'Bravo Search Promo', 'GG-BRAVO-002', 'Clicks', 860, 2.35, 32000, 76000, 2021.00, '2026-03-03', '10:00:00', '2026-03-18', '22:00:00', 'Average', 'Above Average', '/uploads/campaigns/evidence_bravo_1.png', '/uploads/campaigns/creative_bravo_1.png', '2026-03-03 09:15:00', '2026-03-19 08:00:00');

INSERT INTO documents
(id, document_number, client_id, client_name, doc_type, item_type, description, quantity, unit_price, total, date, created_at, updated_at)
VALUES
(3001, 'INV-2026-0001', 1001, 'Alpha Tech Sdn Bhd', 'invoice', 'Ads Management', 'March social ads management fee', 1.00, 2500.00, 2500.00, '2026-03-20', '2026-03-20 11:00:00', '2026-03-20 11:00:00'),
(3002, 'RCPT-2026-0001', 1002, 'Bravo Foods Sdn Bhd', 'receipt', 'Campaign Spend', 'Payment received for March campaign', 1.00, 2021.00, 2021.00, '2026-03-21', '2026-03-21 10:20:00', '2026-03-21 10:20:00');

INSERT INTO invoices
(id, client_id, total_amount, invoice_number, due_date, notes, status, created_at, updated_at)
VALUES
(4001, 1001, 3750.00, 'AYN-INV-2026-0001', '2026-04-05', 'Net 15 terms', 'sent', '2026-03-20 12:00:00', '2026-03-20 12:00:00'),
(4002, 1002, 2521.00, 'AYN-INV-2026-0002', '2026-04-07', 'Includes ad spend and service fee', 'draft', '2026-03-21 09:40:00', '2026-03-21 09:40:00');

INSERT INTO invoice_items
(id, invoice_id, campaign_id, amount, description, created_at)
VALUES
(1, 4001, 2001, 2250.00, 'Facebook ad spend reimbursement', '2026-03-20 12:01:00'),
(2, 4001, NULL, 1500.00, 'Campaign management fee', '2026-03-20 12:02:00'),
(3, 4002, 2002, 2021.00, 'Google ad spend reimbursement', '2026-03-21 09:41:00'),
(4, 4002, NULL, 500.00, 'Search campaign setup fee', '2026-03-21 09:42:00');

INSERT INTO month_end_logs
(id, client_id, previous_available, credits_carried, credits_expired, new_renewal_date, processed_date)
VALUES
(1, 1001, 35, 20, 15, '2026-03-31', '2026-03-31 23:59:59'),
(2, 1002, 12, 5, 7, '2026-03-31', '2026-03-31 23:59:59');

COMMIT;
