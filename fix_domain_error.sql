-- Fix Domain Error 400
-- Run this SQL in phpMyAdmin or your database tool

-- Step 1: Create the domains table if it doesn't exist
CREATE TABLE IF NOT EXISTS `domains` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `business_id` bigint(20) UNSIGNED NOT NULL,
  `domain` varchar(255) NOT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `is_ssl_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `domains_business_id_foreign` (`business_id`),
  CONSTRAINT `domains_business_id_foreign` FOREIGN KEY (`business_id`) REFERENCES `businesses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: Add the subdomain poss.codgoo.app
-- IMPORTANT: Change business_id to match your actual business ID
-- You can find your business_id by running: SELECT id, companyName FROM businesses;

INSERT INTO `domains` (`business_id`, `domain`, `is_verified`, `is_ssl_enabled`, `status`, `created_at`, `updated_at`) 
VALUES (1, 'poss.codgoo.app', 1, 1, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE 
  `is_verified` = 1, 
  `status` = 1, 
  `updated_at` = NOW();

-- Step 3: Verify the domain was added
SELECT * FROM domains WHERE domain = 'poss.codgoo.app';

-- If you need to find your business_id first, run this:
-- SELECT id, companyName FROM businesses;
