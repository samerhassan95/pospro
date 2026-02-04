-- Update existing parties to have default zatca_type as b2c
UPDATE parties 
SET zatca_type = 'b2c', 
    country_code = 'SA' 
WHERE zatca_type IS NULL;

-- Update existing businesses to have default country_code
UPDATE businesses 
SET country_code = 'SA' 
WHERE country_code IS NULL;

-- Update existing sales to have default invoice_type as b2c
UPDATE sales 
SET invoice_type = 'b2c' 
WHERE invoice_type IS NULL;
