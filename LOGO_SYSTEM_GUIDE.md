# Logo System Guide

## Logo Functions Explained

Your application uses different logo functions for different areas:

### 1. **Main Header Logo** - `get_main_header_logo()`
**Used in:**
- Home page header (desktop view)
- Mobile menu header

**Where to change:** Admin Settings → Website Settings → Main Header Logo

### 2. **Common Header Logo** - `get_common_header_logo()`
**Used in:**
- Common header pages (About, Pricing, Blog, Contact, etc.)
- Desktop and mobile views
- Signup modal

**Where to change:** Admin Settings → Website Settings → Common Header Logo

### 3. **Login Page Logo** - `get_login_page_logo()`
**Used in:**
- Login page
- Forgot password page
- Reset password page

**Where to change:** Admin Settings → Website Settings → Login Page Logo

### 4. **Admin Logo** - `get_admin_logo()`
**Used in:**
- Admin dashboard sidebar
- Admin panel header

**Where to change:** Admin Settings → Website Settings → Admin Logo

## What Was Fixed

### Issue
When you changed "Main Header Logo" or "Common Header Logo" in settings, the homepage header didn't update because it was using the wrong logo function (`get_login_page_logo()` instead of `get_main_header_logo()`).

### Solution
Updated the following files to use the correct logo functions:

1. **resources/views/layouts/web/partials/header.blade.php**
   - Changed from `get_login_page_logo()` to `get_main_header_logo()`

2. **resources/views/layouts/web/partials/common_header.blade.php**
   - Changed from `get_login_page_logo()` to `get_common_header_logo()`

## Logo Mapping

| Page/Section | Logo Function | Settings Field |
|-------------|---------------|----------------|
| Home page header | `get_main_header_logo()` | Main Header Logo |
| About/Pricing/Blog/Contact headers | `get_common_header_logo()` | Common Header Logo |
| Login/Forgot Password/Reset | `get_login_page_logo()` | Login Page Logo |
| Admin Dashboard | `get_admin_logo()` | Admin Logo |
| Signup Modal | `get_common_header_logo()` | Common Header Logo |

## Testing Your Changes

After uploading a new logo:

1. **Clear browser cache** (Ctrl+Shift+R or Cmd+Shift+R)
2. **Check these pages:**
   - Home page (should show Main Header Logo)
   - About/Pricing pages (should show Common Header Logo)
   - Login page (should show Login Page Logo)
   - Admin dashboard (should show Admin Logo)

## Troubleshooting

### Logo not updating after upload?
1. Clear browser cache
2. Check if the file was uploaded successfully in the settings
3. Verify the correct logo function is being used in the template

### Different logos showing on different pages?
This is by design! Each section can have its own logo:
- **Home page** = Main Header Logo
- **Other pages** = Common Header Logo
- **Login** = Login Page Logo
- **Admin** = Admin Logo

If you want the same logo everywhere, upload the same image to all logo fields in settings.
