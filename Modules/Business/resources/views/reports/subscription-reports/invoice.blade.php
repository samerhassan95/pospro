@extends('layouts.business.blank')

@section('title')
    {{ __('Subscription Invoice') }}
@endsection

@section('main_content')
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة اشتراك #{{ $subscriber->id }}</title>
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
            grid-template-columns: 1fr 1fr;
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
        
        /* Business Info Section */
        .business-info-section {
            padding: 25px 40px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
        }
        
        .business-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .business-info-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e9ecef;
        }
        
        .business-info-title {
            font-size: 14px;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        
        .business-info-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 12px;
        }
        
        .business-info-label {
            font-weight: 600;
            color: #6c757d;
            min-width: 140px;
        }
        
        .business-info-value {
            color: #2c3e50;
            flex: 1;
        }
        
        /* Subscription Table */
        .subscription-section {
            padding: 30px 40px;
        }
        
        .subscription-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .subscription-table thead {
            background: #2c3e50;
            color: white;
        }
        
        .subscription-table th {
            padding: 12px 10px;
            text-align: center;
            font-size: 12px;
            font-weight: 600;
        }
        
        .subscription-table td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }
        
        .subscription-table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .text-right {
            text-align: right !important;
        }
        
        .text-left {
            text-align: left !important;
        }
        
        /* Summary Section */
        .summary-section {
            padding: 0 40px 30px;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 30px;
        }
        
        .summary-notes {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
        }
        
        .summary-table {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 12px 20px;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }
        
        .summary-row:last-child {
            border-bottom: none;
        }
        
        .summary-label {
            font-weight: 600;
            color: #6c757d;
        }
        
        .summary-value {
            font-weight: 600;
            color: #2c3e50;
        }
        
        .summary-total {
            background: #28a745 !important;
            color: white !important;
            font-size: 16px;
            font-weight: 700;
        }
        
        .summary-total .summary-label,
        .summary-total .summary-value {
            color: white !important;
        }
        
        /* Footer */
        .invoice-footer {
            padding: 20px 40px;
            background: #f8f9fa;
            text-align: center;
            border-top: 2px solid #e9ecef;
        }
        
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .qr-code {
            max-width: 180px;
            height: auto;
            margin: 0 auto;
            border: 3px solid #2c3e50;
            border-radius: 8px;
            padding: 10px;
            background: white;
        }
        
        @media print {
            body { 
                background: white;
                padding: 0;
                margin: 0;
            }
            .invoice-wrapper {
                box-shadow: none;
                max-width: 100%;
                margin: 0;
                padding: 0;
            }
            .action-buttons {
                display: none !important;
            }
            /* Reduce all paddings for print */
            .invoice-header {
                padding: 10px 20px;
                margin-bottom: 0;
            }
            .header-top {
                margin-bottom: 10px;
            }
            .header-info-grid {
                gap: 8px;
            }
            .info-box {
                padding: 8px;
            }
            .business-info-section {
                padding: 10px 20px;
            }
            .business-info-grid {
                gap: 15px;
            }
            .business-info-card {
                padding: 10px;
            }
            .business-info-title {
                font-size: 12px;
                margin-bottom: 8px;
                padding-bottom: 5px;
            }
            .business-info-row {
                margin-bottom: 5px;
                font-size: 10px;
            }
            .subscription-section {
                padding: 10px 20px;
            }
            .subscription-table {
                margin-bottom: 10px;
            }
            .subscription-table th,
            .subscription-table td {
                padding: 4px 6px;
                font-size: 9px;
            }
            .summary-section {
                padding: 0 20px 10px;
                gap: 15px;
            }
            .summary-notes {
                padding: 10px;
            }
            .summary-row {
                padding: 6px 12px;
                font-size: 10px;
            }
            .summary-total {
                font-size: 13px !important;
            }
            .qr-section {
                margin: 5px 0;
                padding: 8px;
                page-break-inside: avoid;
            }
            .qr-code {
                max-width: 100px !important;
                height: auto;
                padding: 5px;
            }
            .qr-section p {
                font-size: 9px;
                margin-top: 5px;
            }
            .invoice-footer {
                padding: 8px 20px;
            }
            .invoice-footer p {
                font-size: 9px;
                margin: 2px 0;
            }
            @page {
                size: A4;
                margin: 8mm;
            }
            /* Prevent page breaks inside important sections */
            .business-info-card,
            .subscription-table,
            .summary-section,
            .qr-section {
                page-break-inside: avoid;
            }
            /* Adjust font sizes for print */
            body {
                font-size: 10px;
                line-height: 1.2;
            }
            .company-details h2 {
                font-size: 16px;
                margin-bottom: 4px;
            }
            .company-info-text {
                font-size: 9px;
                line-height: 1.3;
            }
            .invoice-title {
                font-size: 20px;
                margin-bottom: 3px;
            }
            .invoice-subtitle {
                font-size: 9px;
            }
            .info-label {
                font-size: 9px;
            }
            .info-value {
                font-size: 11px;
            }
            .business-info-label {
                min-width: 100px;
                font-size: 9px;
            }
            .company-logo {
                width: 60px;
                height: 60px;
            }
        }
        
        /* Action Buttons */
        .action-buttons {
            position: fixed;
            top: 20px;
            left: 20px;
            display: flex;
            gap: 10px;
            z-index: 1000;
        }
        
        .action-btn {
            background: white;
            color: #2c3e50;
            border: 2px solid #e9ecef;
            padding: 12px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.15);
        }
        
        .action-btn.print-btn {
            background: #28a745;
            color: white;
            border-color: #28a745;
        }
        
        .action-btn.print-btn:hover {
            background: #218838;
            border-color: #218838;
        }
        
        .action-btn.back-btn {
            background: #6c757d;
            color: white;
            border-color: #6c757d;
        }
        
        .action-btn.back-btn:hover {
            background: #5a6268;
            border-color: #5a6268;
        }
    </style>
