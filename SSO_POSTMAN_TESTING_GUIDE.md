# SSO Testing Guide with Postman

## Prerequisites
1. Postman installed
2. SSO enabled in `.env`: `SSO_ENABLED=true`
3. Secret key: `6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca`

---

## Step 1: Generate SSO Token

### Using PHP Script

Create a file `generate_sso_token.php`:

```php
<?php

$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';

// Test data - modify as needed
$data = [
    'user_id' => 'POSTMAN_TEST_' . time(),
    'name' => 'Test User from Postman',
    'email' => 'postman_test_' . time() . '@example.com',
    'plan_id' => 1, // Change to 1, 2, or 3
    'business_name' => 'Postman Test Business',
    'phone' => '0501234567',
    'locale' => 'ar',
    'timestamp' => time(),
];

// Encrypt
$json = json_encode($data);
$iv = substr(hash('sha256', $secret), 0, 16);
$encrypted = openssl_encrypt($json, 'AES-256-CBC', $secret, 0, $iv);
$signature = hash_hmac('sha256', $encrypted, $secret);
$token = base64_encode($encrypted . '::' . $signature);

echo "Token Generated Successfully!\n\n";
echo "Token:\n";
echo $token . "\n\n";
echo "URL:\n";
echo "http://127.0.0.1:8000/sso/login?token=" . urlencode($token) . "\n\n";
echo "User Data:\n";
print_r($data);
```

Run it:
```bash
php generate_sso_token.php
```

---

## Step 2: Test SSO Login with Postman

### Method 1: GET Request (Browser-like)

**Request:**
- **Method:** GET
- **URL:** `http://127.0.0.1:8000/sso/login?token=YOUR_TOKEN_HERE`
- **Headers:** None required

**Expected Response:**
- **Success:** Redirect to `/business/dashboard` with session cookie
- **Status:** 302 (Redirect)

**To see the response in Postman:**
1. Disable "Automatically follow redirects" in Postman settings
2. Check the `Location` header in response

---

### Method 2: POST Request (API-like)

**Request:**
- **Method:** POST
- **URL:** `http://127.0.0.1:8000/sso/login`
- **Headers:**
  - `Content-Type: application/json`
  - `Accept: application/json`
- **Body (JSON):**
```json
{
    "token": "YOUR_TOKEN_HERE"
}
```

**Expected Response (Success):**
```json
{
    "message": "SSO login successful",
    "redirect": "http://127.0.0.1:8000/business/dashboard",
    "user": {
        "id": 24,
        "name": "Test User from Postman",
        "email": "postman_test_123@example.com",
        "role": "shop-owner",
        "business_id": 27
    }
}
```

**Expected Response (Error):**
```json
{
    "message": "Invalid or expired SSO token"
}
```

---

## Step 3: Test Different Scenarios

### Scenario 1: Create User with Plan (New Business)

**Token Data:**
```php
$data = [
    'user_id' => 'USER_001',
    'name' => 'محمد أحمد',
    'email' => 'mohammed@test.com',
    'plan_id' => 2, // Plan B
    'business_name' => 'متجر محمد',
    'phone' => '0501234567',
    'vat_no' => '300123456789003',
    'commercial_registration' => '1234567890',
    'locale' => 'ar',
    'timestamp' => time(),
];
```

**Expected Result:**
- ✅ User created
- ✅ Business created
- ✅ Subscription created with Plan B
- ✅ User logged in

---

### Scenario 2: Create User for Existing Business

**Token Data:**
```php
$data = [
    'user_id' => 'USER_002',
    'name' => 'أحمد علي',
    'email' => 'ahmed@test.com',
    'business_id' => 5, // Existing business ID
    'timestamp' => time(),
];
```

**Expected Result:**
- ✅ User created
- ✅ Linked to existing business
- ✅ User logged in

---

### Scenario 3: Create Admin User

**Token Data:**
```php
$data = [
    'user_id' => 'ADMIN_001',
    'name' => 'Admin User',
    'email' => 'admin@test.com',
    'role' => 'admin',
    'timestamp' => time(),
];
```

**Expected Result:**
- ✅ User created as admin
- ❌ No business created
- ✅ User logged in

---

## Step 4: Verify Results

### Check User Created
```sql
SELECT * FROM users WHERE external_id = 'USER_001';
```

### Check Business Created
```sql
SELECT * FROM businesses WHERE id = (SELECT business_id FROM users WHERE external_id = 'USER_001');
```

### Check Subscription Created
```sql
SELECT ps.*, p.subscriptionName 
FROM plan_subscribes ps
JOIN plans p ON ps.plan_id = p.id
WHERE ps.business_id = (SELECT business_id FROM users WHERE external_id = 'USER_001');
```

