<div class="invoice-container-sm" style="width: 80mm; font-family: Arial, sans-serif; margin: 0 auto;">
    <div class="invoice-content" style="padding: 5px;">
        
        {{-- Logo --}}
        <div style="text-align: center; margin-bottom: 5px;">
            <img src="{{ asset(get_business_option('business-settings')['invoice_logo'] ?? 'assets/images/default.svg') }}" 
                 alt="Logo" style="max-width: 50px; max-height: 40px;">
        </div>
        
        {{-- Company Info --}}
        <div style="text-align: center; font-size: 10px; margin-bottom: 5px;">
            <strong>{{ $sale->business->companyName ?? 'POS Pro' }}</strong><br>
            @if($sale->business->address)
            <span style="font-size: 8px;">{{ $sale->business->address }}</span><br>
            @endif
            @if($sale->business->phoneNumber)
            <span style="font-size: 8px;">{{ $sale->business->phoneNumber }}</span><br>
            @endif
            @if($sale->business->vat_no)
            <span style="font-size: 8px;">VAT: {{ $sale->business->vat_no }}</span>
            @endif
        </div>
        
        {{-- Invoice Title --}}
        <div style="text-align: center; border-top: 2px dashed #000; border-bottom: 2px dashed #000; padding: 3px 0; margin: 5px 0; font-size: 9px;">
            <strong>فاتورة ضريبية مبسطة</strong><br>
            <span style="font-size: 7px;">Simplified Tax Invoice</span>
        </div>
        
        {{-- Invoice Details --}}
        <div style="font-size: 8px; margin-bottom: 5px;">
            <div style="display: flex; justify-content: space-between;">
                <span>رقم الفاتورة:</span>
                <strong>{{ $sale->invoiceNumber }}</strong>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>التاريخ:</span>
                <span>{{ \Carbon\Carbon::parse($sale->saleDate)->format('Y/m/d h:i A') }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>البائع:</span>
                <span>{{ $sale->user->name }}</span>
            </div>
            <div style="display: flex; justify-content: space-between;">
                <span>المشتري:</span>
                <span>{{ $sale->party->name ?? 'Cash' }}</span>
            </div>
        </div>
        
        {{-- Products Table --}}
        <table style="width: 100%; font-size: 7px; border-collapse: collapse; margin: 5px 0;">
            <thead>
                <tr style="border-top: 2px solid #000; border-bottom: 1px solid #000;">
                    <th style="text-align: right; padding: 2px;">المنتج</th>
                    <th style="text-align: center; padding: 2px; width: 12%;">الكمية</th>
                    <th style="text-align: right; padding: 2px; width: 18%;">السعر</th>
                    <th style="text-align: right; padding: 2px; width: 15%;">الضريبة</th>
                    <th style="text-align: right; padding: 2px; width: 18%;">المجموع</th>
                </tr>
            </thead>
            <tbody>
                @php
                // الحصول على نسبة الضريبة من قاعدة البيانات
                $vatRate = $sale->vat ? ($sale->vat->rate / 100) : 0.15;
                $vatPercent = $sale->vat ? $sale->vat->rate : 15;
                $productsTotal = 0;
                $totalTaxAmount = 0;
                @endphp
                @foreach ($sale->details as $detail)
                @php
                $itemPrice = $detail->price ?? 0;
                $itemQty = $detail->quantities ?? 0;
                $itemSubtotal = $itemPrice * $itemQty;
                // حساب الضريبة باستخدام النسبة من قاعدة البيانات
                $itemTax = $itemSubtotal * $vatRate;
                $itemTotal = $itemSubtotal + $itemTax;
                $productsTotal += $itemSubtotal;
                $totalTaxAmount += $itemTax;
                @endphp
                <tr style="border-bottom: 1px dashed #ccc;">
                    <td style="text-align: right; padding: 2px;">{{ $detail->product->productName ?? '' }}</td>
                    <td style="text-align: center; padding: 2px;">{{ $itemQty }}</td>
                    <td style="text-align: right; padding: 2px;">{{ currency_format($itemPrice, currency: business_currency()) }}</td>
                    <td style="text-align: right; padding: 2px; font-size: 6px;">{{ currency_format($itemTax, currency: business_currency()) }}<br>({{ $vatPercent }}%)</td>
                    <td style="text-align: right; padding: 2px;">{{ currency_format($itemTotal, currency: business_currency()) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{-- Totals --}}
        <div style="font-size: 8px; border-top: 2px dashed #000; padding-top: 5px;">
            @php
            $discountAmount = $sale->discountAmount ?? 0;
            $shippingCharge = $sale->shipping_charge ?? 0;
            // الإجمالي قبل الضريبة (المنتجات - الخصم + التوصيل)
            $subtotalBeforeVat = $productsTotal - $discountAmount + $shippingCharge;
            // الضريبة على الإجمالي قبل الضريبة
            $vatAmount = $subtotalBeforeVat * $vatRate;
            // الإجمالي النهائي شامل الضريبة
            $totalWithVat = $subtotalBeforeVat + $vatAmount;
            @endphp
            
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>الإجمالي الفرعي:</span>
                <span>{{ currency_format($productsTotal, currency: business_currency()) }}</span>
            </div>
            
            @if($discountAmount > 0)
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>الخصم:</span>
                <span>-{{ currency_format($discountAmount, currency: business_currency()) }}</span>
            </div>
            @endif
            
            @if($shippingCharge > 0)
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>قيمة التوصيل:</span>
                <span>{{ currency_format($shippingCharge, currency: business_currency()) }}</span>
            </div>
            @endif
            
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>الإجمالي قبل الضريبة:</span>
                <span>{{ currency_format($subtotalBeforeVat, currency: business_currency()) }}</span>
            </div>
            
            @if($vatAmount > 0)
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>ضريبة القيمة المضافة ({{ $vatPercent }}%):</span>
                <span>{{ currency_format($vatAmount, currency: business_currency()) }}</span>
            </div>
            @endif
            
            <div style="display: flex; justify-content: space-between; padding: 5px 0; margin-top: 3px; border-top: 2px solid #000; font-size: 10px; font-weight: bold;">
                <span>الإجمالي شامل الضريبة / Total:</span>
                <span>{{ currency_format($totalWithVat, currency: business_currency()) }}</span>
            </div>
        </div>
        
        {{-- QR Code --}}
        <div style="text-align: center; margin: 10px 0;">
            <div style="display: inline-block;">
                @php
                $sellerName = $sale->business->companyName ?? '';
                $vatRegistrationNumber = $sale->business->vat_no ?? '';
                $timestamp = $sale->created_at ? \Carbon\Carbon::parse($sale->created_at)->toIso8601String() : \Carbon\Carbon::now()->toIso8601String();
                $invoiceTotal = $sale->totalAmount ?? 0;
                $vatTotal = $sale->vat_amount ?? $sale->tax_amount ?? 0;
                $xmlHash = $sale->invoice_hash ?? null;
                $ecdsaSignature = $sale->cryptographic_stamp ?? null;
                $publicKey = $sale->business->zatca_setting['public_key'] ?? null;
                $zatcaQrContent = generateZatcaQrCode($sellerName, $vatRegistrationNumber, $timestamp, $invoiceTotal, $vatTotal, $xmlHash, $ecdsaSignature, $publicKey);
                @endphp
                {!! QrCode::size(100)->generate($zatcaQrContent) !!}
            </div>
        </div>
        
        {{-- Footer --}}
        <div style="text-align: center; font-size: 7px; margin-top: 5px; border-top: 2px dashed #000; padding-top: 5px;">
            <p style="margin: 2px 0;">{{ get_business_option('business-settings')['gratitude_message'] ?? 'شكراً لزيارتكم' }}</p>
            @if (!empty(get_business_option('business-settings')['note']))
            <p style="margin: 2px 0;">{{ get_business_option('business-settings')['note'] ?? '' }}</p>
            @endif
        </div>
        
    </div>
</div>

@push('js')
<script>
    $(document).ready(function() {
        window.print();
    })
</script>
@endpush