</head>
<body>
    
    {{-- Action Buttons --}}
    <div class="action-buttons">
        <a href="{{ route('business.subscription-reports.index') }}" class="action-btn back-btn">
            <i class="fas fa-arrow-right"></i>
            رجوع / Back
        </a>
        <button onclick="window.print()" class="action-btn print-btn">
            <i class="fas fa-print"></i>
            طباعة / Print
        </button>
    </div>

    <div class="invoice-wrapper">
        
        {{-- Header --}}
        <div class="invoice-header">
            <div class="header-top">
                <div class="company-section">
                    @php
                        $businessSettings = get_business_option('business-settings');
                        $logoPath = $businessSettings['invoice_logo'] ?? $businessSettings['logo'] ?? null;
                        
                        // Get admin/system business info
                        $adminBusiness = \App\Models\Business::first(); // System owner
                    @endphp
                    @if(!empty($logoPath))
                        <img src="{{ asset($logoPath) }}" alt="Logo" class="company-logo">
                    @else
                        <img src="{{ asset('assets/images/default.svg') }}" alt="Logo" class="company-logo">
                    @endif
                    <div class="company-details">
                        <h2>{{ config('app.name', 'POS System') }}</h2>
                        <p class="company-info-text">
                            فاتورة اشتراك في النظام<br>
                            Subscription Invoice
                        </p>
                    </div>
                </div>
                <div class="invoice-title-section">
                    <h1 class="invoice-title">فاتورة اشتراك</h1>
                    <p class="invoice-subtitle">SUBSCRIPTION INVOICE</p>
                </div>
            </div>
            
            <div class="header-info-grid">
                <div class="info-box">
                    <div class="info-label">رقم الفاتورة / Invoice No</div>
                    <div class="info-value large">SUB-{{ $subscriber->id }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">التاريخ / Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($subscriber->created_at)->format('d/m/Y') }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">تاريخ البدء / Start Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($subscriber->created_at)->format('d/m/Y') }}</div>
                </div>
                <div class="info-box">
                    <div class="info-label">تاريخ الانتهاء / End Date</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($subscriber->created_at)->addDays($subscriber->duration)->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>

        {{-- Business Information --}}
        <div class="business-info-section">
            <div class="business-info-grid">
                {{-- System Owner Info --}}
                <div class="business-info-card">
                    <h3 class="business-info-title">بيانات مالك النظام / System Owner</h3>
                    
                    <div class="business-info-row">
                        <span class="business-info-label">اسم الشركة / Company:</span>
                        <span class="business-info-value">{{ config('app.name', 'POS System') }}</span>
                    </div>
                    
                    @if($adminBusiness && $adminBusiness->vat_no)
                    <div class="business-info-row">
                        <span class="business-info-label">رقم ض.ق.م / VAT Number:</span>
                        <span class="business-info-value">{{ $adminBusiness->vat_no }}</span>
                    </div>
                    @endif
                    
                    @if($adminBusiness && $adminBusiness->commercial_registration)
                    <div class="business-info-row">
                        <span class="business-info-label">السجل التجاري / CR:</span>
                        <span class="business-info-value">{{ $adminBusiness->commercial_registration }}</span>
                    </div>
                    @endif
                </div>

                {{-- Subscriber Info --}}
                <div class="business-info-card">
                    <h3 class="business-info-title">بيانات المشترك / Subscriber Information</h3>
                    
                    <div class="business-info-row">
                        <span class="business-info-label">اسم التاجر / Business:</span>
                        <span class="business-info-value">{{ $subscriber->business->companyName ?? 'N/A' }}</span>
                    </div>
                    
                    @if($subscriber->business->vat_no)
                    <div class="business-info-row">
                        <span class="business-info-label">رقم ض.ق.م / VAT Number:</span>
                        <span class="business-info-value">{{ $subscriber->business->vat_no }}</span>
                    </div>
                    @endif
                    
                    @if($subscriber->business->commercial_registration)
                    <div class="business-info-row">
                        <span class="business-info-label">السجل التجاري / CR:</span>
                        <span class="business-info-value">{{ $subscriber->business->commercial_registration }}</span>
                    </div>
                    @endif
                    
                    @if($subscriber->business->phoneNumber)
                    <div class="business-info-row">
                        <span class="business-info-label">الهاتف / Phone:</span>
                        <span class="business-info-value">{{ $subscriber->business->phoneNumber }}</span>
                    </div>
                    @endif
                    
                    <div class="business-info-row">
                        <span class="business-info-label">العنوان / Address:</span>
                        <span class="business-info-value">{{ $subscriber->business->address ?? 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Subscription Details Table --}}
        <div class="subscription-section">
            <table class="subscription-table">
                <thead>
                    <tr>
                        <th style="width: 5%;">#</th>
                        <th style="width: 30%;" class="text-right">الخدمة / Service</th>
                        <th style="width: 15%;">المدة / Duration</th>
                        <th style="width: 15%;">تاريخ البدء / Start</th>
                        <th style="width: 15%;">تاريخ الانتهاء / End</th>
                        <th style="width: 20%;">السعر / Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td class="text-right">{{ $subscriber->plan->subscriptionName ?? 'N/A' }}</td>
                        <td>{{ $subscriber->duration }} {{ __('days') }}</td>
                        <td>{{ \Carbon\Carbon::parse($subscriber->created_at)->format('d/m/Y') }}</td>
                        <td>{{ \Carbon\Carbon::parse($subscriber->created_at)->addDays($subscriber->duration)->format('d/m/Y') }}</td>
                        <td>{!! currency_format($subscriber->price, currency: business_currency()) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        {{-- Summary --}}
        <div class="summary-section">
            <div class="summary-notes">
                <strong style="font-size: 13px; color: #2c3e50;">طريقة الدفع / Payment Method:</strong>
                <p style="margin-top: 10px; font-size: 12px; color: #6c757d;">{{ $subscriber->gateway->name ?? 'N/A' }}</p>
                
                @if($subscriber->transaction_id)
                <div style="margin-top: 15px;">
                    <strong style="font-size: 13px; color: #2c3e50;">رقم المعاملة / Transaction ID:</strong>
                    <p style="margin-top: 5px; font-size: 12px; color: #6c757d;">{{ $subscriber->transaction_id }}</p>
                </div>
                @endif
            </div>
            
            <div class="summary-table">
                @php
                    $subtotal = $subscriber->price;
                    $vatRate = 15; // VAT rate
                    $vatAmount = ($subtotal * $vatRate) / 100;
                    $total = $subtotal + $vatAmount;
                @endphp
                
                <div class="summary-row">
                    <span class="summary-label">المجموع الفرعي / Subtotal:</span>
                    <span class="summary-value">{!! currency_format($subtotal, currency: business_currency()) !!}</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">ضريبة القيمة المضافة ({{ $vatRate }}%) / VAT:</span>
                    <span class="summary-value">{!! currency_format($vatAmount, currency: business_currency()) !!}</span>
                </div>
                
                <div class="summary-row summary-total">
                    <span class="summary-label">الإجمالي شامل الضريبة / Total Including VAT:</span>
                    <span class="summary-value">{!! currency_format($total, currency: business_currency()) !!}</span>
                </div>
            </div>
        </div>

        {{-- QR Code --}}
        <div class="qr-section">
            @php
                // Generate ZATCA QR Code for subscription
                try {
                    $sellerName = config('app.name', 'POS System');
                    $vatNumber = $adminBusiness->vat_no ?? '000000000000000';
                    $timestamp = \Carbon\Carbon::parse($subscriber->created_at)->toIso8601String();
                    $totalAmount = number_format($total, 2, '.', '');
                    $vatAmountStr = number_format($vatAmount, 2, '.', '');
                    
                    // TLV encoding
                    $tlv = '';
                    $tlv .= pack('C', 1) . pack('C', strlen($sellerName)) . $sellerName;
                    $tlv .= pack('C', 2) . pack('C', strlen($vatNumber)) . $vatNumber;
                    $tlv .= pack('C', 3) . pack('C', strlen($timestamp)) . $timestamp;
                    $tlv .= pack('C', 4) . pack('C', strlen($totalAmount)) . $totalAmount;
                    $tlv .= pack('C', 5) . pack('C', strlen($vatAmountStr)) . $vatAmountStr;
                    
                    $qrCodeData = base64_encode($tlv);
                    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeData);
                } catch (\Exception $e) {
                    $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode('SUB-' . $subscriber->id);
                }
            @endphp
            <img src="{{ $qrCodeUrl }}" alt="QR Code" class="qr-code" onerror="this.style.display='none'">
            <p style="margin-top: 10px; font-size: 11px; color: #6c757d;">امسح للتحقق / Scan for verification</p>
        </div>

        {{-- Footer --}}
        <div class="invoice-footer">
            <p style="font-size: 11px; color: #6c757d; margin-bottom: 5px;">هذه فاتورة إلكترونية ولا تحتاج إلى توقيع</p>
            <p style="font-size: 11px; color: #6c757d;">This is a computer-generated invoice and does not require a signature</p>
            <p style="font-size: 12px; color: #2c3e50; margin-top: 10px; font-weight: 600;">شكراً لاشتراكك معنا / Thank you for your subscription!</p>
        </div>

    </div>
</body>
</html>
@endsection
