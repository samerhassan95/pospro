<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Invoice Paid') }} - {{ $sale->invoiceNumber }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .success-container {
            background: white;
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 90%;
        }
        .success-icon {
            color: #28a745;
            font-size: 5rem;
            margin-bottom: 1rem;
            animation: bounce 2s infinite;
        }
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        .invoice-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 1.5rem;
            margin: 2rem 0;
        }
        .amount-display {
            font-size: 2rem;
            font-weight: bold;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1 class="text-success mb-3">{{ __('Payment Successful!') }}</h1>
        <p class="lead text-muted mb-4">{{ __('Your invoice has been paid successfully.') }}</p>
        
        <div class="invoice-details">
            <h5 class="mb-3">{{ __('Invoice Details') }}</h5>
            <div class="row text-start">
                <div class="col-6">
                    <strong>{{ __('Invoice #:') }}</strong>
                </div>
                <div class="col-6">
                    {{ $sale->invoiceNumber }}
                </div>
            </div>
            <div class="row text-start">
                <div class="col-6">
                    <strong>{{ __('Business:') }}</strong>
                </div>
                <div class="col-6">
                    {{ $sale->business->companyName }}
                </div>
            </div>
            <div class="row text-start">
                <div class="col-6">
                    <strong>{{ __('Date:') }}</strong>
                </div>
                <div class="col-6">
                    {{ $sale->saleDate->format('Y-m-d') }}
                </div>
            </div>
            <div class="row text-start">
                <div class="col-6">
                    <strong>{{ __('Amount:') }}</strong>
                </div>
                <div class="col-6 amount-display">
                    {{ currency_format($sale->totalAmount, currency: 'SAR') }}
                </div>
            </div>
            <div class="row text-start">
                <div class="col-6">
                    <strong>{{ __('Status:') }}</strong>
                </div>
                <div class="col-6">
                    <span class="badge bg-success">{{ __('Paid') }}</span>
                </div>
            </div>
        </div>
        
        <div class="alert alert-success">
            <i class="fas fa-info-circle"></i>
            {{ __('Thank you for your payment. A receipt has been sent to your email if provided.') }}
        </div>
        
        <div class="mt-4">
            <button onclick="window.print()" class="btn btn-outline-primary me-2">
                <i class="fas fa-print"></i> {{ __('Print Receipt') }}
            </button>
            <a href="{{ url('/') }}" class="btn btn-primary">
                <i class="fas fa-home"></i> {{ __('Back to Home') }}
            </a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>