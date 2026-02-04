-- Add your subdomain to the domains table
-- This will allow poss.codgoo.app to access your application

-- First, check your business_id
SELECT id, companyName FROM businesses;

-- Then insert the subdomain (replace business_id = 1 with your actual business_id)
INSERT INTO `domains` (`business_id`, `domain`, `is_verified`, `is_ssl_enabled`, `status`, `created_at`, `updated_at`) 
VALUES (1, 'poss.codgoo.app', 1, 1, 1, NOW(), NOW());

-- Verify it was added
SELECT * FROM domains;
