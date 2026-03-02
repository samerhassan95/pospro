# Cleanup Unused Files Guide

## Overview
This guide helps you remove unused documentation, test files, and backup files from your project to keep it clean and production-ready.

## What Will Be Removed

### 1. Documentation Files (150+ files)
- All `.md` files created during development
- Implementation guides
- Testing checklists
- Status updates
- Fix summaries

### 2. Test Files (50+ files)
- PHP test scripts (`test_*.php`, `check_*.php`)
- HTML test pages (`test-*.html`)
- Diagnostic scripts

### 3. Backup Files
- `lang/ar.json.backup`
- `public/assets/plugins/custom/business-dashboard.js.backup`

### 4. SQL Scripts (Development only)
- Migration scripts
- Update scripts
- Fix scripts

### 5. Other Temporary Files
- `category-fix.txt`
- `missings`
- `scroll-fix.css`
- `uploads.zip`
- `lang.rar`

## What Will Be KEPT

✅ **Essential Files:**
- `README.md` - Main project documentation
- `.env` and `.env.example` - Configuration files
- All application code in `app/`, `resources/`, `public/`
- All vendor dependencies
- Database migrations in `database/migrations/`
- Routes, controllers, models, views
- Assets (CSS, JS, images)

## How to Clean Up

### Option 1: Using PHP Script (Recommended)

```bash
# Run the cleanup script
php cleanup_unused_files.php

# Follow the prompts:
# 1. View list of files (optional)
# 2. Confirm deletion by typing "yes"
```

### Option 2: Using Windows Batch File

```cmd
# Double-click or run:
cleanup_unused_files.bat

# Press any key to continue
# Files will be deleted automatically
```

### Option 3: Manual Cleanup

If you prefer to review and delete manually:

1. **Documentation files:**
   ```bash
   # List all .md files except README.md
   dir *.md /b | findstr /v "README.md"
   ```

2. **Test files:**
   ```bash
   # List all test PHP files
   dir test_*.php check_*.php /b
   ```

3. **Backup files:**
   ```bash
   # Find backup files
   dir /s *.backup
   ```

## Safety Measures

### Before Running Cleanup:

1. **Backup your project:**
   ```bash
   # Create a backup
   git add .
   git commit -m "Before cleanup"
   ```

2. **Review the file list:**
   - Run the PHP script and choose to view the list first
   - Make sure no important files are included

3. **Test in development first:**
   - Don't run on production server directly
   - Test on a copy of your project first

### After Cleanup:

1. **Verify application works:**
   ```bash
   # Clear cache
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   
   # Test the application
   php artisan serve
   ```

2. **Check for missing files:**
   - Browse through your application
   - Test all major features
   - Check for any 404 errors

## Disk Space Savings

Expected space savings:
- Documentation files: ~5-10 MB
- Test files: ~2-5 MB
- Backup files: ~1-2 MB
- SQL scripts: ~1 MB
- Other files: ~5-10 MB

**Total: ~15-30 MB**

## Rollback

If you need to restore files:

### Using Git:
```bash
# Restore all deleted files
git checkout HEAD -- .

# Restore specific file
git checkout HEAD -- filename.md
```

### Using Backup:
```bash
# If you created a backup before cleanup
# Copy files from backup location
```

## Production Deployment

For production servers, it's recommended to:

1. **Keep only essential files:**
   - Remove all test files
   - Remove all documentation except README.md
   - Remove all backup files

2. **Use .gitignore:**
   ```
   # Add to .gitignore
   *.backup
   test_*.php
   check_*.php
   *_GUIDE_AR.md
   *_SUMMARY_AR.md
   ```

3. **Deploy clean code:**
   ```bash
   # Only deploy tracked files
   git archive --format=zip HEAD -o deploy.zip
   ```

## Files to Keep for Reference

If you want to keep some documentation for future reference:

### Recommended to Keep:
- `README.md` - Main documentation
- `DEPLOYMENT_GUIDE.md` - If you have deployment instructions
- `TESTING_GUIDE.md` - If you need testing procedures

### Move to Separate Folder:
```bash
# Create a docs archive folder
mkdir docs_archive
move *_AR.md docs_archive\
move *_GUIDE.md docs_archive\
```

## Troubleshooting

### Script Won't Run:
```bash
# Make sure PHP is in your PATH
php -v

# Run with full path
C:\xampp\php\php.exe cleanup_unused_files.php
```

### Permission Errors:
```bash
# Run as administrator (Windows)
# Right-click > Run as administrator

# Or use PowerShell
powershell -Command "Start-Process cmd -Verb RunAs"
```

### Files Still Exist:
```bash
# Check if files are in use
# Close all editors and IDEs
# Try again
```

## Summary

This cleanup process will:
- ✅ Remove 200+ unused files
- ✅ Free up 15-30 MB of disk space
- ✅ Make your project cleaner
- ✅ Prepare for production deployment
- ✅ Keep all essential application files

**Remember:** Always backup before cleanup!
