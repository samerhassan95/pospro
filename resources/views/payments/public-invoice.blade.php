<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Invoice') }} - {{ $sale->invoiceNumber }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .invoice-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .invoice-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
        }
        .invoice-body {
            padding: 2rem;
        }
        .payment-section {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        .amount-display {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }
        .moyasar-btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            color: white;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        .moyasar-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }
        .invoice-details {
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }
        .table th {
            background-color: #f8f9fa;
            border-top: none;
        }
        .alert-payment {
            border-left: 4px solid #28a745;
            background: #d4edda;
            border-color: #c3e6cb;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="invoice-container">
            <!-- Invoice Header -->
            <div class="invoice-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1 class="mb-1">{{ __('Invoice') }}</h1>
                        <h3 class="mb-0">#{{ $sale->invoiceNumber }}</h3>
                    </div>
                    <div class="col-md-4 text-end">
                        <h4 class="mb-1">{{ $sale->business->companyName }}</h4>
                        <p class="mb-0">{{ $sale->business->phoneNumber }}</p>
                        <p class="mb-0">{{ $sale->business->email }}</p>
                    </div>
                </div>
            </div>

            <!-- Invoice Body -->
            <div class="invoice-body">
                <!-- Payment Status Alert -->
                @if(request('payment') == 'success')
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle"></i>
                        {{ __('Payment completed successfully!') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @elseif(request('payment') == 'failed')
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ __('Payment failed. Please try again.') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Invoice Details -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>{{ __('Bill To:') }}</h5>
                        @if($sale->party)
                            <p class="mb-1"><strong>{{ $sale->party->name }}</strong></p>
                            @if($sale->party->phone)
                                <p class="mb-1">{{ $sale->party->phone }}</p>
                            @endif
                            @if($sale->party->email)
                                <p class="mb-1">{{ $sale->party->email }}</p>
                            @endif
                            @if($sale->party->address)
                                <p class="mb-0">{{ $sale->party->address }}</p>
                            @endif
                        @else
                            <p class="text-muted">{{ __('Walk-in Customer') }}</p>
                        @endif
                    </div>
                    <div class="col-md-6 text-end">
                        <p><strong>{{ __('Invoice Date:') }}</strong> {{ $sale->saleDate->format('Y-m-d') }}</p>
                        <p><strong>{{ __('Payment Method:') }}</strong> {{ $sale->payment_type->name ?? __('N/A') }}</p>
                        @if($sale->vat)
                            <p><strong>{{ __('VAT Rate:') }}</strong> {{ $sale->vat->rate }}%</p>
                        @endif
                    </div>
                </div>

                <!-- Items Table -->
                <div class="invoice-details">
                    <table class="table table-striped mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('Item') }}</th>
                                <th class="text-center">{{ __('Quantity') }}</th>
                                <th class="text-end">{{ __('Unit Price') }}</th>
                                <th class="text-end">{{ __('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->details as $detail)
                                <tr>
                                    <td>{{ $detail->product->productName ?? __('Product') }}</td>
                                    <td class="text-center">{{ $detail->quantities }}</td>
                                    <td class="text-end">{{ currency_format($detail->price / $detail->quantities, currency: 'SAR') }}</td>
                                    <td class="text-end">{{ currency_format($detail->price, currency: 'SAR') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">{{ __('Subtotal:') }}</th>
                                <th class="text-end">{{ currency_format($sale->totalAmount - $sale->vat_amount - $sale->shipping_charge + $sale->discountAmount, currency: 'SAR') }}</th>
                            </tr>
                            @if($sale->vat_amount > 0)
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('VAT:') }}</th>
                                    <th class="text-end">{{ currency_format($sale->vat_amount, currency: 'SAR') }}</th>
                                </tr>
                            @endif
                            @if($sale->discountAmount > 0)
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('Discount:') }}</th>
                                    <th class="text-end text-danger">-{{ currency_format($sale->discountAmount, currency: 'SAR') }}</th>
                                </tr>
                            @endif
                            @if($sale->shipping_charge > 0)
                                <tr>
                                    <th colspan="3" class="text-end">{{ __('Shipping:') }}</th>
                                    <th class="text-end">{{ currency_format($sale->shipping_charge, currency: 'SAR') }}</th>
                                </tr>
                            @endif
                            <tr class="table-success">
                                <th colspan="3" class="text-end">{{ __('Total Amount:') }}</th>
                                <th class="text-end amount-display">{{ currency_format($sale->totalAmount, currency: 'SAR') }}</th>
                            </tr>
                            <tr>
                                <th colspan="3" class="text-end">{{ __('Paid Amount:') }}</th>
                                <th class="text-end">{{ currency_format($sale->paidAmount, currency: 'SAR') }}</th>
                            </tr>
                            @if($sale->dueAmount > 0)
                                <tr class="table-warning">
                                    <th colspan="3" class="text-end">{{ __('Due Amount:') }}</th>
                                    <th class="text-end amount-display text-warning">{{ currency_format($sale->dueAmount, currency: 'SAR') }}</th>
                                </tr>
                            @endif
                        </tfoot>
                    </table>
                </div>

                <!-- Payment Section -->
                @if($sale->dueAmount > 0)
                    <div class="payment-section">
                        <h5 class="mb-3">
                            <i class="fas fa-credit-card"></i>
                            {{ __('Pay Online') }}
                        </h5>
                        
                        @if($moyasarEnabled)
                            <div class="alert alert-payment">
                                <i class="fas fa-shield-alt"></i>
                                {{ __('Secure payment powered by Moyasar') }}
                            </div>
                            
                            <form id="paymentForm">
                                @csrf
                                <div class="row align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label">{{ __('Payment Amount') }}</label>
                                        <div class="input-group">
                                            <input type="number" name="amount" class="form-control form-control-lg" 
                                                   value="{{ $sale->dueAmount }}" max="{{ $sale->dueAmount }}" 
                                                   min="0.01" step="0.01" required>
                                            <span class="input-group-text">SAR</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit" class="btn moyasar-btn btn-lg w-100">
                                            <i class="fas fa-credit-card me-2"></i>
                                            {{ __('Pay with Moyasar') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                                {{ __('Online payment is not available for this invoice.') }}
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-success text-center">
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h4>{{ __('Invoice Paid') }}</h4>
                        <p class="mb-0">{{ __('This invoice has been fully paid.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#paymentForm').on('submit', function(e) {
                e.preventDefault();
                
                const amount = $('input[name="amount"]').val();
                const submitBtn = $(this).find('button[type="submit"]');
                
                // Disable button and show loading
                submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>{{ __("Processing...") }}');
                
                $.ajax({
                    url: '{{ route("invoice.pay", $sale->uuid) }}',
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content'),
                        payment_method: 'moyasar',
                        amount: amount
                    },
                    success: function(response) {
                        // Redirect to Moyasar payment page
                        if (response.url) {
                            window.location.href = response.url;
                        }
                    },
                    error: function(xhr) {
                        // Re-enable button
                        submitBtn.prop('disabled', false).html('<i class="fas fa-credit-card me-2"></i>{{ __("Pay with Moyasar") }}');
                        
                        let message = '{{ __("Payment failed") }}';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }
                        
                        // Show error alert
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                ${message}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        $('.payment-section').prepend(alertHtml);
                    }
                });
            });
        });
    </script>
</body>
</html>