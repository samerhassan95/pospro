<div class="invoice-container">
    <div class="invoice-content">
        {{-- Print Header --}}

        <div class="row py-2 d-flex align-items-start justify-content-between border-bottom print-container d-print-none">

            <div class="col-md-6 d-flex align-items-center p-2">
                <span class="Money-Receipt">Simplified Tax Invoice / فاتورة ضريبية مبسطة</span>
            </div>

            <div class="col-md-6 d-flex justify-content-end align-items-end">
                <div class="d-flex gap-2">

                    <form action="{{ route('business.sales.mail', ['sale_id' => $sale->id]) }}" method="POST"
                        class="ajaxform_instant_reload ">
                        @csrf

                        <button type="submit" class="btn  custom-print-btn"><img class="w-10 h-10"
                                src="{{ asset('assets/img/email.svg') }}"><span class="text-white pl-1">Email</span>
                        </button>
                    </form>
                    <button class="btn btn-dark d-flex align-items-center gap-2" onclick="copyPaymentLink('{{ route('invoice.show', $sale->uuid) }}')">
                        <i class="fas fa-link"></i>
                        <span>{{ __('Pay Link') }}</span>
                    </button>
                    <a class="pdf-btn print-btn" href="{{ route('business.sales.pdf', ['sale_id' => $sale->id]) }}">
                        <img class="w-10 h-10" src="{{ asset('assets/img/pdf.svg') }}">
                        PDF
                    </a>
                    <a class="print-btn-2 print-btn" onclick="window.print()"><img class="w-10 h-10" src="{{ asset('assets/img/print.svg') }}">Print</a>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-between align-items-start gap-3 print-logo-container pt-4 pb-2">
            {{-- Left Side: Logo and Company Info --}}
            <div class="d-flex align-items-center gap-3 logo pdf-logo">
                <img class="invoice-logo"
                    src="{{ asset(get_business_option('business-settings')['invoice_logo'] ?? 'assets/images/default.svg') ?? '' }}"
                    style="max-width: 150px; height: auto;"
                    alt="Logo">
                <div>
                    <h2 class="mb-0 fw-bold text-dark">{{ $sale->business->companyName ?? '' }}</h2>
                    <p class="mb-0 text-muted small">{{ $sale->business->address ?? '' }}</p>
                </div>
            </div>

            {{-- Right Side: QR Code and Invoice Title --}}
            <div class="d-flex flex-column align-items-end gap-2">
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
                <div class="qr-code-wrapper p-1 border rounded bg-white shadow-sm" style="width: 130px; height: 130px;">
                    {!! QrCode::size(120)->margin(1)->generate($zatcaQrContent) !!}
                </div>
                <div class="text-end mt-1">
                    <h4 class="text-uppercase fw-bold text-primary mb-0" style="letter-spacing: 2px;">{{ __('INVOICE') }}</h4>
                    <span class="text-muted small">Tax Invoice / فاتورة ضريبية</span>
                </div>
            </div>
        </div>
        <div class="row mt-4 mb-4">
            <div class="col-6">
                <div class="p-3 border rounded h-100 bg-light-subtle">
                    <h6 class="text-uppercase fw-bold text-muted mb-2 small">{{ __('Bill To') }} / العميل</h6>
                    <h5 class="mb-1 fw-bold">{{ $sale->party->name ?? 'Guest' }}</h5>
                    <p class="mb-1 small text-muted"><i class="fas fa-phone-alt me-1"></i> {{ $sale->party->phone ?? '---' }}</p>
                    <p class="mb-0 small text-muted"><i class="fas fa-map-marker-alt me-1"></i> {{ $sale->party->address ?? '---' }}</p>
                </div>
            </div>
            <div class="col-6 text-end">
                <div class="p-3 border rounded h-100 bg-light-subtle">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small fw-bold">{{ __('Invoice No') }}:</span>
                        <span class="fw-bold text-dark">{{ $sale->invoiceNumber ?? '' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small fw-bold">{{ __('Date') }}:</span>
                        <span class="fw-bold text-dark">{{ formatted_date($sale->saleDate ?? '') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small fw-bold">{{ $sale->business->vat_name ?? 'VAT No' }}:</span>
                        <span class="fw-bold text-dark">{{ $sale->business->vat_no ?? '---' }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted small fw-bold">{{ __('Sells By') }}:</span>
                        <span class="fw-bold text-dark">{{ $sale->user->role != 'staff' ? 'Admin' : $sale->user->name }}</span>
                    </div>
                </div>
            </div>
        </div>

        @if (!$sale_returns->isEmpty())
            {{-- Sales --}}
            <div class="table-responsive mt-2">
                <table class="table table-bordered align-middle">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center py-3" style="width: 50px;">{{ __('SL') }}</th>
                            <th class="text-start py-3">{{ __('Description') }}</th>
                            <th class="text-center py-3" style="width: 100px;">{{ __('Qty') }}</th>
                            <th class="text-end py-3" style="width: 120px;">{{ __('Unit Price') }}</th>
                            <th class="text-end py-3" style="width: 120px;">{{ __('Total') }}</th>
                        </tr>
                    </thead>
                    @php
                        $subtotal = 0;
                    @endphp
                    <tbody class="in-table-body-container">
                        @foreach ($sale->details as $detail)
                            @php
                                $productTotal = ($detail->price ?? 0) * ($detail->quantities ?? 0);
                                $subtotal += $productTotal;
                            @endphp
                            <tr class="in-table-body">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-start">
                                    {{ ($detail->product->productName ?? '') . (!empty($detail->stock->batch_no) ? ' (' . $detail->stock->batch_no . ')' : '') }}
                                </td>
                                <td class="text-center">{{ $detail->quantities ?? '' }}</td>
                                <td class= "text-end">{{ currency_format($detail->price ?? 0, currency: business_currency()) }}</td>
                                <td class="text-end">{{ currency_format($productTotal, currency: business_currency()) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex align-items-center justify-content-between position-relative">
                <div>
                    <table class="table">
                        <tbody>
                            <tr class="in-table-row">
                                <td class="text-start"></td>
                            </tr>
                            <tr class="in-table-row">
                                <td class="text-start"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div>
                    <table class="table">
                        <tbody>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Subtotal') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">{{ currency_format($subtotal, currency: business_currency()) }}
                                </td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Vat') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format($sale->vat_amount, currency: business_currency()) }}</td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Shipping Charge') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format($sale->shipping_charge, currency: business_currency()) }}
                                </td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Discount') }}
                                    @if ($sale->discount_type == 'percent')
                                        ({{ $sale->discount_percent }}%)
                                    @endif
                                </td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format($sale->discountAmount + $total_discount, currency: business_currency()) }}
                                </td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Total Amount') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format($subtotal + $sale->vat_amount - ($sale->discountAmount + $total_discount) + $sale->shipping_charge + $sale->rounding_amount, currency: business_currency()) }}
                                </td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Rounding(+/-)') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format(abs($sale->rounding_amount), currency: business_currency()) }}
                                </td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end total-amound">{{ __('Total Payable') }}</td>
                                <td class="text-end total-amound">:</td>
                                <td class="text-end total-amound">
                                    {{ currency_format($subtotal + $sale->vat_amount - ($sale->discountAmount + $total_discount) + $sale->shipping_charge, currency: business_currency()) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Sale Return --}}
            <div class="custom-invoice-table">
                <table class="table table-striped">
                    <thead>
                        <tr class="in-table-header">
                            <th class="head-red text-center">{{ __('SL') }}</th>
                            <th class="head-red text-start">{{ __('Date') }}</th>
                            <th class="head-black text-start">{{ __('Returned Item') }}</th>
                            <th class="head-black text-center">{{ __('Quantity') }}</th>
                            <th class="head-black text-end">{{ __('Total Amount') }}</th>
                        </tr>
                    </thead>
                    @php $total_return_amount = 0; @endphp
                    <tbody class="in-table-body-container">
                        @foreach ($sale_returns as $key => $return)
                            @foreach ($return->details as $detail)
                                @php
                                    $total_return_amount += $detail->return_amount ?? 0;
                                @endphp
                                <tr class="in-table-body">
                                    <td class="text-center">{{ $loop->iteration }}</td>
                                    <td class="text-start">{{ formatted_date($return->return_date) }}</td>
                                    <td class="text-start">
                                        {{ $detail->saleDetail->product->productName ?? '' }}
                                        {{ $detail->saleDetail?->stock?->batch_no ? '(' . $detail->saleDetail?->stock?->batch_no . ')' : '' }}
                                    </td>
                                    <td class="text-center">{{ $detail->return_qty ?? 0 }}</td>
                                    <td class="text-end">
                                        {{ currency_format($detail->return_amount ?? 0, currency: business_currency()) }}
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="d-flex align-items-center justify-content-between position-relative">
                <h2 class="word-amount">{{ amountInWords($total_return_amount) }}</h2>
                <div>
                    <table class="table">
                        <tbody>
                            <tr class="in-table-row">
                                <td class="text-start"></td>
                            </tr>
                            <tr class="in-table-row">
                                <td class="text-start"></td>
                            </tr>
                            <tr class="in-table-row">
                                <td class="text-start paid-by">{{ __('Paid by') }} :
                                    {{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}
                                </td>
                            </tr>
                            @if(!empty($sale->meta['note']))
                                <tr class="in-table-row">
                                    <td class="text-start paid-by">{{ __('Note') }} :
                                        {{ $sale->meta['note'] ?? '' }}
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div>
                    <table class="table">
                        <tbody>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Total Return Amount') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format($total_return_amount, currency: business_currency()) }}</td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end total-amound">{{ __('Payable Amount') }}</td>
                                <td class="text-end total-amound">:</td>
                                <td class="text-end total-amound">
                                    {{ currency_format($sale->totalAmount, currency: business_currency()) }}</td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Paid Amount') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format($sale->paidAmount, currency: business_currency()) }}</td>
                            </tr>
                            <tr class="in-table-row-bottom">
                                <td class="text-end">{{ __('Due') }}</td>
                                <td class="text-end">:</td>
                                <td class="text-end">
                                    {{ currency_format($sale->dueAmount, currency: business_currency()) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            {{-- Sales --}}
            <div class="custom-invoice-table">
                <table class="table table-striped">
                    <thead>
                        <tr class="in-table-header">
                            <th class="head-red text-center">{{ __('SL') }}</th>
                            <th class="head-red text-start">{{ __('Item') }}</th>
                            <th class="head-black text-center">{{ __('Quantity') }}</th>
                            <th class="head-black text-end">{{ __('Unit Price') }}</th>
                            <th class="head-black text-end">{{ __('Total Price') }}</th>
                        </tr>
                    </thead>
                    @php $subtotal = 0; @endphp
                    <tbody class="in-table-body-container">
                        @foreach ($sale->details as $detail)
                            @php
                                $productTotal = ($detail->price ?? 0) * ($detail->quantities ?? 0);
                                $subtotal += $productTotal;
                            @endphp
                            <tr class="in-table-body">
                                <td class="text-center">{{ $loop->iteration }}</td>
                                <td class="text-start">
                                    {{ ($detail->product->productName ?? '') . (!empty($detail->stock?->batch_no) ? ' (' . $detail->stock?->batch_no . ')' : '') }}
                                </td>
                                <td class="text-center">{{ $detail->quantities ?? '' }}</td>
                                <td class="text-end">{{ currency_format($detail->price ?? 0, currency: business_currency()) }}</td>
                                <td class="text-end">{{ currency_format($productTotal, currency: business_currency()) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-7">
                    <div class="p-3 bg-light rounded shadow-sm">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">{{ __('Payment Info') }}</h6>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted small">{{ __('Paid by') }}:</span>
                            <span class="fw-medium">{{ $sale->payment_type_id != null ? $sale->payment_type->name ?? '' : $sale->paymentType }}</span>
                        </div>
                        @if(!empty($sale->meta['note']))
                            <div class="mt-2 pt-2 border-top">
                                <span class="text-muted small d-block">{{ __('Note') }}:</span>
                                <span class="small text-dark italic">{{ $sale->meta['note'] }}</span>
                            </div>
                        @endif
                        <div class="mt-3">
                            <p class="small text-muted italic mb-0"><strong>In Words:</strong> {{ amountInWords($sale->totalAmount) }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-5">
                    <div class="invoice-totals">
                        <div class="d-flex justify-content-between py-1 px-2">
                            <span class="text-muted">{{ __('Subtotal') }}</span>
                            <span class="fw-bold">{{ currency_format($subtotal, currency: business_currency()) }}</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 px-2">
                            <span class="text-muted">{{ __('Vat') }}</span>
                            <span class="fw-bold">{{ currency_format($sale->vat_amount, currency: business_currency()) }}</span>
                        </div>
                        @if($sale->shipping_charge > 0)
                            <div class="d-flex justify-content-between py-1 px-2">
                                <span class="text-muted">{{ __('Shipping') }}</span>
                                <span class="fw-bold">{{ currency_format($sale->shipping_charge, currency: business_currency()) }}</span>
                            </div>
                        @endif
                        @if($sale->discountAmount > 0)
                            <div class="d-flex justify-content-between py-1 px-2 text-danger">
                                <span class="">{{ __('Discount') }}</span>
                                <span class="fw-bold">-{{ currency_format($sale->discountAmount, currency: business_currency()) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between py-2 px-2 mt-2 bg-primary text-white rounded shadow-sm">
                            <span class="fw-bold h5 mb-0">{{ __('Total Payable') }}</span>
                            <span class="fw-bold h5 mb-0">{{ currency_format($sale->totalAmount, currency: business_currency()) }}</span>
                        </div>
                        
                        <div class="mt-3 text-end px-2">
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-muted">{{ __('Received') }}:</span>
                                <span class="text-success fw-bold">{{ currency_format($sale->paidAmount + $sale->change_amount, currency: business_currency()) }}</span>
                            </div>
                            @if($sale->change_amount > 0)
                                <div class="d-flex justify-content-between mb-1 small text-info">
                                    <span>{{ __('Change') }}:</span>
                                    <span class="fw-bold">{{ currency_format($sale->change_amount, currency: business_currency()) }}</span>
                                </div>
                            @elseif($sale->dueAmount > 0)
                                <div class="d-flex justify-content-between mb-1 small text-danger">
                                    <span>{{ __('Due Amount') }}:</span>
                                    <span class="fw-bold">{{ currency_format($sale->dueAmount, currency: business_currency()) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="pdf-footer">
            <div class="in-signature-container d-flex align-items-center justify-content-between my-3 px-2">
                <div class="in-signature">
                    <hr class="in-hr">
                    <h4>{{ __('Customer Signature') }}</h4>
                </div>
                <div class="in-signature">
                    <hr class="in-hr">
                    <h4>{{ __('Authorized Signature') }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