### Check Complete Link
```sql
SELECT 
    u.name as user_name,
    u.email,
    u.role,
    b.companyName as business_name,
    p.subscriptionName as plan_name,
    ps.service_end_date
FROM users u
LEFT JOIN businesses b ON u.business_id = b.id
LEFT JOIN plan_subscribes ps ON b.plan_subscribe_id = ps.id
LEFT JOIN plans p ON ps.plan_id = p.id
WHERE u.external_id = 'USER_001';
```

---

## Step 5: Test SSO Logout

**Request:**
- **Method:** GET
- **URL:** `http://127.0.0.1:8000/sso/logout`
- **Headers:** 
  - `Cookie: laravel_session=YOUR_SESSION_COOKIE`

**Expected Response:**
- **Success:** Redirect to home page
- **Status:** 302

---

## Postman Collection

### Import this JSON into Postman:

```json
{
    "info": {
        "name": "SSO Testing",
        "schema": "https://schema.getpostman.com/json/collection/v2.1.0/collection.json"
    },
    "item": [
        {
            "name": "SSO Login - GET",
            "request": {
                "method": "GET",
                "header": [],
                "url": {
                    "raw": "http://127.0.0.1:8000/sso/login?token={{sso_token}}",
                    "protocol": "http",
                    "host": ["127", "0", "0", "1"],
                    "port": "8000",
                    "path": ["sso", "login"],
                    "query": [
                        {
                            "key": "token",
                            "value": "{{sso_token}}"
                        }
                    ]
                }
            }
        },
        {
            "name": "SSO Login - POST",
            "request": {
                "method": "POST",
                "header": [
                    {
                        "key": "Content-Type",
                        "value": "application/json"
                    },
                    {
                        "key": "Accept",
                        "value": "application/json"
                    }
                ],
                "body": {
                    "mode": "raw",
                    "raw": "{\n    \"token\": \"{{sso_token}}\"\n}"
                },
                "url": {
                    "raw": "http://127.0.0.1:8000/sso/login",
                    "protocol": "http",
                    "host": ["127", "0", "0", "1"],
                    "port": "8000",
                    "path": ["sso", "login"]
                }
            }
        },
        {
            "name": "SSO Logout",
            "request": {
                "method": "GET",
                "header": [],
                "url": {
                    "raw": "http://127.0.0.1:8000/sso/logout",
                    "protocol": "http",
                    "host": ["127", "0", "0", "1"],
                    "port": "8000",
                    "path": ["sso", "logout"]
                }
            }
        }
    ],
    "variable": [
        {
            "key": "sso_token",
            "value": "PASTE_YOUR_TOKEN_HERE"
        }
    ]
}
```

---

## Troubleshooting

### Error: "SSO is not enabled"
**Solution:**
```bash
# Check .env
SSO_ENABLED=true

# Clear cache
php artisan config:clear
```

### Error: "Invalid or expired SSO token"
**Possible causes:**
1. Wrong secret key
2. Token expired (if expiry > 0)
3. Token format incorrect

**Solution:**
- Regenerate token with correct secret
- Check `SSO_SECRET_KEY` in `.env`

### Error: "Invalid plan_id"
**Solution:**
- Check available plans:
```sql
SELECT id, subscriptionName FROM plans WHERE status = 1;
```
- Use valid plan_id (1, 2, or 3)

### Error: "Email already exists"
**Solution:**
- Use different email
- Or SSO will link existing user

---

## Quick Test Commands

### Generate Token (One-liner)
```bash
php -r "
\$secret = '6856fe49d4f8bc0830199edaefe41fa7922d0a323b07ea216dfbc96ce8257cca';
\$data = json_encode(['user_id' => 'TEST_'.time(), 'name' => 'Test', 'email' => 'test'.time().'@test.com', 'plan_id' => 1, 'timestamp' => time()]);
\$iv = substr(hash('sha256', \$secret), 0, 16);
\$encrypted = openssl_encrypt(\$data, 'AES-256-CBC', \$secret, 0, \$iv);
\$signature = hash_hmac('sha256', \$encrypted, \$secret);
echo base64_encode(\$encrypted . '::' . \$signature);
"
```

### Test with cURL
```bash
# Replace TOKEN with your generated token
curl -X GET "http://127.0.0.1:8000/sso/login?token=TOKEN" -L
```

---

## Notes

1. **Token Expiry:** Currently set to 0 (no expiry)
2. **Auto Registration:** Enabled by default
3. **Session:** SSO creates a Laravel session
4. **Cookies:** Session cookie is set automatically
5. **Redirect:** After login, redirects to `/business/dashboard`

---

**Last Updated:** 2026-02-28  
**Version:** 1.0.0
