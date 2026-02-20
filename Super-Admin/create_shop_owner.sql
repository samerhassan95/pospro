-- Create Shop Owner with Full Addon Access
-- Replace the email and password with your desired values

-- Step 1: Create Business Category if not exists
INSERT INTO business_categories (name, status, created_at, updated_at)
SELECT 'General', 1, NOW(), NOW()
WHERE NOT EXISTS (SELECT 1 FROM business_categories WHERE id = 1);

-- Step 2: Create or Update Plan with full addon access
INSERT INTO plans (id, subscriptionName, duration, subscriptionPrice, status, allow_multibranch, addon_domain_limit, subdomain_limit, features, created_at, updated_at)
VALUES (1, 'Premium', 365, 0, 1, 1, 999, 999, '{}', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    allow_multibranch = 1,
    addon_domain_limit = 999,
    subdomain_limit = 999;

-- Step 3: Create Business
-- Change the values below as needed
SET @business_name = 'My Shop';
SET @business_email = 'shop@example.com';
SET @business_phone = '1234567890';

INSERT INTO businesses (business_category_id, companyName, will_expire, address, email, phoneNumber, subscriptionDate, remainingShopBalance, shopOpeningBalance, status, meta, created_at, updated_at)
VALUES (
    1,
    @business_name,
    '2035-12-31',
    'Business Address',
    @business_email,
    @business_phone,
    NOW(),
    0,
    0,
    1,
    '{"show_company_name":1,"show_phone_number":1,"show_address":1,"show_email":1,"show_vat_title":1,"show_vat_no":1}',
    NOW(),
    NOW()
);

SET @business_id = LAST_INSERT_ID();

-- Step 4: Create Plan Subscription
INSERT INTO plan_subscribes (plan_id, business_id, price, payment_status, duration, allow_multibranch, addon_domain_limit, subdomain_limit, created_at, updated_at)
VALUES (1, @business_id, 0, 'paid', 365, 1, 999, 999, NOW(), NOW());

SET @plan_subscribe_id = LAST_INSERT_ID();

-- Step 5: Update Business with Plan Subscription
UPDATE businesses SET plan_subscribe_id = @plan_subscribe_id WHERE id = @business_id;

-- Step 6: Create Shop Owner User
-- Change email, name, and password below
SET @user_email = 'shopowner@example.com';
SET @user_name = 'Shop Owner';
-- Password: 123456 (change the bcrypt hash if you want different password)
SET @user_password = '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';

INSERT INTO users (business_id, name, email, role, phone, lang, password, status, email_verified_at, created_at, updated_at)
VALUES (
    @business_id,
    @user_name,
    @user_email,
    'shop-owner',
    @business_phone,
    'en',
    @user_password,
    1,
    NOW(),
    NOW(),
    NOW()
);

-- Display results
SELECT 
    'Shop Owner Created Successfully!' as message,
    @user_email as email,
    '123456' as password,
    @business_name as business,
    'All addons enabled' as addons,
    '2035-12-31' as expiry;
