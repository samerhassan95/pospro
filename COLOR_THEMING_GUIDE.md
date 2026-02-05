# Color Theming Guide

## How to Change the Primary Color

To change the primary color throughout the entire application, simply update the `--clr-primary` variable in any of these files:

### CSS Files with Primary Color Variable:
1. `public/assets/css/style.css` (line 13)
2. `public/assets/css/custom.css` (line 2)
3. `public/assets/css/arabic.css` (line 5)
4. `public/assets/web/css/styles.css` (line 7)

### Example:
```css
:root {
    --clr-primary: #011646; /* Change this value */
}
```

## What Was Updated

### 1. All Hardcoded Colors Replaced
- Replaced 80+ instances of `#FF6500` with `var(--clr-primary)`
- Updated in CSS files, Blade templates, and inline styles

### 2. SVG Icons Updated
- Changed `fill="#FF6500"` to `fill="currentColor"` in SVG files:
  - `public/assets/images/dashboard/main2.svg`
  - `public/assets/images/logo/favicon.svg`
  - `public/assets/images/logo/LOGO.svg`

### 3. Sidebar Icons Fixed
- Removed hardcoded CSS filters that were forcing orange color
- Icons now use CSS variable through SVG `fill` and `stroke` properties
- Updated in both LTR and RTL styles

### 4. Files Modified
**CSS Files:**
- `public/assets/css/style.css`
- `public/assets/css/pos-products.css`
- `public/assets/css/payments.css`
- `public/assets/css/arabic.css`
- `public/assets/css/custom.css`
- `public/assets/web/css/styles.css`

**Blade Templates:**
- `resources/views/layouts/business/partials/header.blade.php`
- `resources/views/web/components/signup.blade.php`
- `Modules/Business/resources/views/sales/create.blade.php`
- `Modules/Business/resources/views/purchases/create.blade.php`

**SVG Files:**
- `public/assets/images/dashboard/main2.svg`
- `public/assets/images/logo/favicon.svg`
- `public/assets/images/logo/LOGO.svg`

**Other:**
- `test-sidebar.html`

## Testing Your Color Change

After changing `--clr-primary` to `#011646` (or any color), you should see the new color in:

✅ Buttons and links
✅ Sidebar active states
✅ Sidebar icons (SVG paths)
✅ Form focus states
✅ Tabs and navigation
✅ Badges and labels
✅ Hover effects
✅ Logo SVGs (using currentColor)

## Troubleshooting

### If sidebar icons don't change color:
1. Clear browser cache (Ctrl+Shift+R or Cmd+Shift+R)
2. Check that CSS filters have been removed from `.sidebar-icon img`
3. Verify SVG paths use `fill` or `stroke` properties (not hardcoded colors)

### If some elements still show orange:
1. Search for remaining `#FF6500` in your codebase
2. Check for inline styles in HTML/Blade files
3. Look for JavaScript that might be setting colors dynamically

## Browser Cache
After making changes, always hard refresh your browser:
- **Chrome/Edge:** Ctrl+Shift+R (Windows) or Cmd+Shift+R (Mac)
- **Firefox:** Ctrl+F5 (Windows) or Cmd+Shift+R (Mac)
