<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة ضريبية مبسطة #{{ $sale->invoiceNumber }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            background-color: #fff;
            direction: rtl;
            padding: 20px;
            font-size: 14px;
        }
        
        .invoice-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px 20px;
            border: 1px solid #ddd;
        }
        
        /* Header */
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px dashed #000;
        }
        
        .logo {
            max-width: 120px;
            max-height: 80px;
            margin: 0 auto 10px;
            display: block;
        }
        
        .company-name {
            font-size: 22px;
            font-weight: 700;
            color: #000;
            margin-bottom: 5px;
        }
        
        .company-info {
            font-size: 11px;
            color: #333;
            line-height: 1.5;
        }
        
        .invoice-title {
            font-size: 16px;
            font-weight: 700;
            margin: 10px 0 3px;
            color: #000;
        }
        
        .invoice-subtitle {
            font-size: 10px;
            color: #666;
        }
        
        /* Divider */
        .divider {
            border-bottom: 2px dashed #000;
            margin: 15px 0;
        }
        
        /* Info Section */
        .info-section {
            margin-bottom: 15px;
            text-align: center;
        }
        
        .info-line {
            font-size: 12px;
            color: #333;
            margin: 5px 0;
        }
        
        .info-label {
            color: #666;
        }
        
        .info-value {
            font-weight: 700;
            color: #000;
        }
        
        /* Customer */
        .customer-section {
            text-align: center;
            margin-bottom: 15px;
        }
        
        .customer-label {
            font-size: 11px;
            color: #666;
        }
        
        .customer-name {
            font-size: 14px;
            font-weight: 700;
            color: #000;
            margin-top: 3px;
        }
        
        /* Table */
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
        }
        
        .products-table thead {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
        }
        
        .products-table th {
            padding: 8px 5px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #000;
        }
        
        .products-table td {
            padding: 8px 5px;
            text-align: center;
            font-size: 12px;
            color: #333;
            border-bottom: 1px solid #ddd;
        }
        
        .products-table tbody tr:last-child td {
            border-bottom: 2px solid #000;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        /* Summary */
        .summary {
            margin: 15px 0;
            text-align: center;
        }
        
        .summary-line {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 12px;
        }
        
        .summary-label {
            color: #666;
        }
        
        .summary-value {
            font-weight: 700;
            color: #000;
        }
        
        .summary-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }
        
        .summary-total .summary-label,
        .summary-total .summary-value {
            font-size: 16px;
            font-weight: 700;
            color: #000;
        }
        
        /* QR Code */
        .qr-section {
            text-align: center;
            margin: 20px 0;
        }
        
        .qr-code {
            max-width: 150px;
            height: auto;
            margin: 0 auto;
        }
        
        .qr-text {
            font-size: 10px;
            color: #666;
            margin-top: 8px;
        }
        
        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 2px dashed #000;
        }
        
        .thank-you {
            font-size: 14px;
            font-weight: 700;
            color: #000;
            margin-bottom: 5px;
        }
        
        .footer-info {
            font-size: 11px;
            color: #666;
        }
        
        /* Buttons */
        .action-buttons {
            position: fixed;
            top: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        /* Print */
        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                border: none;
                padding: 10px;
            }
            .action-buttons {
                display: none !important;
            }
            @page {
                size: 80mm auto;
                margin: 5mm;
            }
        }
    </style>
