-- B2B Invoice Enhancement Fields
-- Run this SQL if you cannot run migrations on your server
-- Date: 2026-01-29

-- Add fields to businesses table
ALTER TABLE `businesses` 
ADD COLUMN `commercial_registration` VARCHAR(50) NULL AFTER `vat_no`,
ADD COLUMN `additional_id` VARCHAR(50) NULL AFTER `commercial_registration`,
ADD COLUMN `bank_account_number` VARCHAR(50) NULL AFTER `additional_id`,
ADD COLUMN `bank_name` VARCHAR(100) NULL AFTER `bank_account_number`;

-- Add fields to parties table
ALTER TABLE `parties` 
ADD COLUMN `commercial_registration` VARCHAR(50) NULL AFTER `vat_number`,
ADD COLUMN `additional_id` VARCHAR(50) NULL AFTER `commercial_registration`;

-- Add fields to sales table
ALTER TABLE `sales` 
ADD COLUMN `supply_date` DATE NULL AFTER `saleDate`,
ADD COLUMN `po_number` VARCHAR(50) NULL AFTER `supply_date`,
ADD COLUMN `contract_number` VARCHAR(50) NULL AFTER `po_number`,
ADD COLUMN `payment_terms` VARCHAR(100) NULL AFTER `contract_number`,
ADD COLUMN `payment_means` VARCHAR(50) NULL AFTER `payment_terms`,
ADD COLUMN `shipping_address_line1` VARCHAR(255) NULL AFTER `payment_means`,
ADD COLUMN `shipping_address_line2` VARCHAR(255) NULL AFTER `shipping_address_line1`,
ADD COLUMN `shipping_city` VARCHAR(100) NULL AFTER `shipping_address_line2`,
ADD COLUMN `shipping_postal_code` VARCHAR(20) NULL AFTER `shipping_city`;

-- Add fields to sale_details table
ALTER TABLE `sale_details` 
ADD COLUMN `item_code` VARCHAR(50) NULL AFTER `product_id`,
ADD COLUMN `unit_of_measure` VARCHAR(20) NULL AFTER `item_code`,
ADD COLUMN `list_price` DECIMAL(10,2) NULL AFTER `price`,
ADD COLUMN `discount_percent` DECIMAL(5,2) NULL AFTER `list_price`,
ADD COLUMN `net_price` DECIMAL(10,2) NULL AFTER `discount_percent`,
ADD COLUMN `tax_per_item` DECIMAL(10,2) NULL AFTER `net_price`,
ADD COLUMN `tax_exemption_reason` VARCHAR(255) NULL AFTER `tax_per_item`;

-- Add fields to plan_subscribes table
ALTER TABLE `plan_subscribes` 
ADD COLUMN `service_code` VARCHAR(50) NULL AFTER `plan_id`,
ADD COLUMN `service_start_date` DATE NULL AFTER `service_code`,
ADD COLUMN `service_end_date` DATE NULL AFTER `service_start_date`,
ADD COLUMN `tax_period_start` DATE NULL AFTER `service_end_date`,
ADD COLUMN `tax_period_end` DATE NULL AFTER `tax_period_start`,
ADD COLUMN `po_number` VARCHAR(50) NULL AFTER `tax_period_end`,
ADD COLUMN `contract_number` VARCHAR(50) NULL AFTER `po_number`,
ADD COLUMN `payment_terms` VARCHAR(100) NULL AFTER `contract_number`,
ADD COLUMN `payment_means` VARCHAR(50) NULL AFTER `payment_terms`;

-- Verify the changes
SELECT 'businesses table updated' AS status;
SELECT 'parties table updated' AS status;
SELECT 'sales table updated' AS status;
SELECT 'sale_details table updated' AS status;
SELECT 'plan_subscribes table updated' AS status;

-- Check if columns were added successfully
SHOW COLUMNS FROM businesses LIKE '%commercial%';
SHOW COLUMNS FROM parties LIKE '%commercial%';
SHOW COLUMNS FROM sales LIKE '%supply_date%';
SHOW COLUMNS FROM sale_details LIKE '%item_code%';
SHOW COLUMNS FROM plan_subscribes LIKE '%service_code%';
