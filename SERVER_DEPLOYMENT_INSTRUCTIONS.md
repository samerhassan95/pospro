# Server Deployment Instructions

## Issue: Table 'domains' doesn't exist

The `domains` table is part of the CustomDomainAddon module and needs to be created on your server.

## Solution: Run Migrations on Server

### Step 1: Connect to your server via SSH or use cPanel Terminal

### Step 2: Navigate to your application directory
```bash
cd /path/to/your/application
```

### Step 3: Run migrations
```bash
php artisan migrate
```

This will run all pending migrations including the domains table.

### Step 4: If you need to run module-specific migrations
```bash
php artisan module:migrate CustomDomainAddon
```

### Step 5: Clear all caches
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear
```

### Step 6: Optimize for production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Alternative: Run Migration Manually via SQL

If you cannot run artisan commands on your server, you can create the table manually using this SQL:

```sql
CREATE TABLE `domains` (
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

## Verify the Table Exists

After running migrations, verify the table exists:

```sql
SHOW TABLES LIKE 'domains';
```

Or check the table structure:

```sql
DESCRIBE domains;
```

## Common Issues

### Issue: "Nothing to migrate"
This means all migrations have already been run. Check if the table exists:
```bash
php artisan tinker
>>> \Schema::hasTable('domains')
```

### Issue: Permission denied
Make sure your user has permission to run artisan commands and the database user has CREATE TABLE privileges.

### Issue: Module not found
Make sure the CustomDomainAddon module is enabled:
```bash
php artisan module:list
```

If it's disabled, enable it:
```bash
php artisan module:enable CustomDomainAddon
```

## After Migration

Once the table is created, your domain management feature should work correctly.
