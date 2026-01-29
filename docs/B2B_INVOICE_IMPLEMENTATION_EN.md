# B2B Invoice Implementation (Tax Invoice)

## Overview
Added support for B2B invoices (Tax Invoices) in addition to the existing B2C invoices (Simplified Invoices).

## Difference between B2C and B2B

### B2C (Simplified Invoice)
- Usually issued between business and consumer
- Does not require detailed customer information
- Customer VAT number is not required

### B2B (Tax Invoice)
- Issued between business and another business
- Requires detailed customer information
- Customer **must** have a VAT number (15 digits)
- Requires complete and detailed address

## Required Fields for B2B Invoices

### In `parties` table (Customers):
1. **zatca_type**: Invoice type (b2c or b2b)
2. **vat_number**: VAT number (15 digits) - **Required for B2B**
3. **building_number**: Building number - **Required for B2B**
4. **street_name**: Street name - **Required for B2B**
5. **district**: District - **Required for B2B**
6. **city**: City - **Required for B2B**
7. **postal_code**: Postal code - **Required for B2B**
8. **country_code**: Country code (SA, AE, BH, etc.) - **Required**

### In `businesses` table:
Same fields as above (except zatca_type and vat_number)

### In `sales` table:
- **invoice_type**: Invoice type (b2c or b2b)

## Applied Changes

### 1. Database Migration
```bash
php artisan migrate
```
Will add new fields to tables:
- parties
- businesses
- sales

### 2. Models
Updated the following Models:
- `App\Models\Party`
- `App\Models\Business`
- `App\Models\Sale`

### 3. Views
Updated pages:
- `Modules/Business/resources/views/parties/create.blade.php`
- `Modules/Business/resources/views/parties/edit.blade.php`

### 4. Controller Validation
Updated `Modules/Business/App/Http/Controllers/AcnooPartyController.php`

## How to Use

### 1. Add New B2B Customer
1. Go to Customers list
2. Click "Add New Customer"
3. Select "B2B - Tax Invoice" from "Invoice Type" dropdown
4. Additional required fields will appear:
   - VAT Number (15 digits)
   - Building Number
   - Street Name
   - District
   - City
   - Postal Code
   - Country Code
5. Fill all required fields
6. Save customer

### 2. Update Existing Customer to B2B
1. Open customer edit page
2. Change "Invoice Type" to "B2B - Tax Invoice"
3. Fill additional required fields
4. Save changes

### 3. Issue B2B Invoice
When creating an invoice for a B2B customer:
- `invoice_type` in `sales` table will be automatically set to 'b2b'
- Complete customer information will be used in the invoice
- Invoice will be sent to ZATCA as Tax Invoice

## Validation Rules

### For B2B Customers:
```php
'zatca_type' => 'required|in:b2c,b2b',
'vat_number' => 'required_if:zatca_type,b2b|nullable|digits:15',
'building_number' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'street_name' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'district' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'city' => 'required_if:zatca_type,b2b|nullable|string|max:255',
'postal_code' => 'required_if:zatca_type,b2b|nullable|string|max:10',
'country_code' => 'required|string|max:2',
```

## JavaScript Functionality
Added JavaScript to show/hide fields automatically:
- When selecting B2C: Hides additional fields
- When selecting B2B: Shows additional fields and makes them required

## Next Steps

### 1. Update UBL Generator
Need to update `app/Services/Zatca/UblGenerator.php` to support B2B invoices:
- Add complete customer information
- Add customer VAT number
- Add complete address

### 2. Update ZATCA Service
Need to update `app/Services/Zatca/ZatcaService.php`:
- Add new function to send B2B invoices
- Differentiate between B2C and B2B in sending process

### 3. Update PDF Template
Need to update invoice PDF template to show:
- Invoice type (B2C or B2B)
- Complete customer information for B2B invoices
- Customer VAT number

### 4. Update Invoice Creation Page
Need to add option to select invoice type when creating new invoice:
- If customer is B2B type, automatically select B2B
- Allow manual change if needed

## Important Notes

1. **VAT Number**: Must be exactly 15 digits
2. **Country Code**: Must be 2 letters (SA, AE, BH, etc.)
3. **Required Fields**: All address fields are required for B2B invoices
4. **ZATCA Compliance**: Ensure all data complies with ZATCA requirements

## Examples

### B2B Customer Data Example:
```json
{
  "name": "Example Company Ltd",
  "zatca_type": "b2b",
  "vat_number": "300123456789003",
  "building_number": "1234",
  "street_name": "King Fahd Road",
  "district": "Al Olaya",
  "city": "Riyadh",
  "postal_code": "12345",
  "country_code": "SA"
}
```

### B2C Customer Data Example:
```json
{
  "name": "Mohammed Ahmed",
  "zatca_type": "b2c",
  "phone": "0501234567",
  "email": "mohammed@example.com"
}
```

## Files Modified/Created

### Created:
1. `database/migrations/2026_01_22_000000_add_b2b_fields_to_parties_and_businesses.php`
2. `database/seeders/UpdateB2BFieldsSeeder.php`
3. `docs/B2B_INVOICE_IMPLEMENTATION.md`
4. `docs/B2B_INVOICE_IMPLEMENTATION_EN.md`

### Modified:
1. `app/Models/Party.php`
2. `app/Models/Business.php`
3. `app/Models/Sale.php`
4. `Modules/Business/resources/views/parties/create.blade.php`
5. `Modules/Business/resources/views/parties/edit.blade.php`
6. `Modules/Business/App/Http/Controllers/AcnooPartyController.php`
