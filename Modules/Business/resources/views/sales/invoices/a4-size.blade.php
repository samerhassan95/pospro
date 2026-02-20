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
            padding: 20px 0;
        }
        
        .invoice-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        /* Header Section */
        .invoice-header {
            padding: 30px 40px;
            background: white;
            border-bottom: 3px solid #e9ecef;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 25px;
        }
        
        .company-section {
            display: flex;
            gap: 15px;
            align-items: center;
        }
        
        .company-logo {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        
        .company-details h2 {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 8px;
        }
        
        .company-info-text {
            font-size: 11px;
            color: #6c757d;
            line-height: 1.6;
            margin: 0;
        }
        
        .invoice-title-section {
            text-align: left;
        }
        
        .invoice-title {
            font-size: 28px;
            font-weight: 800;
            color: #2c3e50;
            margin-bottom: 5px;
            text-align: left;
        }
        
        .invoice-subtitle {
            font-size: 11px;
            color: #95a5a6;
            text-align: left;
        }
        
        .header-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
        }
        
        .info-box {
            background: #f8f9fa;
            padding: 18px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .info-label {
            font-size: 11px;
            color: #6c757d;
            margin-bottom: 4px;
            text-transform: uppercase;
        }
        
        .info-value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .info-value.large {
            font-size: 16px;
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
            padding: 25px 40px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        
        .client-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .client-title {
            font-size: 13px;
            color: #6c757d;
            margin-bottom: 5px;
        }
        
        .client-name {
            font-size: 20px;
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
            padding: 30px 40px;
        }
        
        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 20px;
        }
        
        .products-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .products-table thead th {
            background: #2c3e50;
            color: white;
            padding: 12px 10px;
            font-size: 12px;
            font-weight: 600;
            text-align: center;
            border: 1px solid #1a252f;
        }
        
        .products-table tbody td {
            padding: 12px 10px;
            font-size: 13px;
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
            padding: 0 40px 30px 40px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .terms-box {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .terms-title {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 12px;
        }
        
        .terms-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .terms-list li {
            padding: 6px 0;
            padding-right: 15px;
            font-size: 12px;
            color: #495057;
            position: relative;
            line-height: 1.6;
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
            padding: 10px 0;
            border-bottom: 1px solid #e9ecef;
        }
        
        .total-row:last-child {
            border-bottom: none;
        }
        
        .total-label {
            font-size: 13px;
            color: #6c757d;
        }
        
        .total-value {
            font-size: 14px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .total-row.discount .total-value {
            color: #dc3545;
        }
        
        .total-row.grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid #2c3e50;
        }
        
        .total-row.grand-total .total-label {
            font-size: 16px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        .total-row.grand-total .total-value {
            font-size: 20px;
            font-weight: 700;
            color: #2c3e50;
        }
        
        /* Footer */
        .invoice-footer {
            padding: 25px 40px;
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
            width: 100px;
            height: 100px;
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px;
        }
        
        .company-footer {
            text-align: right;
            max-width: 400px;
        }
        
        .company-footer-name {
            font-size: 18px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .company-footer-info {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.8;
        }
        
        .company-footer-info i {
            margin-left: 8px;
            color: #95a5a6;
            width: 15px;
        }
        
        .copyright {
            text-align: center;
            padding: 15px;
            font-size: 11px;
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
            }
            .print-button {
                display: none !important;
            }
            
            /* تقليل المسافات للطباعة */
            .invoice-header {
                padding: 15px 30px;
            }
            .header-top {
                margin-bottom: 15px;
            }
            .header-info-grid {
                gap: 10px;
            }
            .info-box {
                padding: 10px;
            }
            .client-card {
                padding: 12px 30px;
            }
            .table-section {
                padding: 15px 30px;
            }
            .products-table thead th {
                padding: 8px 6px;
                font-size: 11px;
            }
            .products-table tbody td {
                padding: 8px 6px;
                font-size: 12px;
            }
            .totals-section {
                padding: 0 30px 15px 30px;
                gap: 20px;
            }
            .terms-box {
                padding: 12px;
            }
            .total-row {
                padding: 6px 0;
            }
            .invoice-footer {
                padding: 15px 30px;
            }
            .qr-code {
                width: 80px;
                height: 80px;
            }
            .copyright {
                padding: 10px;
                font-size: 10px;
            }
            .company-logo {
                width: 60px;
                height: 60px;
            }
            .company-details h2 {
                font-size: 18px;
                margin-bottom: 5px;
            }
            .company-info-text {
                font-size: 10px;
                line-height: 1.4;
            }
            .invoice-title {
                font-size: 24px;
            }
            .client-title {
                font-size: 11px;
            }
            .client-name {
                font-size: 16px;
            }
            .total-row.grand-total {
                margin-top: 10px;
                padding-top: 10px;
            }
            
            @page {
                size: A4;
                margin: 10mm;
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
                        <th style="width: 45%;">الصنف / Item</th>
                        <th style="width: 15%;">الكمية / Qty</th>
                        <th style="width: 20%;">السعر / Price</th>
                        <th style="width: 20%;">المجموع / Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->details as $detail)
                        @php
                            $itemTotal = ($detail->price ?? 0) * ($detail->quantities ?? 0);
                        @endphp
                        <tr>
                            <td style="text-align: right;">{{ $detail->product->productName ?? '-' }}</td>
                            <td>{{ $detail->quantities ?? 0 }}</td>
                            <td>{{ currency_format($detail->price ?? 0, currency: business_currency()) }}</td>
                            <td>{{ currency_format($itemTotal, currency: business_currency()) }}</td>
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
                    // حساب الضريبة الصحيح
                    if (!empty($sale->vat_amount) && $sale->vat_amount > 0) {
                        $subtotalBeforeVat = $sale->totalAmount - $sale->vat_amount;
                        $vatAmount = $sale->vat_amount;
                    } else {
                        // احسب الضريبة: الإجمالي قبل الضريبة × 15%
                        $subtotalBeforeVat = $sale->totalAmount;
                        $vatAmount = $subtotalBeforeVat * 0.15;
                    }
                    $totalWithVat = $subtotalBeforeVat + $vatAmount;
                    
                    // حساب عدد المنتجات
                    $totalItems = $sale->details->sum('quantities');
                @endphp
                
                <div class="total-row">
                    <span class="total-label">عدد المنتجات / Total Items:</span>
                    <span class="total-value">{{ $totalItems }}</span>
                </div>
                
                <div class="total-row">
                    <span class="total-label">الإجمالي قبل الضريبة / Subtotal:</span>
                    <span class="total-value">{{ currency_format($subtotalBeforeVat, currency: business_currency()) }}</span>
                </div>
                
                <div class="total-row">
                    <span class="total-label">ضريبة القيمة المضافة (15%) / VAT (15%):</span>
                    <span class="total-value">{{ currency_format($vatAmount, currency: business_currency()) }}</span>
                </div>
                
                @if($sale->discountAmount > 0)
                <div class="total-row discount">
                    <span class="total-label">الخصم / Discount:</span>
                    <span class="total-value">-{{ currency_format($sale->discountAmount, currency: business_currency()) }}</span>
                </div>
                @endif
                
                <div class="total-row grand-total">
                    <span class="total-label">الإجمالي / Total:</span>
                    <span class="total-value">{{ currency_format($totalWithVat, currency: business_currency()) }}</span>
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
                    {!! QrCode::size(90)->margin(0)->generate($zatcaQrContent) !!}
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