<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة ضريبية #{{ $sale->invoiceNumber }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body { 
            background-color: #f5f5f5; 
            direction: rtl;
            padding: 10px 0;
        }
        
        .invoice-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        /* Header Section */
        .invoice-header {
            padding: 20px 30px;
            background: white;
            border-bottom: 3px solid #e9ecef;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .company-section {
            display: flex;
            gap: 12px;
            align-items: center;
        }
        
        .company-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
        }
        
        .company-details h2 {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 6px;
        }
        
        .company-info-text {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
            margin: 0;
        }
        
        .invoice-title-section {
            text-align: left;
        }
        
        .invoice-title {
            font-size: 24px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 4px;
            text-align: left;
        }
        
        .invoice-subtitle {
            font-size: 10px;
            color: #95a5a6;
            text-align: left;
        }
        
        .header-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 12px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .info-label {
            font-size: 10px;
            color: #6c757d;
            margin-bottom: 3px;
            text-transform: uppercase;
        }
        
        .info-value {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .info-value.large {
            font-size: 14px;
        }
        
        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #28a745;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .print-button:hover {
            background: #218838;
        }
        
        /* Client Card */
        .client-card {
            padding: 15px 30px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        
        .client-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .client-title {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 3px;
        }
        
        .client-name {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .status-badge {
            padding: 8px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        
        .status-badge.paid {
            background: #d4edda;
            color: #155724;
        }
        
        .status-badge.unpaid {
            background: #f8d7da;
            color: #721c24;
        }
        
        .client-info-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 15px;
        }
        
        .client-info-item {
            font-size: 12px;
        }
        
        .client-info-label {
            color: #6c757d;
            margin-bottom: 4px;
        }
        
        .client-info-value {
            color: #2c3e50;
            font-weight: 600;
        }
        
        /* Products Table */
        .table-section {
            padding: 15px 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        
        .products-table thead th {
            background: #2c3e50;
            color: white;
            padding: 10px 8px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            border: 1px solid #1a252f;
        }
        
        .products-table tbody td {
            padding: 10px 8px;
            font-size: 12px;
            border: 1px solid #e9ecef;
            text-align: center;
            vertical-align: middle;
        }
        
        .products-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }
        
        .product-name {
            font-weight: 600;
            color: #2c3e50;
            text-align: right;
        }
        
        .product-desc {
            font-size: 11px;
            color: #6c757d;
            margin-top: 3px;
        }
        
        /* Totals Section */
        .totals-section {
            padding: 0 30px 15px 30px;
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 20px;
        }
        
        .terms-box {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }
        
        .terms-title {
            font-size: 14px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .terms-list li {
            padding: 5px 0;
            padding-right: 12px;
            font-size: 11px;
            color: #495057;
            position: relative;
            line-height: 1.5;
        }
        
        .terms-list li:before {
            content: "•";
            position: absolute;
            right: 0;
            font-weight: bold;
            color: #6c757d;
        }
        
        .totals-box {
            background: white;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .total-row:last-child {
            border-bottom: none;
        }
        
        .total-label {
            font-size: 12px;
            color: #6c757d;
        }
        
        .total-value {
            font-size: 13px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .total-row.discount .total-value {
            color: #dc3545;
        }
        
        .total-row.grand-total {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #2c3e50;
        }
        
        .total-row.grand-total .total-label {
            font-size: 14px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .total-row.grand-total .total-value {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        /* Footer */
        .invoice-footer {
            padding: 10px 30px;
            background: #f8f9fa;
            border-top: 2px solid #e9ecef;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .qr-section {
            text-align: center;
        }
        
        .qr-code {
            width: 80px;
            height: 80px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 4px;
        }
        
        .company-footer {
            text-align: right;
            max-width: 400px;
        }
        
        .company-footer-name {
            font-size: 14px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 6px;
        }
        
        .company-footer-info {
            font-size: 10px;
            color: #6c757d;
            line-height: 1.5;
        }
        
        .company-footer-info i {
            margin-left: 5px;
            color: #95a5a6;
            width: 12px;
        }
        
        .copyright {
            text-align: center;
            padding: 8px;
            font-size: 9px;
            color: #95a5a6;
            background: #f8f9fa;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .invoice-wrapper {
                box-shadow: none;
                max-width: 100%;
            }
            .print-button {
                display: none !important;
            }
            
            /* تقليل المسافات للطباعة بشكل أكبر */
            .invoice-header {
                padding: 4px 15px;
            }
            .header-top {
                margin-bottom: 4px;
            }
            .header-info-grid {
                gap: 4px;
            }
            .info-box {
                padding: 4px 6px;
            }
            .info-label {
                font-size: 8px;
                margin-bottom: 1px;
            }
            .info-value {
                font-size: 10px;
            }
            .info-value.large {
                font-size: 11px;
            }
            .client-card {
                padding: 4px 15px;
            }
            .client-title {
                font-size: 8px;
                margin-bottom: 1px;
            }
            .client-name {
                font-size: 12px;
            }
            .table-section {
                padding: 4px 15px;
            }
            .section-title {
                font-size: 12px;
                margin-bottom: 4px;
            }
            .products-table {
                margin-bottom: 6px;
            }
            .products-table thead th {
                padding: 3px 2px;
                font-size: 8px;
            }
            .products-table tbody td {
                padding: 3px 2px;
                font-size: 9px;
            }
            .totals-section {
                padding: 0 15px 4px 15px;
                gap: 8px;
                grid-template-columns: 1fr 280px;
            }
            .terms-box {
                padding: 6px;
            }
            .terms-title {
                font-size: 11px;
                margin-bottom: 4px;
            }
            .terms-list li {
                padding: 2px 0;
                font-size: 8px;
            }
            .total-row {
                padding: 2px 0;
            }
            .total-label {
                font-size: 9px;
            }
            .total-value {
                font-size: 10px;
            }
            .total-row.grand-total {
                margin-top: 4px;
                padding-top: 4px;
            }
            .total-row.grand-total .total-label {
                font-size: 11px;
            }
            .total-row.grand-total .total-value {
                font-size: 13px;
            }
            .invoice-footer {
                padding: 4px 15px;
            }
            .qr-code {
                width: 55px;
                height: 55px;
            }
            .company-footer-name {
                font-size: 11px;
                margin-bottom: 2px;
            }
            .company-footer-info {
                font-size: 8px;
                line-height: 1.3;
            }
            .copyright {
                padding: 4px;
                font-size: 7px;
            }
            .company-logo {
                width: 40px;
                height: 40px;
            }
            .company-details h2 {
                font-size: 13px;
                margin-bottom: 2px;
            }
            .company-info-text {
                font-size: 7px;
                line-height: 1.1;
            }
            .invoice-title {
                font-size: 16px;
                margin-bottom: 1px;
            }
            .invoice-subtitle {
                font-size: 8px;
            }
            
            @page {
                size: A4;
                margin: 4mm;
            }
        }
    </style>
</head>
<body>
    {{-- Print Button --}}
    <button onclick="window.print()" class="print-button">
        <i class="fas fa-print"></i> طباعة
    </button>
    
    <div class="invoice-wrapper">
        <!-- Header -->
        <div class="invoice-header">
            <div class="header-top">
                <div class="company-section">
                    <img src="{{ asset(get_business_option('business-settings')['invoice_logo'] ?? 'assets/images/default.svg') }}" 
                         alt="Logo" class="company-logo">
                    <div class="company-details">
                        <h2>{{ $sale->business->companyName ?? 'اسم الشركة' }}</h2>
                        @if($sale->business->address || $sale->business->building_number)
                        <p class="company-info-text">
                            @if($sale->business->building_number || $sale->business->street_name)
                                {{ $sale->business->building_number }}، {{ $sale->business->street_name }}، 
                                {{ $sale->business->district }}، {{ $sale->business->city }}
                            @else
                                {{ $sale->business->address }}
                            @endif
                        </p>
                        @endif
                        @if($sale->business->vat_no)
                        <p class="company-info-text">
                            <strong>رقم ضريبة القيمة المضافة:</strong> {{ $sale->business->vat_no }}
                        </p>
                        @endif
                    </div>
                </div>
                
                <div class="invoice-title-section">
                    <h1 class="invoice-title">فاتورة ضريبية</h1>
                    <p class="invoice-subtitle">
                        @if($sale->invoice_type === 'b2b')
                            Tax Invoice / فاتورة ضريبية
                        @else
                            Simplified Tax Invoice / فاتورة ضريبية مبسطة
                        @endif
                    </p>
                </div>
            </div>
            
            <div class="header-info-grid">
                <div class="info-box">
                    <div class="info-label">رقم الفاتورة / Printed At</div>
                    <div class="info-value large">{{ $sale->invoiceNumber }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">التاريخ / Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($sale->saleDate)->format('Y/m/d h:i A') }}</div>
                </div>
                @if($sale->delivery_type)
                <div class="info-box">
                    <div class="info-label">نوع الطلب</div>
                    <div class="info-value">
                        @if($sale->delivery_type == 'delivery')
                            توصيل
                        @elseif($sale->delivery_type == 'pre-order')
                            طلب مسبق
                        @else
                            استلام
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <!-- Client Card -->
        <div class="client-card">
            <div class="client-header">
                <div>
                    <div class="client-title">البائع / Seller</div>
                    <div class="client-name">{{ $sale->user->name ?? 'البائع' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Customer Card -->
        <div class="client-card" style="border-top: 1px solid #e9ecef;">
            <div class="client-header">
                <div>
                    <div class="client-title">المشتري / Customer</div>
                    <div class="client-name">{{ $sale->party->name ?? 'عميل' }}</div>
                </div>
            </div>
        </div>
        
        <!-- Products Table -->
        <div class="table-section">
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">الصنف / Item</th>
                        <th style="width: 10%;">الكمية / Qty</th>
                        <th style="width: 15%;">السعر / Price</th>
                        <th style="width: 10%;">الضريبة / Tax</th>
                        <th style="width: 15%;">المجموع / Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // الحصول على نسبة الضريبة من قاعدة البيانات
                        $vatRate = $sale->vat ? ($sale->vat->rate / 100) : 0.15;
                        $vatPercent = $sale->vat ? $sale->vat->rate : 15;
                    @endphp
                    @foreach ($sale->details as $detail)
                        @php
                            $itemPrice = $detail->price ?? 0;
                            $itemQty = $detail->quantities ?? 0;
                            $itemSubtotal = $itemPrice * $itemQty;
                            // حساب الضريبة باستخدام النسبة من قاعدة البيانات
                            $itemTax = $itemSubtotal * $vatRate;
                            $itemTotal = $itemSubtotal + $itemTax;
                        @endphp
                        <tr>
                            <td style="text-align: right;">{{ $detail->product->productName ?? '-' }}</td>
                            <td>{{ $itemQty }}</td>
                            <td>{!! currency_format($itemPrice, currency: business_currency()) !!}</td>
                            <td>{!! currency_format($itemTax, currency: business_currency()) !!}<br><small style="font-size: 9px;">({{ $vatPercent }}%)</small></td>
                            <td>{!! currency_format($itemTotal, currency: business_currency()) !!}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <!-- Totals Section -->
        <div class="totals-section">
            <div class="terms-box">
                @if(!empty($sale->meta['note']))
                    <h3 class="terms-title">ملاحظات</h3>
                    <p style="font-size: 13px; color: #495057;">{{ $sale->meta['note'] }}</p>
                @endif
            </div>
            
            <div class="totals-box">
                @php
                    // الحصول على نسبة الضريبة من قاعدة البيانات
                    $vatRate = $sale->vat ? ($sale->vat->rate / 100) : 0.15;
                    $vatPercent = $sale->vat ? $sale->vat->rate : 15;
                    
                    // حساب الإجمالي والضريبة من الجدول
                    $productsSubtotal = 0;
                    $productsTax = 0;
                    
                    foreach ($sale->details as $detail) {
                        $itemPrice = $detail->price ?? 0;
                        $itemQty = $detail->quantities ?? 0;
                        $itemSubtotal = $itemPrice * $itemQty;
                        $itemTax = $itemSubtotal * $vatRate;
                        
                        $productsSubtotal += $itemSubtotal;
                        $productsTax += $itemTax;
                    }
                    
                    // الخصم
                    $discountAmount = $sale->discountAmount ?? 0;
                    
                    // قيمة التوصيل
                    $shippingCharge = $sale->shipping_charge ?? 0;
                    
                    // الإجمالي قبل الضريبة (المنتجات - الخصم + التوصيل)
                    $subtotalBeforeVat = $productsSubtotal - $discountAmount + $shippingCharge;
                    
                    // الضريبة على الإجمالي قبل الضريبة
                    $vatAmount = $subtotalBeforeVat * $vatRate;
                    
                    // الإجمالي النهائي
                    $totalWithVat = $subtotalBeforeVat + $vatAmount;
                    
                    // حساب عدد المنتجات
                    $totalItems = $sale->details->sum('quantities');
                @endphp
                
                <div class="total-row">
                    <span class="total-label">عدد المنتجات / Total Items:</span>
                    <span class="total-value">{{ $totalItems }}</span>
                </div>
                
                <div class="total-row">
                    <span class="total-label">الإجمالي الفرعي / Subtotal:</span>
                    <span class="total-value">{!! currency_format($productsSubtotal, currency: business_currency()) !!}</span>
                </div>
                
                @if($discountAmount > 0)
                <div class="total-row discount">
                    <span class="total-label">الخصم / Discount:</span>
                    <span class="total-value">-{!! currency_format($discountAmount, currency: business_currency()) !!}</span>
                </div>
                @endif
                
                @if($shippingCharge > 0)
                <div class="total-row">
                    <span class="total-label">قيمة التوصيل / Shipping:</span>
                    <span class="total-value">{!! currency_format($shippingCharge, currency: business_currency()) !!}</span>
                </div>
                @endif
                
                <div class="total-row">
                    <span class="total-label">الإجمالي قبل الضريبة / Subtotal before VAT:</span>
                    <span class="total-value">{!! currency_format($subtotalBeforeVat, currency: business_currency()) !!}</span>
                </div>
                
                @if($vatAmount > 0)
                <div class="total-row">
                    <span class="total-label">ضريبة القيمة المضافة ({{ $vatPercent }}%) / VAT ({{ $vatPercent }}%):</span>
                    <span class="total-value">{!! currency_format($vatAmount, currency: business_currency()) !!}</span>
                </div>
                @endif
                
                <div class="total-row grand-total">
                    <span class="total-label">الإجمالي شامل الضريبة / Total (incl. VAT):</span>
                    <span class="total-value">{!! currency_format($totalWithVat, currency: business_currency()) !!}</span>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="invoice-footer">
            <div class="qr-section">
                <div class="qr-code">
                    @php
                        $sellerName = $sale->business->companyName ?? '';
                        $vatRegistrationNumber = $sale->business->vat_no ?? '';
                        $timestamp = $sale->created_at ? \Carbon\Carbon::parse($sale->created_at)->toIso8601String() : \Carbon\Carbon::now()->toIso8601String();
                        $invoiceTotal = $sale->totalAmount ?? 0;
                        $vatTotal = $sale->vat_amount ?? 0;
                        
                        $xmlHash = $sale->invoice_hash ?? null;
                        $ecdsaSignature = $sale->cryptographic_stamp ?? null;
                        $publicKey = $sale->business->zatca_setting['public_key'] ?? null;
                        
                        $zatcaQrContent = generateZatcaQrCode($sellerName, $vatRegistrationNumber, $timestamp, $invoiceTotal, $vatTotal, $xmlHash, $ecdsaSignature, $publicKey);
                    @endphp
                    {!! QrCode::size(75)->margin(0)->generate($zatcaQrContent) !!}
                </div>
            </div>
            
            <div class="company-footer">
                <div class="company-footer-name">{{ $sale->business->companyName ?? 'اسم الشركة' }}</div>
                <div class="company-footer-info">
                    @if($sale->business->address)
                        <div><i class="fas fa-map-marker-alt"></i> {{ $sale->business->address }}</div>
                    @endif
                    @if($sale->business->phoneNumber)
                        <div><i class="fas fa-phone"></i> {{ $sale->business->phoneNumber }}</div>
                    @endif
                    @if($sale->business->email)
                        <div><i class="fas fa-envelope"></i> {{ $sale->business->email }}</div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="copyright">
            &copy; {{ date('Y') }} {{ $sale->business->companyName ?? '' }}. جميع الحقوق محفوظة.
        </div>
    </div>
</body>
</html>