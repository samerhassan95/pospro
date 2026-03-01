-- ============================================
-- Database Changes for Production Server
-- For phpMyAdmin - Simple Version
-- Date: March 1, 2026
-- ============================================

-- IMPORTANT: Make a backup before running this script!

-- ============================================
-- 1. Add Plan Permissions
-- ============================================

-- Check if columns exist before adding
ALTER TABLE `plans` 
ADD COLUMN `allow_purchases` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_multibranch`;

ALTER TABLE `plans` 
ADD COLUMN `allow_products` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_purchases`;

ALTER TABLE `plans` 
ADD COLUMN `allow_warehouses` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_products`;

ALTER TABLE `plans` 
ADD COLUMN `warehouse_limit` INT NULL AFTER `allow_warehouses`;

ALTER TABLE `plans` 
ADD COLUMN `branch_limit` INT NULL AFTER `warehouse_limit`;

ALTER TABLE `plans` 
ADD COLUMN `allow_stock` TINYINT(1) NOT NULL DEFAULT 1 AFTER `branch_limit`;

ALTER TABLE `plans` 
ADD COLUMN `allow_customers` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_stock`;

ALTER TABLE `plans` 
ADD COLUMN `allow_suppliers` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_customers`;

ALTER TABLE `plans` 
ADD COLUMN `allow_vat_settings` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_suppliers`;

ALTER TABLE `plans` 
ADD COLUMN `allow_due_list` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_vat_settings`;

ALTER TABLE `plans` 
ADD COLUMN `allow_finance` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_due_list`;

ALTER TABLE `plans` 
ADD COLUMN `allow_commission` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_finance`;

ALTER TABLE `plans` 
ADD COLUMN `allow_hrm` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_commission`;

ALTER TABLE `plans` 
ADD COLUMN `allow_reports` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_hrm`;

ALTER TABLE `plans` 
ADD COLUMN `allow_pos_app` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_reports`;

ALTER TABLE `plans` 
ADD COLUMN `allow_store` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_pos_app`;

ALTER TABLE `plans` 
ADD COLUMN `allow_sales` TINYINT(1) NOT NULL DEFAULT 1 AFTER `allow_store`;

-- ============================================
-- 2. Add SSO Fields to Users Table
-- ============================================

ALTER TABLE `users` 
ADD COLUMN `external_id` VARCHAR(255) NULL AFTER `id` COMMENT 'User ID from master app';

ALTER TABLE `users` 
ADD COLUMN `sso_provider` VARCHAR(255) NULL DEFAULT 'nomuapps' AFTER `external_id`;

ALTER TABLE `users` 
ADD COLUMN `last_sso_login` TIMESTAMP NULL AFTER `sso_provider`;

-- Add index for SSO lookup
ALTER TABLE `users` 
ADD INDEX `idx_sso_lookup` (`external_id`, `sso_provider`);

-- ============================================
-- 3. Add Branch Support to HRM Tables
-- (Only run if tables exist - check first!)
-- ============================================

-- For holidays table (run only if table exists)
-- ALTER TABLE `holidays` 
-- ADD COLUMN `branch_id` BIGINT UNSIGNED NULL AFTER `business_id`,
-- ADD CONSTRAINT `holidays_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

-- For attendances table (run only if table exists)
-- ALTER TABLE `attendances` 
-- ADD COLUMN `branch_id` BIGINT UNSIGNED NULL AFTER `business_id`,
-- ADD CONSTRAINT `attendances_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

-- For leaves table (run only if table exists)
-- ALTER TABLE `leaves` 
-- ADD COLUMN `branch_id` BIGINT UNSIGNED NULL AFTER `business_id`,
-- ADD CONSTRAINT `leaves_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

-- For payrolls table (run only if table exists)
-- ALTER TABLE `payrolls` 
-- ADD COLUMN `branch_id` BIGINT UNSIGNED NULL AFTER `business_id`,
-- ADD CONSTRAINT `payrolls_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

-- For employees table (run only if table exists)
-- ALTER TABLE `employees` 
-- ADD COLUMN `branch_id` BIGINT UNSIGNED NULL AFTER `business_id`,
-- ADD CONSTRAINT `employees_branch_id_foreign` FOREIGN KEY (`branch_id`) REFERENCES `branches` (`id`) ON DELETE SET NULL;

-- ============================================
-- 4. Register Migrations
-- ============================================

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('2026_02_26_000000_add_permissions_to_plans_table', (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp)),
('2026_02_27_000000_add_sso_fields_to_users_table', (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp)),
('2026_02_28_000001_add_branch_id_to_hrm_tables', (SELECT IFNULL(MAX(batch), 0) + 1 FROM (SELECT batch FROM migrations) AS temp));

-- ============================================
-- Verification Queries
-- ============================================

-- Check users table
SELECT 'Checking users table...' AS status;
SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'users'
AND COLUMN_NAME IN ('external_id', 'sso_provider', 'last_sso_login');

-- Check plans table
SELECT 'Checking plans table...' AS status;
SELECT COUNT(*) as permission_count
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'plans'
AND COLUMN_NAME LIKE 'allow_%';
-- Expected: 17

-- Check migrations
SELECT 'Checking migrations...' AS status;
SELECT migration, batch
FROM migrations
WHERE migration IN (
    '2026_02_26_000000_add_permissions_to_plans_table',
    '2026_02_27_000000_add_sso_fields_to_users_table',
    '2026_02_28_000001_add_branch_id_to_hrm_tables'
)
ORDER BY id DESC;

SELECT 'Database changes applied successfully!' AS status;
