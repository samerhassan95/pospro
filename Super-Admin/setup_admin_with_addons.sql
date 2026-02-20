-- Setup admin@admin.com user with full addon access as Super Admin
-- Run this SQL script to enable all addons for the admin user

-- Insert super admin user if not exists
INSERT INTO users (business_id, email, name, role, phone, image, lang, visibility, password, status, email_verified_at, created_at, updated_at)
SELECT NULL, 'admin@admin.com', 'Super Admin', 'superadmin', '1234567890', NULL, 'en', NULL, '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, NOW(), NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email = 'admin@admin.com');

-- Update existing admin@admin.com to superadmin if exists
UPDATE users 
SET role = 'superadmin', 
    name = 'Super Admin'
WHERE email = 'admin@admin.com';

-- Update all plans to enable multibranch and addon limits
UPDATE plans 
SET allow_multibranch = 1, 
    addon_domain_limit = 999, 
    subdomain_limit = 999
WHERE id IN (1, 2, 3);

-- Update plan subscriptions to enable all addon features
UPDATE plan_subscribes 
SET allow_multibranch = 1, 
    addon_domain_limit = 999, 
    subdomain_limit = 999
WHERE business_id = 1;

-- Update business expiry date to far future
UPDATE businesses 
SET will_expire = '2035-12-31'
WHERE id = 1;

SELECT 'Super Admin user setup complete! Login with admin@admin.com / admin' AS message;
