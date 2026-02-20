# Barcode & QR Code Scanner Feature - Usage Guide

## Overview
The barcode and QR code scanner feature has been successfully implemented in your POS system. It allows users to quickly search and add products to the cart by scanning barcodes, QR codes, or typing product codes/names.

## How to Use

### 1. Opening the Scanner
- In the POS interface, look for the **barcode scan icon** in the header
- Click the scan icon to open the barcode & QR code scanner modal

### 2. Search Methods

#### Manual Search
- Type in the search box to find products by:
  - Product name
  - Product code
  - Barcode number
  - QR code content
  - Product description
- Search results appear automatically as you type (minimum 2 characters)

#### Camera Scanning (Enhanced Support)
- Click "Start Camera" button
- Point your device's camera at a barcode or QR code
- The system will automatically detect and search for the product
- **Supports multiple formats**:
  - Traditional barcodes (Code 128, EAN-13, EAN-8, UPC, etc.)
  - QR codes with product information
  - Data Matrix codes
  - PDF417 codes
- Click "Stop Camera" when done

### 3. QR Code Support

#### QR Code Formats Supported
The system can handle various QR code formats:

1. **Simple Product Code**: Just the product code
2. **JSON Format**: 
   ```json
   {"product_id": 123, "code": "ABC123", "name": "Product Name"}
   ```
3. **Pipe-separated**: `ProductCode|Product Name|123`
4. **Semicolon-separated**: `ProductCode;Product Name`
5. **URL with parameters**: `https://example.com/product?id=123&code=ABC123`

#### Why QR Codes Might Not Work
If your QR code isn't working, check:
- **Content Format**: Make sure the QR code contains product information in a supported format
- **Product Exists**: The product must exist in your system with matching code/name/ID
- **Camera Permissions**: Browser needs camera access
- **Lighting**: Ensure good lighting for camera scanning
- **QR Code Quality**: Make sure the QR code is clear and not damaged

### 4. Adding Products to Cart
- Search results show in a table with product details:
  - Product image
  - Name and description
  - Product code
  - Category
  - Price
  - Stock availability
- Click "Add to Cart" button next to any product
- The product will be added to your POS cart
- The modal will close automatically after adding

### 5. Enhanced Features
- **Multi-format detection**: Automatically detects barcode vs QR code
- **Fallback libraries**: Uses QuaggaJS if native detection isn't available
- **Smart parsing**: Extracts product info from complex QR code content
- **Better error handling**: Clear messages for scanning issues
- **Responsive design**: Works on desktop and mobile devices
- **Loading states**: Visual feedback during operations

## Technical Details

### Browser Compatibility
- **Modern browsers**: Full support with native BarcodeDetector API
- **Older browsers**: Fallback to QuaggaJS library
- **Mobile devices**: Optimized camera constraints for better performance
- **Manual input**: Always available as fallback

### QR Code Integration
To make your products work with QR codes:

1. **Add QR Code Field**: If your products table has a `qr_code` field, store QR content there
2. **Generate QR Codes**: Create QR codes with product information in supported formats
3. **Test Scanning**: Use the scanner to verify QR codes work correctly

### Troubleshooting QR Codes

#### Common Issues
1. **QR code scanned but no results**: 
   - Check if the QR code content matches any product in your system
   - Try manual search with the QR code content

2. **Camera not detecting QR codes**:
   - Ensure good lighting
   - Hold device steady
   - Try different angles
   - Check if QR code is clear and undamaged

3. **Complex QR codes not working**:
   - The system tries to parse JSON, pipe-separated, and URL formats
   - For custom formats, you may need to modify the parsing logic

#### Debug Steps
1. Open browser developer tools
2. Check console for detection messages
3. Manually type the QR code content to test parsing
4. Verify product exists with matching information

## Customization

### Adding Custom QR Code Formats
Edit the `extractProductInfoFromQRCode()` method in `AcnooSaleController.php` to support your specific QR code format.

### Camera Settings
Modify camera constraints in `barcode-scanner.js` for different resolutions or camera preferences.

The enhanced scanner now provides comprehensive support for both traditional barcodes and modern QR codes, making your POS system more versatile and user-friendly!