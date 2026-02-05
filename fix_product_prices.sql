-- Fix Product Prices for B2B Invoices
-- Run this SQL to update the "first" product price

-- Update the "first" product price in stocks table
UPDATE stocks 
SET 
    productSalePrice = 100,
    productWholeSalePrice = 90,
    productDealerPrice = 85,
    productPurchasePrice = 70
WHERE product_id = (
    SELECT id FROM products 
    WHERE productName = 'first' 
    AND business_id = 4 
    LIMIT 1
);

-- Verify the update
SELECT 
    p.id as product_id,
    p.productName,
    s.productSalePrice as sale_price,
    s.productWholeSalePrice as wholesale_price,
    s.productDealerPrice as dealer_price,
    s.productPurchasePrice as purchase_price,
    s.productStock as stock
FROM products p
JOIN stocks s ON s.product_id = p.id
WHERE p.productName = 'first' 
AND p.business_id = 4;

-- Expected result:
-- product_id | productName | sale_price | wholesale_price | dealer_price | purchase_price | stock
-- -----------|-------------|------------|-----------------|--------------|----------------|------
-- [id]       | first       | 100.00     | 90.00           | 85.00        | 70.00          | 643
