-- Update footer information in the options table
-- Change acnoo to Nomu and update the link to https://nomu.com/

UPDATE `options` 
SET `value` = JSON_SET(
    `value`,
    '$.admin_footer_link_text', 'Nomu',
    '$.admin_footer_link', 'https://nomu.com/'
)
WHERE `key` = 'general';
