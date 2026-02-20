# Admin User with All Addons Setup

This guide explains how to set up the admin@admin.com user with full access to all addons.

## What Has Been Configured

### 1. Modules Status (modules_statuses.json)
All available addons have been enabled globally:
- ✅ Business
- ✅ WarehouseAddon
- ✅ HrmAddon
- ✅ MultiBranchAddon
- ✅ CustomDomainAddon
- ✅ SocialLoginAddon
- ✅ ThermalPrinterAddon
- ✅ AffiliateAddon
- ✅ MarketingAddon
- ✅ BkashAddon
- ✅ CinetpayAddon
- ✅ FedapayAddon
- ✅ PawapayAddon
- ✅ SerialCodeAddon
- ✅ CustomReportsAddon

### 2. Database Seeders Updated
- **UserSeeder**: Added admin@admin.com user
- **PlanSeeder**: Enabled all addon features in all plans
- **PlanSubscribeSeeder**: Set unlimited addon limits

### 3. Purchase Code Verification
Modified `AddonController.php` to accept any purchase code for addon installation.

## Setup Methods

### Method 1: Using Artisan Command (Recommended)
```bash
php artisan admin:setup-addons
```

This command will:
- Create/update admin@admin.com user
- Enable all addon features in plans
- Update plan subscriptions with unlimited addon access
- Extend business expiry dates

### Method 2: Using SQL Script
```bash
# For MySQL
mysql -u your_username -p your_database < setup_admin_with_addons.sql

# Or import via phpMyAdmin
```

### Method 3: Fresh Installation
If you're doing a fresh installation, simply run:
```bash
php artisan migrate:fresh --seed
```

The seeders have been updated to include the admin user with full addon access.

## Login Credentials

**Admin User:**
- Email: `admin@admin.com`
- Password: `admin`
- Role: Admin (full system access)

**Shop Owner (existing):**
- Email: `shopowner@acnoo.com`
- Password: `123456`
- Role: Shop Owner

## Installing Addon Modules

1. Go to Admin Panel → Addons
2. Upload any addon ZIP file
3. Enter ANY purchase code (verification is bypassed)
4. The addon will be installed automatically

## Addon Features Enabled

All plans now include:
- ✅ Multi-branch support
- ✅ Unlimited addon domains (999)
- ✅ Unlimited subdomains (999)
- ✅ Extended expiry (until 2035)

## Verification

After setup, verify by:
1. Login as admin@admin.com
2. Check that all modules are visible in the sidebar
3. Try accessing addon features like:
   - Warehouses
   - HRM
   - Multi-branch
   - Custom domains

## Troubleshooting

If addons don't appear:
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Notes

- The admin user has system-wide access (not tied to a specific business)
- All addons are enabled globally via modules_statuses.json
- Purchase code verification is bypassed for easy addon installation
- Business expiry dates are set to 2035 for long-term access
