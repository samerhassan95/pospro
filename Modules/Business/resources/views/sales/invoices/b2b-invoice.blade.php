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
        <p>{{ __('Tax Invoice issued between businesses') }} / تصدر غالباً بين منشأة ومنشأة</p>
        <div class="invoice-info">
            <div class="invoice-number">{{ __('Invoice No') }}: {{ $sale->invoiceNumber }}</div>
            <div class="invoice-date">{{ __('Date') }}: {{ \Carbon\Carbon::parse($sale->saleDate)->format('d/m/Y') }}</div>
            @if($sale->delivery_type)
            <div class="delivery-type">{{ __('Order Type') }}: 
                @if($sale->delivery_type == 'delivery')
                    {{ __('Delivery') }} / توصيل
                @elseif($sale->delivery_type == 'pre-order')
                    {{ __('Pre-order') }} / طلب مسبق
                @else
                    {{ __('Takeaway') }} / استلام
                @endif
            </div>
            @endif
        </div>
    </div>

    {{-- Seller and Buyer Information --}}
    <div class="b2b-parties">
        {{-- Seller (البائع) --}}
        <div class="b2b-party-box">
            <h3>{{ __('Seller Information') }} / بيانات البائع</h3>
            <p><strong>{{ __('Company Name') }}:</strong> {{ $sale->business->companyName ?? '---' }}</p>
            <p><strong>{{ __('VAT Number') }}:</strong> {{ $sale->business->vat_no ?? '---' }}</p>
            @if($sale->business->commercial_registration)
            <p><strong>{{ __('CR Number') }}:</strong> {{ $sale->business->commercial_registration }}</p>
            @else
            <p style="color: #ff9800;"><strong>{{ __('CR Number') }}:</strong> <em>{{ __('Not provided - Please add in Settings') }}</em></p>
            @endif
            @if($sale->business->additional_id)
            <p><strong>{{ __('Additional ID') }}:</strong> {{ $sale->business->additional_id }}</p>
            @endif
            <p><strong>{{ __('Building No') }}:</strong> {{ $sale->business->building_number ?? '---' }}</p>
            <p><strong>{{ __('Street') }}:</strong> {{ $sale->business->street_name ?? '---' }}</p>
            <p><strong>{{ __('District') }}:</strong> {{ $sale->business->district ?? '---' }}</p>
            <p><strong>{{ __('City') }}:</strong> {{ $sale->business->city ?? '---' }}</p>
            <p><strong>{{ __('Postal Code') }}:</strong> {{ $sale->business->postal_code ?? '---' }}</p>
            <p><strong>{{ __('Country') }}:</strong> {{ $sale->business->country_code ?? '---' }}</p>
            <p><strong>{{ __('Phone') }}:</strong> {{ $sale->business->phoneNumber ?? '---' }}</p>
            <p><strong>{{ __('Email') }}:</strong> {{ $sale->business->email ?? '---' }}</p>
        </div>

        {{-- Buyer (المشتري) --}}
        <div class="b2b-party-box">
            <h3>{{ __('Buyer Information') }} / بيانات المشتري</h3>
            <p><strong>{{ __('Company Name') }}:</strong> {{ $sale->party->name ?? 'Guest' }}</p>
            @if($sale->party)
            <p><strong>{{ __('VAT Number') }}:</strong> {{ $sale->party->vat_number ?? '---' }}</p>
            @if($sale->party->commercial_registration)
            <p><strong>{{ __('CR Number') }}:</strong> {{ $sale->party->commercial_registration }}</p>
            @else
            <p style="color: #ff9800;"><strong>{{ __('CR Number') }}:</strong> <em>{{ __('Not provided - Please add in Party settings') }}</em></p>
            @endif
            @if($sale->party->additional_id)
            <p><strong>{{ __('Additional ID') }}:</strong> {{ $sale->party->additional_id }}</p>
            @endif
            <p><strong>{{ __('Building No') }}:</strong> {{ $sale->party->building_number ?? '---' }}</p>
            <p><strong>{{ __('Street') }}:</strong> {{ $sale->party->street_name ?? '---' }}</p>
            <p><strong>{{ __('District') }}:</strong> {{ $sale->party->district ?? '---' }}</p>
            <p><strong>{{ __('City') }}:</strong> {{ $sale->party->city ?? '---' }}</p>
            <p><strong>{{ __('Postal Code') }}:</strong> {{ $sale->party->postal_code ?? '---' }}</p>
            <p><strong>{{ __('Country') }}:</strong> {{ $sale->party->country_code ?? '---' }}</p>
            <p><strong>{{ __('Phone') }}:</strong> {{ $sale->party->phone ?? '---' }}</p>
            @else
            <p><em>{{ __('Guest Customer - No details available') }}</em></p>
            @endif
        </div>
    </div>

    {{-- Invoice Details --}}
    <div class="b2b-invoice-details">
        <div>
            <p><strong>{{ __('Invoice Number') }}:</strong> {{ $sale->invoiceNumber ?? '' }}</p>
            <p><strong>{{ __('Invoice Date') }}:</strong> {{ formatted_date($sale->saleDate ?? '') }}</p>
            @if($sale->supply_date)
            <p><strong>{{ __('Supply Date') }}:</strong> {{ formatted_date($sale->supply_date) }}</p>
            @endif
        </div>
        <div style="text-align: right;">
            <p><strong>{{ __('Payment Method') }}:</strong> {{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}</p>
            <p><strong>{{ __('Sales By') }}:</strong> {{ $sale->user->name ?? 'Admin' }}</p>
            @if($sale->po_number)
            <p><strong>{{ __('PO Number') }}:</strong> {{ $sale->po_number }}</p>
            @endif
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
                <th style="width: 30px;">#</th>
                <th style="width: 60px;">{{ __('Code') }}<br>الرمز</th>
                <th>{{ __('Description') }}<br>الوصف</th>
                <th style="width: 40px;">{{ __('UoM') }}<br>وحدة</th>
                <th style="width: 40px;" class="text-center">{{ __('Qty') }}<br>كمية</th>
                <th style="width: 60px;" class="text-right">{{ __('List Pr.') }}<br>السعر</th>
                <th style="width: 45px;" class="text-right">{{ __('Disc %') }}<br>خصم</th>
                <th style="width: 60px;" class="text-right">{{ __('Net Pr.') }}<br>الصافي</th>
                <th style="width: 50px;" class="text-right">{{ __('VAT') }}<br>ضريبة</th>
                <th style="width: 70px;" class="text-right">{{ __('Total') }}<br>إجمالي</th>
            </tr>
        </thead>
        <tbody>
            @php 
                $subtotal = 0;
                $totalTax = 0;
                $vatRate = $sale->vat_percent ?? 15;
            @endphp
            @foreach ($sale->details as $detail)
                @php
                    $quantity = $detail->quantities ?? 0;
                    $price = $detail->price ?? 0;
                    
                    // Calculate discount if exists
                    $discount = $detail->discount ?? 0;
                    $discountPercent = 0;
                    if ($discount > 0 && $price > 0) {
                        $discountPercent = ($discount / ($price * $quantity)) * 100;
                    }
                    
                    // List price (before discount)
                    $listPrice = $detail->list_price ?? $price;
                    
                    // Net price (after discount)
                    $netPrice = $detail->net_price ?? $price;
                    
                    // Line total before tax
                    $lineTotal = $netPrice * $quantity;
                    
                    // Tax per line
                    $taxPerItem = $detail->tax_per_item ?? ($lineTotal * $vatRate / 100);
                    
                    // Line total with tax
                    $lineTotalWithTax = $lineTotal + $taxPerItem;
                    
                    $subtotal += $lineTotal;
                    $totalTax += $taxPerItem;
                    
                    // Item code
                    $itemCode = $detail->item_code ?? ($detail->product->productCode ?? $detail->product->id ?? 'N/A');
                    
                    // Unit of measure
                    $uom = $detail->unit_of_measure ?? ($detail->product->unit->name ?? 'PCS');
                @endphp
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $itemCode }}</td>
                    <td>{{ $detail->product->productName ?? '' }}</td>
                    <td class="text-center">{{ $uom }}</td>
                    <td class="text-center">{{ $quantity }}</td>
                    <td class="text-right">{!! currency_format($listPrice, currency: business_currency()) !!}</td>
                    <td class="text-right">{{ $discountPercent > 0 ? number_format($discountPercent, 1) . '%' : '-' }}</td>
                    <td class="text-right">{!! currency_format($netPrice, currency: business_currency()) !!}</td>
                    <td class="text-right">{!! currency_format($taxPerItem, currency: business_currency()) !!}</td>
                    <td class="text-right">{!! currency_format($lineTotalWithTax, currency: business_currency()) !!}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Tax Summary --}}
    <div class="b2b-tax-summary">
        <h4>{{ __('Tax Summary') }} / ملخص الضرائب</h4>
        <table>
            <thead>
                <tr>
                    <th>{{ __('Tax Rate %') }}<br>نسبة الضريبة</th>
                    <th>{{ __('Taxable Amount') }}<br>المبلغ الخاضع</th>
                    <th>{{ __('Tax Amount') }}<br>قيمة الضريبة</th>
                    <th>{{ __('Total Inc. Tax') }}<br>الإجمالي شامل</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">{{ $sale->vat_percent ?? 15 }}%</td>
                    <td class="text-right">{!! currency_format($subtotal, currency: business_currency()) !!}</td>
                    <td class="text-right">{!! currency_format($sale->vat_amount, currency: business_currency()) !!}</td>
                    <td class="text-right">{!! currency_format($subtotal + $sale->vat_amount, currency: business_currency()) !!}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Totals --}}
    <div class="b2b-totals">
        <table>
            <tr>
                <td>{{ __('Subtotal') }} / المجموع الفرعي:</td>
                <td style="text-align: right;"><strong>{!! currency_format($subtotal, currency: business_currency()) !!}</strong></td>
            </tr>
            <tr>
                <td>{{ __('VAT') }} ({{ $sale->vat_percent ?? 15 }}%) / الضريبة:</td>
                <td style="text-align: right;"><strong>{!! currency_format($sale->vat_amount, currency: business_currency()) !!}</strong></td>
            </tr>
            @if($sale->discountAmount > 0)
            <tr>
                <td>{{ __('Discount') }} / الخصم:</td>
                <td style="text-align: right;"><strong>-{!! currency_format($sale->discountAmount, currency: business_currency()) !!}</strong></td>
            </tr>
            @endif
            @if($sale->shipping_charge > 0)
            <tr>
                <td>{{ __('Shipping') }} / الشحن:</td>
                <td style="text-align: right;"><strong>{!! currency_format($sale->shipping_charge, currency: business_currency()) !!}</strong></td>
            </tr>
            @endif
            <tr class="total-row">
                <td>{{ __('Total Amount') }} / الإجمالي الكلي:</td>
                <td style="text-align: right;">{!! currency_format($sale->totalAmount, currency: business_currency()) !!}</td>
            </tr>
        </table>
    </div>

    {{-- Footer with QR and Signatures --}}
    <div class="b2b-footer">
        <div class="b2b-qr">
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
            {!! QrCode::size(80)->generate($zatcaQrContent) !!}
            <p>{{ __('Scan for Verification') }}<br>امسح للتحقق</p>
        </div>
        
        <div class="b2b-footer-text">
            <p>
                <strong>{{ __('Note') }}:</strong> {{ __('This is a computer-generated tax invoice compliant with ZATCA Phase 2 requirements.') }}
                {{ __('Contact') }}: {{ $sale->business->phoneNumber ?? '' }}
            </p>
            @if($sale->uuid)
            <p style="font-size: 6px; margin-top: 3px;"><strong>UUID:</strong> {{ $sale->uuid }}</p>
            @endif
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
