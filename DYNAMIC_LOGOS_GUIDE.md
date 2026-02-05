# Dynamic Logo System Implementation

## Overview
The login page and admin dashboard now use dynamic logos that can be configured from the admin panel instead of static images.

## What Was Changed

### 1. Helper Functions Added
New helper functions in `app/Helpers/Helper.php`:
- `get_admin_logo()` - Returns admin logo with fallback
- `get_login_page_logo()` - Returns login page logo with fallback  
- `get_login_page_image()` - Returns login page image with fallback
- `get_main_header_logo()` - Returns main header logo with fallback
- `get_common_header_logo()` - Returns common header logo with fallback
- `get_footer_logo()` - Returns footer logo with fallback
- `get_system_title()` - Returns system title with fallback

### 2. Updated Templates
**Authentication Pages:**
- `resources/views/auth/login.blade.php` - Uses **Login Page Logo** and **dynamic title**
- `resources/views/auth/forgot-password.blade.php` - Uses **Login Page Logo** and **dynamic title**
- `resources/views/auth/reset-password.blade.php` - Uses **Login Page Logo** and **dynamic title**

**Admin Layout:**
- `resources/views/layouts/partials/side-bar.blade.php` - Uses admin logo and **dynamic title**
- `resources/views/layouts/partials/header.blade.php` - Uses admin logo and **dynamic title**
- `resources/views/layouts/business/partials/header.blade.php` - Uses admin logo and **dynamic title**

**Web Layout:**
- `resources/views/layouts/web/partials/common_header.blade.php` - Uses **Main Header Logo** and **dynamic title**
- `resources/views/layouts/web/partials/header.blade.php` - Uses **Main Header Logo** and **dynamic title**
- `resources/views/layouts/web/partials/footer.blade.php` - Uses **Footer Logo** and **dynamic title**

### 3. Admin Settings
The admin can configure these logos from:
**Admin Panel → Settings → General Settings**

Available logo settings:
- **Main Header Logo** - Used in website headers (main logo field)
- **Common Header Logo** - Alternative header logo option
- **Footer Logo** - Used in website footer
- **Admin Logo** - Used in admin dashboard only
- **Login Page Logo** - Used in login page header and auth pages
- **Login Page Image** - Main image displayed on login page
- **System Title** - Dynamic title used throughout the system instead of "Bytes Pos"

## Logo Usage Mapping

| Location | Logo Used | Setting Field |
|----------|-----------|---------------|
| Website Header | Main Header Logo | `logo` |
| Website Footer | Footer Logo | `footer_logo` |
| Admin Dashboard | Admin Logo | `admin_logo` |
| **Login Page Header** | **Login Page Logo** | `login_page_logo` |
| Login Page Image | Login Page Image | `login_page_image` |
| **System Title** | **Dynamic Title** | `title` |

## How It Works

1. **Settings Storage**: Logos are stored in the `options` table with key `general`
2. **Caching**: Settings are cached for performance using `get_option()` function
3. **Fallbacks**: If no custom logo is set, system falls back to default static images
4. **File Upload**: Admin can upload new logos through the settings form

## Benefits

✅ **Dynamic Configuration** - No need to replace files manually
✅ **Admin Control** - Easy to change from admin panel
✅ **Fallback System** - Always shows a logo even if none uploaded
✅ **Performance** - Settings are cached for fast loading
✅ **Consistent** - Same logo system across all pages
✅ **Proper Separation** - Different logos for different contexts

## Usage for Admins

1. Go to **Admin Panel → Settings → General Settings**
2. Upload desired logos in the respective fields:
   - **Main Header Logo** - Primary logo for website headers
   - **Footer Logo** - Logo for website footer
   - **Admin Logo** - Logo for admin dashboard
   - **Login Page Logo** - Logo for login page header
   - **Login Page Image** - Main image for login page
3. Click **Update** to save changes
4. Changes will be reflected immediately across the site

## Technical Notes

- Images are uploaded to `public/uploads/` directory
- Supported formats: JPG, PNG, SVG, GIF
- Original filenames are preserved with timestamp prefix
- Old images are automatically replaced when new ones are uploaded
- Cache is cleared automatically when settings are updated
- **Main Header Logo** uses the primary `logo` field from settings
- **Footer Logo** uses the dedicated `footer_logo` field from settings