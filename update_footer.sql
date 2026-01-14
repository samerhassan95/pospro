-- Update footer information in the options table
-- Change acnoo to codgoo and update the link to https://codgoo.com/

UPDATE `options` 
SET `value` = JSON_SET(
    `value`,
    '$.admin_footer_link_text', 'codgoo',
    '$.admin_footer_link', 'https://codgoo.com/'
)
WHERE `key` = 'general';
