-- Reset all logo paths to default values
-- This will fix the issue of missing logos and images

UPDATE `options` 
SET `value` = JSON_SET(
    `value`,
    '$.logo', 'assets/images/Logo.png',
    '$.admin_logo', 'assets/images/Logo.png',
    '$.common_header_logo', 'assets/images/Logo.png',
    '$.footer_logo', 'assets/images/Logo.png',
    '$.favicon', 'favicon.ico',
    '$.login_page_logo', 'assets/images/Logo.png',
    '$.login_page_image', 'assets/images/login.png'
)
WHERE `key` = 'general';

-- Clear cache after running this (run in terminal):
-- php artisan cache:clear
-- php artisan view:clear
