@php
    $zatcaOption = \App\Models\Option::where('key', 'superadmin_zatca_setting')->first();
    $zatcaSettings = $zatcaOption ? $zatcaOption->value : null;
    $sellerName = $zatcaSettings['csr_config']['common_name'] ?? config('app.name');
    $sellerVat = $zatcaSettings['csr_config']['organization_identifier'] ?? '---';
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Invoice') }} #{{ $subscriber->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Arabic:wght@400;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans Arabic', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: #f0f2f5;
            padding: 20px;
        }

        .b2b-invoice {
            max-width: 210mm;
            min-height: 290mm; /* A4 height approx */
            margin: 0 auto;
            padding: 8mm; /* Reduced padding */
            background: white;
            color: #333;
            font-size: 11px; /* Slightly smaller font */
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            border-radius: 8px;
            display: flex;
            flex-direction: column;
        }

        .b2b-header {
            text-align: center;
            border-bottom: 3px solid #1565C0;
            padding-bottom: 8px;
            margin-bottom: 12px;
            background: linear-gradient(to bottom, #E3F2FD 0%, #ffffff 100%);
            padding-top: 8px;
            border-radius: 8px 8px 0 0;
        }

        .b2b-header h1 {
            color: #1565C0;
            font-size: 20px; /* Smaller header */
            margin: 0;
            font-weight: bold;
        }

        .b2b-parties {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            gap: 12px;
        }

        .b2b-party-box {
            flex: 1;
            border: 1px solid #1565C0;
            padding: 8px;
            border-radius: 6px;
            background: #F9F9F9;
        }

        .b2b-party-box h3 {
            color: #ffffff;
            background: #1565C0;
            font-size: 12px;
            margin: -8px -8px 8px -8px;
            padding: 4px 8px;
            border-radius: 5px 5px 0 0;
            font-weight: bold;
            text-align: center;
        }

        .b2b-party-box p {
            margin: 2px 0; /* Tighter margins */
            font-size: 10px;
            line-height: 1.4;
        }

        .b2b-invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: linear-gradient(135deg, #E3F2FD 0%, #BBDEFB 100%);
            border-radius: 6px;
            border-right: 4px solid #1565C0;
        }

        .b2b-table th {
            padding: 8px 5px;
            text-align: center;
            font-size: 11px;
        }

        .b2b-table td {
            padding: 8px 5px;
            font-size: 11px;
        }

        .b2b-totals {
            margin-right: auto;
            width: 250px;
            margin-top: 5px;
        }

        .b2b-footer {
            margin-top: auto; /* Push to bottom */
            padding-top: 10px;
            border-top: 2px solid #E0E0E0;
        }

        @media print {
            body { background: white; padding: 0; margin: 0; }
            .b2b-invoice { 
                box-shadow: none; 
                max-width: 100%; 
                border: none; 
                padding: 0;
                min-height: auto;
                height: 100vh;
            }
            .no-print { display: none !important; }
            @page {
                size: A4;
                margin: 10mm;
            }
        }

        .print-btn {
            background: #1565C0;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            margin-bottom: 20px;
            display: inline-block;
            cursor: pointer;
            border: none;
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center;">
        <button onclick="window.print()" class="print-btn">طباعة الفاتورة / Print Invoice</button>
    </div>

    <div class="b2b-invoice">
        {{-- Header --}}
        <div class="b2b-header">
            <h1>{{ __('TAX INVOICE') }} / فاتورة ضريبية</h1>
            <p>{{ __('Standard Tax Invoice (B2B)') }} / فاتورة ضريبية خاضعة لضريبة القيمة المضافة</p>
        </div>

        {{-- Parties --}}
        <div class="b2b-parties">
            {{-- Seller --}}
            <div class="b2b-party-box">
                <h3>{{ __('Supplier') }} / المورد</h3>
                <p><strong>اسم المنشأة:</strong> <span>{{ $sellerName }}</span></p>
                <p><strong>الرقم الضريبي:</strong> <span>{{ $sellerVat }}</span></p>
                <p><strong>العنوان:</strong> <span>{{ $zatcaSettings['csr_config']['registered_address'] ?? '---' }}</span></p>
                <p><strong>المدينة/الحي:</strong> <span>{{ $zatcaSettings['csr_config']['location'] ?? '---' }} / {{ $zatcaSettings['csr_config']['organization_unit_name'] ?? '---' }}</span></p>
            </div>

            {{-- Buyer --}}
            <div class="b2b-party-box">
                <h3>{{ __('Customer') }} / العميل (المشترك)</h3>
                <p><strong>اسم الشركة:</strong> <span>{{ $subscriber->business->companyName }}</span></p>
                <p><strong>الرقم الضريبي:</strong> <span>{{ $subscriber->business->vat_no ?? '---' }}</span></p>
                <p><strong>رقم المبنى:</strong> <span>{{ $subscriber->business->building_number ?? '---' }}</span></p>
                <p><strong>الشارع:</strong> <span>{{ $subscriber->business->street_name ?? '---' }}</span></p>
                <p><strong>الحي/المدينة:</strong> <span>{{ $subscriber->business->district ?? '---' }} / {{ $subscriber->business->city ?? '---' }}</span></p>
                <p><strong>الرمز البريدي:</strong> <span>{{ $subscriber->business->postal_code ?? '---' }}</span></p>
            </div>
        </div>

        {{-- Invoice Info --}}
        <div class="b2b-invoice-details">
            <div>
                <p><strong>رقم الفاتورة:</strong> {{ $subscriber->invoice_number }}</p>
                <p><strong>تاريخ الإصدار:</strong> {{ $subscriber->created_at->format('Y-m-d') }}</p>
            </div>
            <div style="text-align: left;">
                <p><strong>طريقة الدفع:</strong> {{ $subscriber->gateway->name ?? 'تحويل بنكي' }}</p>
                <p><strong>حالة الربط:</strong> 
                    <span class="status-badge {{ $subscriber->zatca_status == 'CLEARED' ? 'status-cleared' : 'status-pending' }}">
                        {{ $subscriber->zatca_status ?? 'PENDING' }}
                    </span>
                </p>
            </div>
        </div>

        {{-- Items --}}
        <table class="b2b-table">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>وصف الخدمة / Service Description</th>
                    <th>الكمية / Qty</th>
                    <th>سعر الوحدة / Unit Price</th>
                    <th>الضريبة 15% / VAT</th>
                    <th>الإجمالي الشامل / Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>
                        <strong>{{ $subscriber->plan->subscriptionName }}</strong><br>
                        <small>دورة الاشتراك: {{ $subscriber->duration }} يوم</small>
                    </td>
                    <td>1</td>
                    <td>{{ number_format($taxableAmount, 2) }} SAR</td>
                    <td>{{ number_format($vatTotal, 2) }} SAR</td>
                    <td>{{ number_format($totalAmount, 2) }} SAR</td>
                </tr>
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="b2b-totals">
            <table>
                <tr>
                    <td>الإجمالي غير شامل الضريبة (Subtotal):</td>
                    <td>{{ number_format($taxableAmount, 2) }} SAR</td>
                </tr>
                <tr>
                    <td>مجموع ضريبة القيمة المضافة (Total VAT):</td>
                    <td>{{ number_format($vatTotal, 2) }} SAR</td>
                </tr>
                <tr class="total-row">
                    <td>الإجمالي شامل الضريبة (Total Payable):</td>
                    <td>{{ number_format($totalAmount, 2) }} SAR</td>
                </tr>
            </table>
        </div>

        {{-- Footer --}}
        <div class="b2b-footer">
            <div class="qr-section">
                @if($qrCode)
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($qrCode) }}" alt="ZATCA QR">
                @else
                    <div style="width:120px; height:120px; background:#eee; display:flex; align-items:center; justify-content:center; font-size:10px; color:#999; border:1px dashed #ccc;">
                        QR PENDING COMPLIANCE
                    </div>
                @endif
                <p>امسح للتحقق من الفاتورة</p>
            </div>

            <div style="flex:1; padding-right:30px; font-size:10px; color:#777; line-height:1.6;">
                <p><strong>ملاحظة:</strong> هذه الفاتورة صدرت إلكترونياً وهي خاضعة لأنظمة هيئة الزكاة والضريبة والجمارك (المرحلة الثانية). أي تعديل يدوي على هذه الفاتورة يلغي صلاحيتها.</p>
                <p style="margin-top:10px;">ID: {{ $subscriber->uuid }}</p>
                @if($subscriber->invoice_hash)
                    <p style="word-break: break-all;">HASH: {{ $subscriber->invoice_hash }}</p>
                @endif
            </div>
        </div>

        {{-- Signatures --}}
        <div style="display:flex; justify-content:space-between; margin-top:40px; border-top:1px solid #eee; pt-20">
            <div style="text-align:center; width:200px;">
                <div style="border-top:1px solid #333; margin-bottom:5px; margin-top:20px;"></div>
                <p>ختم وتوقيع المورد</p>
            </div>
            <div style="text-align:center; width:200px;">
                <div style="border-top:1px solid #333; margin-bottom:5px; margin-top:20px;"></div>
                <p>توقيع العميل</p>
            </div>
        </div>
    </div>

</body>
</html>
