<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice') }} #{{ $sale->invoiceNumber }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Inter', sans-serif; }
        .invoice-card { max-width: 900px; margin: 40px auto; border: none; box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15); border-radius: 1rem; }
        .invoice-header { background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%); color: white; border-radius: 1rem 1rem 0 0; padding: 2.5rem; }
        .qr-code-bg { background: white; padding: 0.5rem; border-radius: 0.5rem; display: inline-block; }
        .table thead th { background: #f8f9fa; color: #6c757d; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.05em; border-top: none; }
        .total-section { background: #f8f9fa; border-radius: 0.5rem; padding: 1.5rem; }
        .pay-button { background: #000; color: #fff; border: none; padding: 1rem 2rem; border-radius: 0.5rem; font-weight: 600; width: 100%; transition: all 0.3s; }
        .pay-button:hover { background: #333; transform: translateY(-2px); }
        .status-badge { padding: 0.5rem 1rem; border-radius: 2rem; font-size: 0.875rem; font-weight: 600; }
        .status-paid { background: #d1e7dd; color: #0f5132; }
        .status-unpaid { background: #f8d7da; color: #842029; }
        @media print { .no-print { display: none; } .invoice-card { margin: 0; box-shadow: none; } }
    </style>
</head>
<body>
    <div class="container pb-5">
        <div class="card invoice-card">
            <div class="invoice-header d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-6 fw-bold mb-1">{{ __('INVOICE') }}</h1>
                    <p class="mb-0 opacity-75">#{{ $sale->invoiceNumber }} | {{ formatted_date($sale->saleDate) }}</p>
                </div>
                <div class="text-end">
                    @if($sale->isPaid)
                        <span class="status-badge status-paid"><i class="fas fa-check-circle me-1"></i> {{ __('PAID') }}</span>
                    @else
                        <span class="status-badge status-unpaid"><i class="fas fa-clock me-1"></i> {{ __('UNPAID') }}</span>
                    @endif
                </div>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row mb-5">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @php
                                $logo = get_business_option('business-settings', $business->id)['invoice_logo'] ?? null;
                            @endphp
                            @if($logo)
                                <img src="{{ asset($logo) }}" alt="Logo" style="height: 60px;">
                            @endif
                            <h3 class="fw-bold mb-0">{{ $business->companyName }}</h3>
                        </div>
                        <p class="text-muted mb-1"><i class="fas fa-map-marker-alt me-2"></i>{{ $business->address }}</p>
                        <p class="text-muted mb-1"><i class="fas fa-phone me-2"></i>{{ $business->phoneNumber }}</p>
                        <p class="text-muted mb-1"><i class="fas fa-envelope me-2"></i>{{ $business->email }}</p>
                        @if($business->vat_no)
                            <p class="text-muted mb-0"><i class="fas fa-file-invoice me-2"></i>{{ __('VAT') }}: {{ $business->vat_no }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-md-end">
                        <h6 class="text-uppercase text-muted mb-2">{{ __('Bill To') }}</h6>
                        <h4 class="fw-bold mb-1">{{ $sale->party->name ?? 'Guest' }}</h4>
                        <p class="text-muted mb-1">{{ $sale->party->phone ?? '' }}</p>
                        <p class="text-muted mb-0">{{ $sale->party->address ?? '' }}</p>
                        
                        <div class="mt-4">
                            @php
                                $sellerName = $business->companyName;
                                $vatRegistrationNumber = $business->vat_no;
                                $timestamp = $sale->created_at->toIso8601String();
                                $invoiceTotal = $sale->totalAmount;
                                $vatTotal = $sale->vat_amount;
                                $zatcaQrContent = generateZatcaQrCode($sellerName, $vatRegistrationNumber, $timestamp, $invoiceTotal, $vatTotal);
                            @endphp
                            <div class="qr-code-bg shadow-sm">
                                {!! QrCode::size(100)->generate($zatcaQrContent) !!}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive mb-5">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th class="ps-0">{{ __('Item') }}</th>
                                <th class="text-center">{{ __('Quantity') }}</th>
                                <th class="text-end">{{ __('Unit Price') }}</th>
                                <th class="text-end pe-0">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($sale->details as $detail)
                                <tr>
                                    <td class="ps-0">
                                        <div class="fw-bold">{{ $detail->product->productName ?? '' }}</div>
                                        @if($detail->stock?->batch_no)
                                            <small class="text-muted">Batch: {{ $detail->stock->batch_no }}</small>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $detail->quantities }}</td>
                                    <td class="text-end">{{ currency_format($detail->price, currency: business_currency($business->id)) }}</td>
                                    <td class="text-end pe-0">{{ currency_format($detail->price * $detail->quantities, currency: business_currency($business->id)) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="total-section shadow-sm">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('Subtotal') }}</span>
                                <span class="fw-bold">{{ currency_format($sale->totalAmount - $sale->vat_amount + $sale->discountAmount - $sale->shipping_charge, currency: business_currency($business->id)) }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">{{ __('VAT') }}</span>
                                <span class="fw-bold">{{ currency_format($sale->vat_amount, currency: business_currency($business->id)) }}</span>
                            </div>
                            @if($sale->shipping_charge > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">{{ __('Shipping') }}</span>
                                    <span class="fw-bold">{{ currency_format($sale->shipping_charge, currency: business_currency($business->id)) }}</span>
                                </div>
                            @endif
                            @if($sale->discountAmount > 0)
                                <div class="d-flex justify-content-between mb-2 text-danger">
                                    <span>{{ __('Discount') }}</span>
                                    <span class="fw-bold">-{{ currency_format($sale->discountAmount, currency: business_currency($business->id)) }}</span>
                                </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="h5 fw-bold mb-0">{{ __('Total Amount') }}</span>
                                <span class="h5 fw-bold mb-0 text-primary">{{ currency_format($sale->totalAmount, currency: business_currency($business->id)) }}</span>
                            </div>
                            
                            @if(!$sale->isPaid && $sale->dueAmount > 0)
                                <div class="d-flex justify-content-between mt-3 text-danger">
                                    <span class="fw-bold">{{ __('Amount Due') }}</span>
                                    <span class="fw-bold h5 mb-0">{{ currency_format($sale->dueAmount, currency: business_currency($business->id)) }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!$sale->isPaid && $moyasar_setting && !empty($moyasar_setting['publishable_key']))
                    <div class="mt-5 no-print">
                        <div class="card bg-dark text-white p-4 rounded-4 border-0">
                            <h4 class="mb-4 d-flex align-items-center">
                                <i class="fas fa-lock me-2 text-success"></i> {{ __('Secure Online Payment') }}
                            </h4>
                            <p class="mb-4 opacity-75">{{ __('Pay your invoice conveniently using Credit Card, Mada, or Apple Pay through Moyasar.') }}</p>
                            
                            <form action="{{ route('invoice.pay', $sale->uuid) }}" method="POST">
                                @csrf
                                <button type="submit" class="pay-button d-flex align-items-center justify-content-center gap-2">
                                    {{ __('PAY NOW WITH MOYASAR') }} <i class="fas fa-arrow-right"></i>
                                </button>
                            </form>
                            
                            <div class="mt-4 d-flex justify-content-center gap-3 opacity-50">
                                <i class="fab fa-cc-visa fa-2x"></i>
                                <i class="fab fa-cc-mastercard fa-2x"></i>
                                <img src="https://moyasar.com/assets/img/mada-light.svg" style="height: 32px;">
                                <i class="fab fa-apple-pay fa-2x"></i>
                            </div>
                        </div>
                    </div>
                @endif
                
                <div class="mt-5 text-center no-print">
                    <button class="btn btn-outline-secondary btn-sm px-4 rounded-pill me-2" onclick="window.print()">
                        <i class="fas fa-print me-2"></i>{{ __('Print Invoice') }}
                    </button>
                    <a href="{{ url('/') }}" class="btn btn-link btn-sm text-decoration-none text-muted">
                        <i class="fas fa-chevron-left me-2"></i>{{ __('Back to Home') }}
                    </a>
                </div>
            </div>
        </div>
        
        <p class="text-center text-muted small pb-4">
            &copy; {{ date('Y') }} {{ $business->companyName }}. {{ __('All rights reserved.') }}
        </p>
    </div>
</body>
</html>
