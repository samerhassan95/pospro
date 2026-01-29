# Fix: Domain Error 400 - "This domain is not allowed"

## Problem
You're getting a 400 error when accessing `poss.codgoo.app` because:
1. The `domains` table doesn't exist on your server
2. The CheckDomain middleware is trying to query this table and failing

## Solution

You have **TWO OPTIONS**:

---

## **OPTION 1: Create the domains table (Recommended)**

### Step 1: Run this SQL in phpMyAdmin or your database tool

```sql
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
```

### Step 2: Add your subdomain to the domains table

```sql
-- Replace 1 with your actual business_id
INSERT INTO `domains` (`business_id`, `domain`, `is_verified`, `is_ssl_enabled`, `status`, `created_at`, `updated_at`) 
VALUES (1, 'poss.codgoo.app', 1, 1, 1, NOW(), NOW());
```

### Step 3: Clear cache on server

If you have SSH access:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

Or create a file `clear-cache.php` in your public folder:
```php
<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('route:clear');

echo "Cache cleared successfully!";
```

Then visit: `https://poss.codgoo.app/clear-cache.php`

---

## **OPTION 2: Temporarily disable the CustomDomainAddon module**

If you don't need the custom domain feature right now, you can disable it:

### Step 1: Edit `modules_statuses.json`

Change:
```json
{
    "CustomDomainAddon": true
}
```

To:
```json
{
    "CustomDomainAddon": false
}
```

### Step 2: Clear cache (same as Option 1, Step 3)

---

## **Which Option Should You Choose?**

- **Choose Option 1** if you want to use custom domains/subdomains feature
- **Choose Option 2** if you just want to get the site working quickly and don't need custom domains

---

## **After Fixing**

Once you've applied one of the solutions:
1. Clear your browser cache
2. Try accessing `https://poss.codgoo.app` again
3. The 400 error should be gone

---

## **Important Notes**

1. The subdomain `poss.codgoo.app` must be properly configured in your DNS/cPanel to point to your application
2. Make sure your `.env` file has the correct `APP_URL` setting
3. If you're using HTTPS, make sure SSL certificate is installed for the subdomain
