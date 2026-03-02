<div class="invoice-container-sm">
    <div class="invoice-content invoice-content-size">

        <div class="invoice-logo" style="margin: 5px 0;">
            <img src="{{ asset(get_business_option('business-settings')['invoice_logo'] ?? 'assets/images/default.svg') ?? '' }}"
                alt="Logo" style="max-width: 80px; max-height: 60px;">
        </div>
        <div style="margin: 5px 0;">
            <h4 class="company-name" style="font-size: 14px; margin: 3px 0;">{{ $sale->business->companyName ?? 'Pos Pro' }}</h4>
            <div class="company-info" style="font-size: 8px; text-align: center; line-height: 1.3;">
                @if($sale->invoice_type === 'b2b')
                {{-- B2B Seller Details --}}
                @if($sale->business->building_number || $sale->business->street_name)
                <p>
                    @if($sale->business->building_number)
                    {{__('Building')}}: {{ $sale->business->building_number }},
                    @endif
                    @if($sale->business->street_name)
                    {{ $sale->business->street_name }}
                    @endif
                </p>
                @endif
                @if($sale->business->district || $sale->business->city)
                <p>
                    @if($sale->business->district)
                    {{ $sale->business->district }},
                    @endif
                    @if($sale->business->city)
                    {{ $sale->business->city }}
                    @endif
                    @if($sale->business->postal_code)
                    - {{ $sale->business->postal_code }}
                    @endif
                </p>
                @endif
                @else
                {{-- B2C Seller Details --}}
                @if($sale->business->address || $sale->business->building_number)
                <p>
                    @if($sale->business->building_number || $sale->business->street_name)
                        {{ $sale->business->building_number }}، {{ $sale->business->street_name }}، 
                        {{ $sale->business->district }}، {{ $sale->business->city }}
                    @else
                        {{ $sale->business->address ?? '' }}
                    @endif
                </p>
                @endif
                @endif
                @if($sale->business->phoneNumber)
                <p>{{__('Mobile')}} : {{ $sale->business->phoneNumber }}</p>
                @endif
                @if (!empty($sale->business->vat_no))
                <p>{{ __('VAT Number') }} / الرقم الضريبي: {{ $sale->business->vat_no }}</p>
                @endif
            </div>
        </div>
        <h3 class="invoice-title my-1" style="text-align: center; font-size: 11px; margin: 5px 0;">
            @if($sale->invoice_type === 'b2b')
            <strong>Tax Invoice / فاتورة ضريبية</strong>
            <br>
            <small style="font-size:7px;">(تصدر غالباً بين منشأة ومنشأة أخرى)</small>
            @else
            <strong>فاتورة ضريبية مبسطة</strong><br>
            <small style="font-size:8px;">Simplified Tax Invoice</small>
            @endif
        </h3>

        @if($sale->invoice_type === 'b2b')
        {{-- B2B Invoice Details --}}
        <div style="margin: 8px 0; padding: 5px; border: 1px solid #333; font-size: 9px;">
            <p style="margin: 2px 0;"><strong>{{__('Serial Number')}} / الرقم التسلسلي:</strong> {{ $sale->invoiceNumber ?? '' }}</p>
            <p style="margin: 2px 0;"><strong>{{__('Date')}} / التاريخ:</strong> {{ formatted_date($sale->saleDate ?? '') }} {{ formatted_time($sale->saleDate ?? '') }}</p>
            @if($sale->delivery_type)
            <p style="margin: 2px 0;"><strong>{{__('Order Type')}} / نوع الطلب:</strong> 
                @if($sale->delivery_type == 'delivery')
                    {{__('Delivery')}} / توصيل
                @elseif($sale->delivery_type == 'pre-order')
                    {{__('Pre-order')}} / طلب مسبق
                @else
                    {{__('Takeaway')}} / استلام
                @endif
            </p>
            @endif
        </div>

        {{-- Seller Information --}}
        <div style="margin: 8px 0; padding: 5px; border: 1px solid #333; background: #f0f0f0; font-size: 8px;">
            <p style="margin: 2px 0; font-weight: bold; text-align: center; background: #1565C0; color: white; padding: 2px; margin: -5px -5px 3px -5px;">{{__('Seller Information')}} / معلومات البائع</p>
            <p style="margin: 2px 0;"><strong>{{__('Seller Name')}} / اسم البائع:</strong> {{ $sale->business->companyName ?? '---' }}</p>
            <p style="margin: 2px 0;"><strong>{{__('Seller Address')}} / عنوان البائع:</strong>
                @if($sale->business->building_number || $sale->business->street_name || $sale->business->district || $sale->business->city)
                {{ $sale->business->building_number ?? '' }}{{ $sale->business->building_number && $sale->business->street_name ? ', ' : '' }}{{ $sale->business->street_name ?? '' }}{{ ($sale->business->building_number || $sale->business->street_name) && ($sale->business->district || $sale->business->city) ? ', ' : '' }}{{ $sale->business->district ?? '' }}{{ $sale->business->district && $sale->business->city ? ', ' : '' }}{{ $sale->business->city ?? '' }}{{ $sale->business->postal_code ? ' - ' . $sale->business->postal_code : '' }}
                @else
                ---
                @endif
            </p>
            <p style="margin: 2px 0;"><strong>{{__('VAT Registration Number')}} / رقم تسجيل ضريبة القيمة المضافة للبائع:</strong> {{ $sale->business->vat_no ?? '---' }}</p>
            <p style="margin: 2px 0;"><strong>{{__('Commercial Registration Number')}} / رقم السجل التجاري:</strong> {{ $sale->business->commercial_registration ?? '---' }}</p>
        </div>

        {{-- Buyer Information --}}
        <div style="margin: 8px 0; padding: 5px; border: 1px solid #333; background: #f0f0f0; font-size: 8px;">
            <p style="margin: 2px 0; font-weight: bold; text-align: center; background: #1565C0; color: white; padding: 2px; margin: -5px -5px 3px -5px;">{{__('Buyer Information')}} / معلومات المشتري</p>
            <p style="margin: 2px 0;"><strong>{{__('Buyer Name')}} / اسم المشتري:</strong> {{ $sale->party->name ?? 'Guest' }}</p>
            <p style="margin: 2px 0;"><strong>{{__('Buyer Address')}} / عنوان المشتري:</strong>
                @if($sale->party && ($sale->party->building_number || $sale->party->street_name || $sale->party->district || $sale->party->city))
                {{ $sale->party->building_number ?? '' }}{{ $sale->party->building_number && $sale->party->street_name ? ', ' : '' }}{{ $sale->party->street_name ?? '' }}{{ ($sale->party->building_number || $sale->party->street_name) && ($sale->party->district || $sale->party->city) ? ', ' : '' }}{{ $sale->party->district ?? '' }}{{ $sale->party->district && $sale->party->city ? ', ' : '' }}{{ $sale->party->city ?? '' }}{{ $sale->party->postal_code ? ' - ' . $sale->party->postal_code : '' }}
                @else
                ---
                @endif
            </p>
            <p style="margin: 2px 0;"><strong>{{__('VAT Registration Number')}} / رقم تسجيل ضريبة القيمة المضافة للمشتري:</strong> {{ $sale->party->vat_number ?? '---' }}</p>
            <p style="margin: 2px 0;"><strong>{{__('Commercial Registration Number')}} / رقم السجل التجاري:</strong> {{ $sale->party->commercial_registration ?? '---' }}</p>
            @if($sale->delivery_type)
            <p style="margin: 2px 0;"><strong>{{__('Order Type')}} / نوع الطلب:</strong> 
                @if($sale->delivery_type == 'delivery')
                    {{__('Delivery')}} / توصيل
                @elseif($sale->delivery_type == 'pre-order')
                    {{__('Pre-order')}} / طلب مسبق
                @else
                    {{__('Takeaway')}} / استلام
                @endif
            </p>
            @endif
        </div>
        @else
        {{-- B2C Invoice Details - Compact Format --}}
        <div style="text-align: center; margin: 5px 0; padding: 5px 0; border-top: 2px dashed #000; border-bottom: 2px dashed #000; font-size: 9px;">
            <p style="margin: 2px 0;"><strong>{{ __('Invoice') }}:</strong> {{ $sale->invoiceNumber ?? '' }}</p>
            <p style="margin: 2px 0;">{{ \Carbon\Carbon::parse($sale->saleDate)->format('Y/m/d h:i A') }}</p>
            <p style="margin: 2px 0;"><strong>{{ __('Sales By') }}:</strong> {{ $sale->user->name }}</p>
            <p style="margin: 2px 0;"><strong>{{ __('Customer') }}:</strong> {{ $sale->party->name ?? 'Cash' }}</p>
            @if($sale->party->phone)
            <p style="margin: 2px 0;"><strong>{{ __('Mobile') }}:</strong> {{ $sale->party->phone }}</p>
            @endif
            @if($sale->delivery_type)
            <p style="margin: 2px 0;"><strong>{{ __('Order Type') }}:</strong> 
                @if($sale->delivery_type == 'delivery')
                    {{ __('Delivery') }}
                @elseif($sale->delivery_type == 'pre-order')
                    {{ __('Pre-order') }}
                @else
                    {{ __('Takeaway') }}
                @endif
            </p>
            @endif
        </div>
        @endif
        @if (!$sale_returns->isEmpty())
        <table class="ph-invoice-table">
            <thead>
                <tr>
                    <th class="text-start table-sl">{{ __('SL') }}</th>
                    <th>{{ __('Product') }}</th>
                    <th>{{ __('QTY') }}</th>
                    <th>{{ __('U.Price') }}</th>
                    <th class="text-end">{{ __('Amount') }}</th>
                </tr>
            </thead>
            @php
            $subtotal = 0;
            @endphp
            <tbody>
                @foreach ($sale->details as $detail)
                @php
                $productTotal = ($detail->price ?? 0) * ($detail->quantities ?? 0);
                $subtotal += $productTotal;
                @endphp
                <tr>
                    <td class="text-start table-sl">{{ $loop->iteration }}</td>
                    <td>{{ $detail->product->productName ?? '' }}</td>
                    <td class="text-center">{{ $detail->quantities ?? '' }}</td>
                    <td class="text-center">
                        {!! currency_format($detail->price ?? 0, currency: business_currency()) !!}
                    </td>
                    <td class="text-end">
                        {!! currency_format($productTotal, currency: business_currency()) !!}
                    </td>
                </tr>
                @endforeach
                <tr>
                    <td class="total-due" colspan="2">
                        <div class="payment-type-container">
                            <h6 class="text-center">{{ __('Payment Type') }}:
                                {{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}
                            </h6>
                            <p class="text-center">{{ $sale->meta['notes'] ?? ($sale->meta['note'] ?? '') }}
                            </p>
                        </div>
                    </td>
                    <td colspan="3">
                        <div class="calculate-amount">
                            <div class="d-flex justify-content-between">
                                <p>{{ __('Sub-Total') }}:</p>
                                <p>{!! currency_format($subtotal, currency: business_currency()) !!}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>{{ __('Vat') }}:</p>
                                <p> {!! currency_format($sale->tax_amount, currency: business_currency()) !!}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between ">
                                <p>{{ __('Delivery charge') }}:</p>
                                <p>{!! currency_format($sale->shipping_charge, currency: business_currency()) !!}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>{{ __('Discount') }}
                                    @if ($sale->discount_type == 'percent')
                                    ({{ $sale->discount_percent }}%)
                                    @endif:
                                </p>
                                <p> {!! currency_format($sale->discountAmount + $total_discount, currency: business_currency()) !!}
                                </p>
                            </div>
                            <div class="in-border"></div>

                            <div class="d-flex justify-content-between total-amount">
                                <p>{{ __('Net Payable') }}:</p>
                                <p> {!! currency_format($subtotal + $sale->tax_amount - ($sale->discountAmount + $total_discount) + $sale->shipping_charge + $sale->rounding_amount, currency: business_currency()) !!}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between paid">
                                <p>{{ __('Total Payable') }}:</p>
                                <p> {!! currency_format($subtotal + $sale->tax_amount - ($sale->discountAmount + $total_discount) + $sale->shipping_charge, currency: business_currency()) !!}
                                </p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table class="ph-invoice-table mt-2">
            <thead>
                <tr>
                    <th class="invoice-th">{{ __('Date') }}</th>
                    <th class="invoice-th">{{ __('Return Product') }}</th>
                    <th class="invoice-th">{{ __('QTY') }}</th>
                    <th class="invoice-th text-end">{{ __('Amount') }}</th>
                </tr>
            </thead>
            @php $total_return_amount = 0; @endphp
            <tbody>
                @foreach ($sale_returns as $key => $return)
                @foreach ($return->details as $detail)
                @php
                $total_return_amount += $detail->return_amount ?? 0;
                @endphp
                <tr>

                    <td class="text-start">{{ formatted_date($return->return_date) }}</td>
                    <td>{{ $detail->saleDetail->product->productName ?? '' }}</td>
                    <td class="text-center">{{ $detail->return_qty ?? 0 }}</td>
                    <td class="text-end">
                        {!! currency_format($detail->return_amount ?? 0, currency: business_currency()) !!}
                    </td>
                </tr>
                @endforeach
                @endforeach
                <tr>
                    <td class="total-due" colspan="2">
                        <div class="payment-type-container">
                            <h6 class="text-center">{{ __('Payment Type') }}:
                                {{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}
                            </h6>
                            <p class="text-center">{{ $sale->meta['notes'] ?? ($sale->meta['note'] ?? '') }}
                            </p>
                        </div>
                    </td>
                    <td colspan="3">
                        <div class="calculate-amount">
                            <div class="d-flex justify-content-between">
                                <p>{{ __('Total Return') }}:</p>
                                <p>{!! currency_format($total_return_amount, currency: business_currency()) !!}
                                </p>
                            </div>
                            <div class="in-border"></div>

                            <div class="d-flex justify-content-between total-amount">
                                <p>{{ __('Payable') }}:</p>
                                <p>{!! currency_format($sale->totalAmount, currency: business_currency()) !!}
                                </p>
                            </div>
                            <div class="d-flex justify-content-between paid">
                                <p>{{ __('Paid') }}:</p>
                                <p>{!! currency_format($sale->paidAmount, currency: business_currency()) !!}</p>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>{{ __('Due') }}:</p>
                                <p>{!! currency_format($sale->dueAmount, currency: business_currency()) !!}</p>
                            </div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        @else
        @if($sale->invoice_type === 'b2b')
        {{-- B2B Products Table - Matching ZATCA Sample --}}
        <table class="ph-invoice-table" style="width: 100%; font-size: 8px; border-collapse: collapse;">
            <thead>
                <tr style="background: #1565C0; color: white;">
                    <th style="padding: 3px; text-align: left; border: 1px solid #333;">{{__('Product')}}<br>المنتج</th>
                    <th style="padding: 3px; text-align: right; border: 1px solid #333;">{{__('Unit Price')}}<br>سعر الوحدة</th>
                    <th style="padding: 3px; text-align: center; border: 1px solid #333;">{{__('Qty')}}<br>الكمية</th>
                    <th style="padding: 3px; text-align: right; border: 1px solid #333;">{{__('Subtotal')}}<br>المجموع الفرعي</th>
                    <th style="padding: 3px; text-align: center; border: 1px solid #333;">{{__('Tax')}}%<br>نسبة الضريبة</th>
                    <th style="padding: 3px; text-align: right; border: 1px solid #333;">{{__('Tax Value')}}<br>قيمة الضريبة</th>
                    <th style="padding: 3px; text-align: right; border: 1px solid #333;">{{__('Total')}}<br>المجموع شامل</th>
                </tr>
            </thead>
            <tbody>
                @php
                $subtotal = 0;
                $totalTax = 0;
                @endphp
                @foreach ($sale->details as $detail)
                @php
                $unitPrice = $detail->list_price ?? $detail->price ?? 0;
                $quantity = $detail->quantities ?? 0;
                $lineSubtotal = $unitPrice * $quantity;
                $vatRate = $sale->vat_percent ?? 15;
                $taxValue = $detail->tax_per_item ?? ($lineSubtotal * $vatRate / 100);
                $lineTotalWithTax = $lineSubtotal + $taxValue;

                $subtotal += $lineSubtotal;
                $totalTax += $taxValue;
                @endphp
                <tr>
                    <td style="padding: 2px; border: 1px solid #ddd;">{{ $detail->product->productName ?? '' }}</td>
                    <td style="padding: 2px; text-align: right; border: 1px solid #ddd;">{!! currency_format($unitPrice, currency: business_currency()) !!}</td>
                    <td style="padding: 2px; text-align: center; border: 1px solid #ddd;">{{ $quantity }}</td>
                    <td style="padding: 2px; text-align: right; border: 1px solid #ddd;">{!! currency_format($lineSubtotal, currency: business_currency()) !!}</td>
                    <td style="padding: 2px; text-align: center; border: 1px solid #ddd;">{{ $vatRate }}%</td>
                    <td style="padding: 2px; text-align: right; border: 1px solid #ddd;">{!! currency_format($taxValue, currency: business_currency()) !!}</td>
                    <td style="padding: 2px; text-align: right; border: 1px solid #ddd;">{!! currency_format($lineTotalWithTax, currency: business_currency()) !!}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        {{-- B2B Totals - Matching ZATCA Sample --}}
        <div style="margin-top: 8px; text-align: right; font-size: 9px;">
            <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #ddd;">
                <span><strong>{{__('Subtotal')}} / الإجمالي الفرعي:</strong></span>
                <span><strong>{!! currency_format($subtotal, currency: business_currency()) !!}</strong></span>
            </div>
            @if($sale->discountAmount > 0)
            <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #ddd;">
                <span><strong>{{__('Discount')}} / الخصم:</strong></span>
                <span><strong>-{!! currency_format($sale->discountAmount, currency: business_currency()) !!}</strong></span>
            </div>
            @endif
            @if($sale->shipping_charge > 0)
            <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #ddd;">
                <span><strong>{{__('Shipping')}} / الشحن:</strong></span>
                <span><strong>{!! currency_format($sale->shipping_charge, currency: business_currency()) !!}</strong></span>
            </div>
            @endif
            @php
                $taxableAmount = $subtotal - ($sale->discountAmount ?? 0) + ($sale->shipping_charge ?? 0);
                $vatPercent = $sale->vat ? $sale->vat->rate : 15;
            @endphp
            <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #ddd;">
                <span><strong>{{__('Taxable Amount')}} / المبلغ الخاضع للضريبة:</strong></span>
                <span><strong>{!! currency_format($taxableAmount, currency: business_currency()) !!}</strong></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 3px 0; border-bottom: 1px solid #ddd;">
                <span><strong>{{__('VAT')}} ({{ $vatPercent }}%) / الضريبة:</strong></span>
                <span><strong>{!! currency_format($sale->vat_amount, currency: business_currency()) !!}</strong></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 5px 0; background: #1565C0; color: white; font-weight: bold; margin-top: 3px;">
                <span>{{__('Total with Tax')}} / المجموع شامل الضريبة:</span>
                <span>{!! currency_format($sale->totalAmount, currency: business_currency()) !!}</span>
            </div>
        </div>
        @else
        {{-- B2C Products Table - Compact Format --}}
        <table class="ph-invoice-table" style="width: 100%; font-size: 8px; margin: 3px 0;">
            <thead>
                <tr style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                    <th style="padding: 3px; text-align: right;">الصنف</th>
                    <th style="padding: 3px; text-align: center;">الكمية</th>
                    <th style="padding: 3px; text-align: right;">السعر</th>
                    <th style="padding: 3px; text-align: right;">المجموع</th>
                </tr>
            </thead>

            @php
            $subtotal = 0;
            @endphp
            <tbody>
                @foreach ($sale->details as $detail)
                @php
                $productTotal = ($detail->price ?? 0) * ($detail->quantities ?? 0);
                $subtotal += $productTotal;
                @endphp
                <tr style="border-bottom: 1px dashed #ccc;">
                    <td style="padding: 3px; text-align: right;">{{ $detail->product->productName ?? '' }}</td>
                    <td style="padding: 3px; text-align: center;">{{ $detail->quantities ?? '' }}</td>
                    <td style="padding: 3px; text-align: right;">
                        {!! currency_format($detail->price ?? 0, currency: business_currency()) !!}
                    </td>
                    <td style="padding: 3px; text-align: right;">
                        {!! currency_format($productTotal, currency: business_currency()) !!}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        {{-- B2C Totals - Compact Format --}}
        <div style="margin: 5px 0; padding: 5px 0; border-top: 2px dashed #000; font-size: 9px;">
            @php
                $totalItems = $sale->details->sum('quantities');
                $discountAmount = $sale->discountAmount ?? 0;
                $shippingCharge = $sale->shipping_charge ?? 0;
                $vatPercent = $sale->vat ? $sale->vat->rate : 15;
                // Taxable base: Items Subtotal - Discount + Shipping
                $taxableAmount = $subtotal - $discountAmount + $shippingCharge;
                $vatAmount = $sale->vat_amount ?? 0;
                $totalWithVat = $sale->totalAmount;
            @endphp
            
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>عدد المنتجات:</span>
                <span><strong>{{ $totalItems }}</strong></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>الإجمالي الفرعي:</span>
                <span><strong>{!! currency_format($subtotal, currency: business_currency()) !!}</strong></span>
            </div>
            @if($discountAmount > 0)
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>الخصم:</span>
                <span><strong>-{!! currency_format($discountAmount, currency: business_currency()) !!}</strong></span>
            </div>
            @endif
            @if($shippingCharge > 0)
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>الشحن:</span>
                <span><strong>{!! currency_format($shippingCharge, currency: business_currency()) !!}</strong></span>
            </div>
            @endif
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>الإجمالي الخاضع للضريبة:</span>
                <span><strong>{!! currency_format($taxableAmount, currency: business_currency()) !!}</strong></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 1px 0;">
                <span>ضريبة القيمة المضافة ({{ $vatPercent }}%):</span>
                <span><strong>{!! currency_format($vatAmount, currency: business_currency()) !!}</strong></span>
            </div>
            <div style="display: flex; justify-content: space-between; padding: 4px 0; margin-top: 2px; border-top: 2px solid #000; font-size: 11px;">
                <span><strong>الإجمالي / Total:</strong></span>
                <span><strong>{!! currency_format($totalWithVat, currency: business_currency()) !!}</strong></span>
            </div>
        </div>
        @endif
        @endif

        <div class="invoice-footer-sm mt-3">
            @if($sale->invoice_type === 'b2b')
            {{-- B2B Footer with QR Code - Matching ZATCA Sample --}}
            <div style="text-align: center; margin: 10px 0;">
                <div class="scanner justify-content-center d-flex" style="width: 120px; height: 120px; margin: 5px auto;">
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
                    {!! QrCode::size(120)->generate($zatcaQrContent) !!}
                </div>
                <p style="font-size: 7px; margin: 3px 0; font-weight: bold;">{{ __('QR Code') }}<br>رمز الاستجابة السريعة (QR Code)</p>
                <p style="font-size: 6px; margin: 2px 0; color: #666;">{{ __('Date and time of invoice issuance') }}<br>تاريخ و وقت إصدار الفاتورة</p>
            </div>
            @else
            {{-- B2C Footer --}}
            <h5>{{ get_business_option('business-settings')['gratitude_message'] ?? '' }}</h5>
            @if (!empty(get_business_option('business-settings')['note']))
            <p class="text-center note-pera">{{ get_business_option('business-settings')['note_label'] ?? '' }} :
                {{ get_business_option('business-settings')['note'] ?? '' }}
            </p>
            @endif
            <div class="scanner justify-content-center d-flex" style="width: 150px; height: 150px; margin: 10px auto;">
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
                {!! QrCode::size(150)->generate($zatcaQrContent) !!}
            </div>
            @endif
            <h6>{{ get_option('general')['admin_footer_text'] ?? '' }} <a href="{{ get_option('general')['admin_footer_link'] ?? '#' }}" target="_blank">{{ get_option('general')['admin_footer_link_text'] ?? '' }}</h6>
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