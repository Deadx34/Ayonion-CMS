-- Migration: Enable cascading deletes for campaigns when a client is deleted
ALTER TABLE campaigns
DROP FOREIGN KEY campaigns_ibfk_1;
ALTER TABLE campaigns
ADD CONSTRAINT campaigns_ibfk_1 FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE;