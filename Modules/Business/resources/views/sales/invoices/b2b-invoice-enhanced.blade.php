{{-- Enhanced B2B Tax Invoice Template with All ZATCA Requirements --}}
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }
    .b2b-invoice {
        font-family: 'Arial', 'Helvetica', sans-serif;
        max-width: 210mm;
        margin: 0 auto;
        padding: 8mm;
        background: white;
        color: #333;
        font-size: 9px;
    }
    .b2b-header {
        text-align: center;
        border-bottom: 2px solid #1565C0;
        padding-bottom: 6px;
        margin-bottom: 8px;
        background: linear-gradient(to bottom, #E3F2FD 0%, #ffffff 100%);
        padding-top: 6px;
    }
    .b2b-header h1 {
        color: #1565C0;
        font-size: 18px;
        margin: 0;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .b2b-header p {
        color: #555;
        margin: 2px 0;
        font-size: 8px;
        font-style: italic;
    }
    
    /* Company Header */
    .b2b-company-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
        padding-bottom: 5px;
        border-bottom: 1px solid #E0E0E0;
    }
    .b2b-company-logo {
        flex: 0 0 auto;
    }
    .b2b-company-logo img {
        max-width: 70px;
        max-height: 35px;
        object-fit: contain;
    }
    .b2b-company-info {
        flex: 1;
        padding-left: 10px;
    }
    .b2b-company-info h2 {
        font-size: 13px;
        color: #1565C0;
        margin-bottom: 3px;
        font-weight: bold;
    }
    .b2b-company-info p {
        font-size: 7px;
        color: #666;
        margin: 1px 0;
        line-height: 1.2;
    }
    
    /* Parties Section */
    .b2b-parties {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        gap: 8px;
    }
    .b2b-party-box {
        flex: 1;
        border: 1px solid #1565C0;
        padding: 6px;
        border-radius: 4px;
        background: #F9F9F9;
    }
    .b2b-party-box h3 {
        color: #ffffff;
        background: #1565C0;
        font-size: 9px;
        margin: -6px -6px 5px -6px;
        padding: 4px 6px;
        border-radius: 3px 3px 0 0;
        font-weight: bold;
        text-transform: uppercase;
    }
    .b2b-party-box p {
        margin: 2px 0;
        font-size: 7px;
        line-height: 1.3;
        color: #333;
    }
    .b2b-party-box strong {
        color: #1565C0;
        display: inline-block;
        min-width: 70px;
        font-weight: 600;
        font-size: 7px;
    }
    
    /* Invoice Details */
    .b2b-invoice-details {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        padding: 5px 8px;
        background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
        border-radius: 4px;
        border-left: 2px solid #1565C0;
    }
    .b2b-invoice-details div {
        flex: 1;
    }
    .b2b-invoice-details p {
        margin: 2px 0;
        font-size: 8px;
        color: #333;
    }
    .b2b-invoice-details strong {
        color: #0D47A1;
        font-weight: 600;
    }
    
    /* Additional Info Box */
    .b2b-additional-info {
        margin-bottom: 8px;
        padding: 5px 8px;
        background: #FFF9C4;
        border-radius: 4px;
        border-left: 2px solid #F57C00;
    }
    .b2b-additional-info h4 {
        font-size: 8px;
        color: #F57C00;
        margin-bottom: 3px;
        font-weight: bold;
    }
    .b2b-additional-info p {
        margin: 1px 0;
        font-size: 7px;
        color: #555;
    }
    .b2b-additional-info strong {
        color: #E65100;
        font-weight: 600;
    }
    
    /* Products Table */
    .b2b-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 8px;
        font-size: 6.5px;
    }
    .b2b-table thead {
        background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%);
        color: white;
    }
    .b2b-table th {
        padding: 4px 2px;
        text-align: left;
        font-size: 6.5px;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 0.2px;
        border-right: 1px solid rgba(255,255,255,0.2);
    }
    .b2b-table th:last-child {
        border-right: none;
    }
    .b2b-table td {
        padding: 3px 2px;
        border-bottom: 1px solid #E0E0E0;
        font-size: 6.5px;
        background: white;
    }
    .b2b-table tbody tr:nth-child(even) {
        background: #FAFAFA;
    }
    .b2b-table .text-center {
        text-align: center;
    }
    .b2b-table .text-right {
        text-align: right;
    }
    
    /* Tax Summary Table */
    .b2b-tax-summary {
        margin-bottom: 8px;
        width: 100%;
        max-width: 400px;
        margin-left: auto;
    }
    .b2b-tax-summary h4 {
        font-size: 9px;
        color: #1565C0;
        margin-bottom: 4px;
        font-weight: bold;
        text-align: center;
        background: #E3F2FD;
        padding: 3px;
        border-radius: 3px;
    }
    .b2b-tax-summary table {
        width: 100%;
        border-collapse: collapse;
        border: 1px solid #1565C0;
    }
    .b2b-tax-summary th {
        background: #1565C0;
        color: white;
        padding: 3px;
        font-size: 7px;
        text-align: center;
        font-weight: bold;
    }
    .b2b-tax-summary td {
        padding: 3px;
        font-size: 7px;
        text-align: right;
        border-bottom: 1px solid #E0E0E0;
        background: white;
    }
    
    /* Totals Section */
    .b2b-totals {
        margin-left: auto;
        width: 250px;
        margin-top: 5px;
    }
    .b2b-totals table {
        width: 100%;
        border-collapse: collapse;
    }
    .b2b-totals td {
        padding: 3px 6px;
        font-size: 8px;
        border-bottom: 1px solid #E0E0E0;
        background: white;
    }
    .b2b-totals td:first-child {
        color: #555;
        font-weight: 500;
    }
    .b2b-totals td:last-child {
        text-align: right;
        font-weight: 600;
        color: #333;
    }
    .b2b-totals .total-row {
        background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%);
        color: white !important;
        font-weight: bold;
        font-size: 10px;
        border: none;
    }
    .b2b-totals .total-row td {
        color: white;
        padding: 5px 6px;
        border: none;
    }
    
    /* Payment Info Box */
    .b2b-payment-info {
        margin-bottom: 8px;
        padding: 5px 8px;
        background: #E8F5E9;
        border-radius: 4px;
        border-left: 2px solid #4CAF50;
    }
    .b2b-payment-info h4 {
        font-size: 8px;
        color: #2E7D32;
        margin-bottom: 3px;
        font-weight: bold;
    }
    .b2b-payment-info p {
        margin: 1px 0;
        font-size: 7px;
        color: #555;
    }
    .b2b-payment-info strong {
        color: #1B5E20;
        font-weight: 600;
    }
    
    /* Footer */
    .b2b-footer {
        margin-top: 8px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding-top: 6px;
        border-top: 1px solid #E0E0E0;
    }
    .b2b-qr {
        text-align: center;
        padding: 3px;
        border: 1px solid #1565C0;
        border-radius: 4px;
        background: white;
    }
    .b2b-qr p {
        font-size: 6px;
        color: #666;
        margin-top: 2px;
        font-weight: 500;
    }
    .b2b-footer-text {
        flex: 1;
        padding: 0 10px;
    }
    .b2b-footer-text p {
        font-size: 7px;
        color: #666;
        line-height: 1.4;
        text-align: justify;
    }
    
    /* Signatures */
    .b2b-signatures {
        display: flex;
        justify-content: space-between;
        margin-top: 12px;
        padding-top: 6px;
    }
    .b2b-signature {
        text-align: center;
        width: 120px;
    }
    .b2b-signature-line {
        border-top: 1px solid #333;
        margin-bottom: 3px;
        margin-top: 15px;
    }
    .b2b-signature p {
        font-size: 7px;
        color: #333;
        margin: 1px 0;
    }
    .b2b-signature strong {
        font-weight: 600;
    }
    
    /* Print Styles */
    @media print {
        .no-print { 
            display: none !important; 
        }
        .b2b-invoice { 
            padding: 5mm;
            max-width: 100%;
            font-size: 8px;
        }
        @page {
            size: A4;
            margin: 5mm;
        }
    }
    
    /* Buttons */
    .b2b-print-buttons {
        text-align: center;
        margin-top: 10px;
        padding-top: 8px;
        border-top: 1px dashed #E0E0E0;
    }
    .b2b-btn {
        padding: 6px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        margin: 0 4px;
        transition: all 0.3s;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }
    .b2b-btn-primary {
        background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%);
        color: white;
    }
    .b2b-btn-primary:hover {
        background: linear-gradient(135deg, #0D47A1 0%, #1565C0 100%);
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }
    .b2b-btn-secondary {
        background: #757575;
        color: white;
    }
    .b2b-btn-secondary:hover {
        background: #616161;
        box-shadow: 0 2px 5px rgba(0,0,0,0.3);
    }
</style>

<div class="b2b-invoice">
    {{-- Header --}}
    <div class="b2b-header">
        <h1>{{ __('TAX INVOICE') }} / فاتورة ضريبية</h1>
        <p style="margin-top: 4px; font-size: 9px;">{{ __('Tax Invoice issued between businesses') }} / (تصدر غالباً بين منشأة ومنشأة أخرى)</p>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px; padding: 4px 0;">
            <div>
                <p style="margin: 2px 0; font-size: 8px;"><strong>{{ __('Serial Number') }} / الرقم التسلسلي:</strong> {{ $sale->invoiceNumber ?? '' }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 2px 0; font-size: 8px;"><strong>{{ __('Date') }} / التاريخ:</strong> {{ formatted_date($sale->saleDate ?? '') }} {{ formatted_time($sale->saleDate ?? '') }}</p>
            </div>
        </div>
    </div>

    {{-- Seller and Buyer Information --}}
    <div class="b2b-parties">
        {{-- Seller (البائع) --}}
        <div class="b2b-party-box">
            <h3>{{ __('Seller Information') }} / معلومات البائع</h3>
            <p><strong>{{ __('Seller Name') }} / اسم البائع:</strong> {{ $sale->business->companyName ?? '---' }}</p>
            <p><strong>{{ __('Seller Address') }} / عنوان البائع:</strong> 
                @if($sale->business->building_number || $sale->business->street_name || $sale->business->district || $sale->business->city)
                    {{ $sale->business->building_number ?? '' }}{{ $sale->business->building_number && $sale->business->street_name ? ', ' : '' }}{{ $sale->business->street_name ?? '' }}{{ ($sale->business->building_number || $sale->business->street_name) && ($sale->business->district || $sale->business->city) ? ', ' : '' }}{{ $sale->business->district ?? '' }}{{ $sale->business->district && $sale->business->city ? ', ' : '' }}{{ $sale->business->city ?? '' }}{{ $sale->business->postal_code ? ' - ' . $sale->business->postal_code : '' }}
                @else
                    ---
                @endif
            </p>
            <p><strong>{{ __('VAT Registration Number') }} / رقم تسجيل ضريبة القيمة المضافة للبائع:</strong> {{ $sale->business->vat_no ?? '---' }}</p>
            <p><strong>{{ __('Commercial Registration Number') }} / رقم السجل التجاري:</strong> {{ $sale->business->commercial_registration ?? '---' }}</p>
        </div>

        {{-- Buyer (المشتري) --}}
        <div class="b2b-party-box">
            <h3>{{ __('Buyer Information') }} / معلومات المشتري</h3>
            <p><strong>{{ __('Buyer Name') }} / اسم المشتري:</strong> {{ $sale->party->name ?? 'Guest' }}</p>
            <p><strong>{{ __('Buyer Address') }} / عنوان المشتري:</strong> 
                @if($sale->party && ($sale->party->building_number || $sale->party->street_name || $sale->party->district || $sale->party->city))
                    {{ $sale->party->building_number ?? '' }}{{ $sale->party->building_number && $sale->party->street_name ? ', ' : '' }}{{ $sale->party->street_name ?? '' }}{{ ($sale->party->building_number || $sale->party->street_name) && ($sale->party->district || $sale->party->city) ? ', ' : '' }}{{ $sale->party->district ?? '' }}{{ $sale->party->district && $sale->party->city ? ', ' : '' }}{{ $sale->party->city ?? '' }}{{ $sale->party->postal_code ? ' - ' . $sale->party->postal_code : '' }}
                @else
                    ---
                @endif
            </p>
            <p><strong>{{ __('VAT Registration Number') }} / رقم تسجيل ضريبة القيمة المضافة للمشتري:</strong> {{ $sale->party->vat_number ?? '---' }}</p>
            <p><strong>{{ __('Commercial Registration Number') }} / رقم السجل التجاري:</strong> {{ ($sale->party && $sale->party->commercial_registration) ? $sale->party->commercial_registration : '---' }}</p>
        </div>
    </div>


    {{-- Additional Information --}}
    @if($sale->contract_number || $sale->payment_terms || $sale->payment_means)
    <div class="b2b-additional-info">
        <h4>{{ __('Additional Information') }} / معلومات إضافية</h4>
        @if($sale->contract_number)
        <p><strong>{{ __('Contract Number') }}:</strong> {{ $sale->contract_number }}</p>
        @endif
        @if($sale->payment_terms)
        <p><strong>{{ __('Payment Terms') }}:</strong> {{ $sale->payment_terms }}</p>
        @endif
        @if($sale->payment_means)
        <p><strong>{{ __('Payment Means') }}:</strong> {{ $sale->payment_means }}</p>
        @endif
    </div>
    @endif

    {{-- Payment Information --}}
    @if($sale->business->bank_name || $sale->business->bank_account_number)
    <div class="b2b-payment-info">
        <h4>{{ __('Payment Information') }} / معلومات الدفع</h4>
        @if($sale->business->bank_name)
        <p><strong>{{ __('Bank Name') }}:</strong> {{ $sale->business->bank_name }}</p>
        @endif
        @if($sale->business->bank_account_number)
        <p><strong>{{ __('Bank Account') }}:</strong> {{ $sale->business->bank_account_number }}</p>
        @endif
        @if($sale->payment_terms)
        <p><strong>{{ __('Payment Terms') }}:</strong> {{ $sale->payment_terms }}</p>
        @endif
    </div>
    @endif

    {{-- Products Table --}}
    <table class="b2b-table">
        <thead>
            <tr>
                <th style="width: 80px;">{{ __('Product') }}<br>المنتج</th>
                <th style="width: 60px;" class="text-right">{{ __('Unit Price') }}<br>سعر الوحدة</th>
                <th style="width: 50px;" class="text-center">{{ __('Quantity') }}<br>الكمية</th>
                <th style="width: 80px;" class="text-right">{{ __('Subtotal without Tax') }}<br>المجموع الفرعي بدون الضريبة</th>
                <th style="width: 60px;" class="text-center">{{ __('Tax Rate') }}<br>نسبة الضريبة</th>
                <th style="width: 70px;" class="text-right">{{ __('Tax Value') }}<br>قيمة الضريبة</th>
                <th style="width: 90px;" class="text-right">{{ __('Total including VAT') }}<br>المجموع شامل ضريبة القيمة المضافة</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $subtotal = 0;
                $totalTax = 0;
                // Get VAT rate from sale or vat relationship or default to 15%
                $vatRate = $sale->vat->rate ?? $sale->vat_percent ?? 15;
                if ($vatRate == 0 || $vatRate == null) {
                    $vatRate = 15; // Default to 15% if not set
                }
            @endphp
            @foreach ($sale->details as $detail)
                @php
                    $unitPrice = $detail->list_price ?? $detail->price ?? 0;
                    $quantity = $detail->quantities ?? 0;
                    $lineSubtotal = $unitPrice * $quantity;
                    // Use tax_per_item if available, otherwise calculate from line subtotal
                    $taxValue = $detail->tax_per_item ?? ($lineSubtotal * $vatRate / 100);
                    $lineTotalWithTax = $lineSubtotal + $taxValue;
                    
                    $subtotal += $lineSubtotal;
                    $totalTax += $taxValue;
                @endphp
                <tr>
                    <td>{{ $detail->product->productName ?? '' }}</td>
                    <td class="text-right">{!! currency_format($unitPrice, currency: business_currency()) !!}</td>
                    <td class="text-center">{{ $quantity }}</td>
                    <td class="text-right">{!! currency_format($lineSubtotal, currency: business_currency()) !!}</td>
                    <td class="text-center">{{ number_format($vatRate, 0) }}%</td>
                    <td class="text-right">{!! currency_format($taxValue, currency: business_currency()) !!}</td>
                    <td class="text-right">{!! currency_format($lineTotalWithTax, currency: business_currency()) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Totals Section - Matching ZATCA Sample --}}
    @php
        // Calculate final VAT amount - use sale vat_amount if available, otherwise calculate from subtotal
        $finalVatAmount = $sale->vat_amount ?? ($subtotal * $vatRate / 100);
        // Ensure we use the correct VAT rate (not 0)
        $displayVatRate = $vatRate > 0 ? $vatRate : 15;
    @endphp
    <div class="b2b-totals" style="margin-top: 10px; margin-bottom: 10px;">
        <table style="width: 100%; max-width: 400px; margin-left: auto; border-collapse: collapse;">
            <tr>
                <td style="padding: 5px; text-align: right; font-size: 8px; border-bottom: 1px solid #E0E0E0;">{{ __('Total') }} / المجموع:</td>
                <td style="padding: 5px; text-align: right; font-size: 8px; font-weight: 600; border-bottom: 1px solid #E0E0E0;">{!! currency_format($subtotal, currency: business_currency()) !!}</td>
            </tr>
            <tr>
                <td style="padding: 5px; text-align: right; font-size: 8px; border-bottom: 1px solid #E0E0E0;">{{ __('VAT') }} ({{ number_format($displayVatRate, 0) }}%) / ضريبة القيمة المضافة ({{ number_format($displayVatRate, 0) }}%):</td>
                <td style="padding: 5px; text-align: right; font-size: 8px; font-weight: 600; border-bottom: 1px solid #E0E0E0;">{!! currency_format($finalVatAmount, currency: business_currency()) !!}</td>
            </tr>
            <tr class="total-row">
                <td style="padding: 8px; text-align: right; font-size: 10px; font-weight: bold; background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%); color: white;">{{ __('Total with Tax') }} ({{ number_format($displayVatRate, 0) }}%) / المجموع مع الضريبة ({{ number_format($displayVatRate, 0) }}%):</td>
                <td style="padding: 8px; text-align: right; font-size: 10px; font-weight: bold; background: linear-gradient(135deg, #1565C0 0%, #1976D2 100%); color: white;">{!! currency_format($subtotal + $finalVatAmount, currency: business_currency()) !!}</td>
            </tr>
        </table>
    </div>

    {{-- Footer with QR Code - Matching ZATCA Sample Layout --}}
    <div class="b2b-footer" style="margin-top: 15px; padding-top: 10px; border-top: 1px solid #E0E0E0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start;">
            {{-- QR Code on Left --}}
            <div class="b2b-qr" style="flex: 0 0 auto; margin-right: 15px;">
                @php
                    $sellerName = $sale->business->companyName ?? '';
                    $vatRegistrationNumber = $sale->business->vat_no ?? '';
                    $timestamp = $sale->created_at ? \Carbon\Carbon::parse($sale->created_at)->toIso8601String() : \Carbon\Carbon::now()->toIso8601String();
                    $invoiceTotal = $sale->totalAmount ?? 0;
                    // Use calculated VAT amount for QR code (recalculate if needed)
                    $qrVatAmount = isset($finalVatAmount) ? $finalVatAmount : ($sale->vat_amount ?? ($subtotal * ($sale->vat->rate ?? $sale->vat_percent ?? 15) / 100));
                    $xmlHash = $sale->invoice_hash ?? null;
                    $ecdsaSignature = $sale->cryptographic_stamp ?? null;
                    $publicKey = $sale->business->zatca_setting['public_key'] ?? null;
                    $zatcaQrContent = generateZatcaQrCode($sellerName, $vatRegistrationNumber, $timestamp, $invoiceTotal, $qrVatAmount, $xmlHash, $ecdsaSignature, $publicKey);
                @endphp
                {!! QrCode::size(100)->generate($zatcaQrContent) !!}
                <p style="font-size: 7px; margin-top: 4px; text-align: center; font-weight: 500;">{{ __('QR Code') }}<br>رمز الاستجابة السريعة (QR Code)</p>
                <p style="font-size: 6px; margin-top: 2px; text-align: center; color: #666;">{{ __('Date and time of invoice issuance') }}<br>تاريخ و وقت إصدار الفاتورة</p>
            </div>
            
            {{-- Footer Text on Right --}}
            <div class="b2b-footer-text" style="flex: 1; padding-left: 10px;">
                <p style="font-size: 7px; line-height: 1.5; color: #666; text-align: justify;">
                    <strong>{{ __('Note') }}:</strong> {{ __('This is a computer-generated tax invoice compliant with ZATCA Phase 2 requirements.') }}
                    {{ __('Contact') }}: {{ $sale->business->phoneNumber ?? '' }}
                </p>
                @if($sale->uuid)
                <p style="font-size: 6px; margin-top: 3px; color: #666;"><strong>UUID:</strong> {{ $sale->uuid }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Signatures --}}
    <div class="b2b-signatures">
        <div class="b2b-signature">
            <div class="b2b-signature-line"></div>
            <p><strong>{{ __('Buyer Signature') }}</strong></p>
            <p>توقيع المشتري</p>
        </div>
        <div class="b2b-signature">
            <div class="b2b-signature-line"></div>
            <p><strong>{{ __('Seller Signature') }}</strong></p>
            <p>توقيع البائع</p>
        </div>
    </div>

    {{-- Print Buttons (hidden when printing) --}}
    <div class="no-print b2b-print-buttons">
        <button onclick="window.print()" class="b2b-btn b2b-btn-primary">
            <i class="fas fa-print"></i> {{ __('Print Invoice') }}
        </button>
        <a href="{{ route('business.sales.index') }}" class="b2b-btn b2b-btn-secondary">
            <i class="fas fa-arrow-left"></i> {{ __('Back to Sales') }}
        </a>
    </div>
</div>