</head>
<body>
    
    {{-- Buttons --}}
    <div class="action-buttons">
        <a href="{{ route('business.sales.index') }}" class="btn btn-secondary">رجوع</a>
        <button onclick="window.print()" class="btn">طباعة</button>
    </div>

    <div class="invoice-container">
        
        {{-- Header --}}
        <div class="header">
            @php
                $businessSettings = get_business_option('business-settings');
                $logoPath = $businessSettings['invoice_logo'] ?? $businessSettings['logo'] ?? null;
            @endphp
            @if(!empty($logoPath))
                <img src="{{ asset($logoPath) }}" alt="Logo" class="logo">
            @endif
            
            <div class="company-name">{{ $sale->business->companyName ?? 'N/A' }}</div>
            <div class="company-info">
                @if($sale->business->building_number || $sale->business->street_name)
                    {{ $sale->business->building_number }}، {{ $sale->business->street_name }}، {{ $sale->business->district }}، {{ $sale->business->city }}
                @else
                    {{ $sale->business->address ?? '' }}
                @endif
            </div>
            <div class="company-info">{{ $sale->business->vat_no ?? '' }}</div>
            
            <div class="invoice-title">فاتورة ضريبية مبسطة</div>
            <div class="invoice-subtitle">VAT INVOICE</div>
        </div>

        <div class="divider"></div>

        {{-- Invoice Info --}}
        <div class="info-section">
            <div class="info-line">
                <span class="info-label">رقم الفاتورة / Printed At:</span>
                <span class="info-value">{{ $sale->invoiceNumber }}</span>
            </div>
            <div class="info-line">
                <span class="info-value">{{ \Carbon\Carbon::parse($sale->saleDate)->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <div class="divider"></div>

        {{-- Customer --}}
        @if($sale->party_id && $sale->party)
        <div class="customer-section">
            <div class="customer-label">العميل / Customer</div>
            <div class="customer-name">{{ $sale->party->name }}</div>
        </div>
        <div class="divider"></div>
        @endif

        {{-- Products Table --}}
        <table class="products-table">
            <thead>
                <tr>
                    <th class="text-right">الصنف<br>Item</th>
                    <th>الكمية<br>Qty</th>
                    <th>السعر<br>Price</th>
                    <th>المجموع<br>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->details as $detail)
                <tr>
                    <td class="text-right">{{ $detail->product->productName ?? 'N/A' }}</td>
                    <td>{{ $detail->quantities }}</td>
                    <td>{{ currency_format($detail->price, currency: business_currency()) }}</td>
                    <td>{{ currency_format($detail->price * $detail->quantities, currency: business_currency()) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Summary --}}
        <div class="summary">
            @php
                // Get VAT rate from database
                $vatRate = $sale->vat ? ($sale->vat->rate / 100) : 0.15;
                $vatPercent = $sale->vat ? $sale->vat->rate : 15;
                
                // حساب الضريبة الصحيح
                if (!empty($sale->vat_amount) && $sale->vat_amount > 0) {
                    $subtotalBeforeVat = $sale->totalAmount - $sale->vat_amount;
                    $vatAmount = $sale->vat_amount;
                    $totalWithVat = $sale->totalAmount;
                } else {
                    $subtotalBeforeVat = $sale->totalAmount;
                    $vatAmount = $subtotalBeforeVat * $vatRate;
                    $totalWithVat = $subtotalBeforeVat + $vatAmount;
                }
            @endphp
            
            <div class="summary-line">
                <span class="summary-label">الإجمالي قبل الضريبة / Subtotal:</span>
                <span class="summary-value">{{ currency_format($subtotalBeforeVat, currency: business_currency()) }}</span>
            </div>
            
            <div class="summary-line">
                <span class="summary-label">ضريبة القيمة المضافة ({{ $vatPercent }}%):</span>
                <span class="summary-value">{{ currency_format($vatAmount, currency: business_currency()) }}</span>
            </div>
            
            @if($sale->discountAmount > 0)
            <div class="summary-line">
                <span class="summary-label">الخصم / Discount:</span>
                <span class="summary-value">-{{ currency_format($sale->discountAmount, currency: business_currency()) }}</span>
            </div>
            @endif
            
            <div class="summary-line summary-total">
                <span class="summary-label">الإجمالي / Total:</span>
                <span class="summary-value">{{ currency_format($totalWithVat, currency: business_currency()) }}</span>
            </div>
        </div>

        <div class="divider"></div>

        {{-- QR Code --}}
        <div class="qr-section">
            @php
                try {
                    $sellerName = $sale->business->companyName ?? '';
                    $vatNumber = $sale->business->vat_no ?? '';
                    $timestamp = \Carbon\Carbon::parse($sale->saleDate)->toIso8601String();
                    $totalAmount = number_format($totalWithVat, 2, '.', '');
                    $vatAmountStr = number_format($vatAmount, 2, '.', '');
                    
                    $tlv = '';
                    $tlv .= pack('C', 1) . pack('C', strlen($sellerName)) . $sellerName;
                    $tlv .= pack('C', 2) . pack('C', strlen($vatNumber)) . $vatNumber;
                    $tlv .= pack('C', 3) . pack('C', strlen($timestamp)) . $timestamp;
                    $tlv .= pack('C', 4) . pack('C', strlen($totalAmount)) . $totalAmount;
                    $tlv .= pack('C', 5) . pack('C', strlen($vatAmountStr)) . $vatAmountStr;
                    
                    $qrCodeData = base64_encode($tlv);
                    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeData);
                } catch (\Exception $e) {
                    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($sale->invoiceNumber);
                }
            @endphp
            <img src="{{ $qrCodeUrl }}" alt="QR Code" class="qr-code" onerror="this.style.display='none'">
            <div class="qr-text">امسح للتحقق / Scan to verify</div>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <div class="thank-you">شكراً لزيارتكم / Thank you for your visit</div>
            <div class="footer-info">{{ $sale->business->phoneNumber ?? '' }}</div>
        </div>

    </div>
</body>
</html>
