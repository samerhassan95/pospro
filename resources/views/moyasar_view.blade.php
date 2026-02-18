<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Secure Payment') }} - Moyasar</title>
    <!-- Moyasar Payment Styles -->
    <link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.12.0/moyasar.css" />
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .payment-container {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            margin: 1rem;
        }
        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .payment-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .amount-display {
            font-size: 2.5rem;
            font-weight: bold;
            color: #28a745;
            margin: 1rem 0;
            text-shadow: 0 2px 4px rgba(40, 167, 69, 0.2);
        }
        .description {
            color: #6c757d;
            font-size: 1.1rem;
            margin-bottom: 1.5rem;
        }
        .security-badge {
            background: #e8f5e8;
            border: 1px solid #d4edda;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
        }
        .security-badge i {
            color: #28a745;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }
        .mysr-form {
            margin-top: 1rem;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .loading-content {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #28a745;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .payment-methods {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        .payment-method {
            padding: 0.5rem 1rem;
            background: #f8f9fa;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <i class="fas fa-credit-card fa-3x text-primary mb-3"></i>
            <h2>{{ __('Complete Your Payment') }}</h2>
            <div class="amount-display">{{ number_format($amount / 100, 2) }} {{ __('SAR') }}</div>
            <p class="description">{{ $description }}</p>
        </div>
        
        <div class="security-badge">
            <i class="fas fa-shield-alt"></i>
            <div><strong>{{ __('Secure Payment') }}</strong></div>
            <small>{{ __('Your payment is protected by Moyasar SSL encryption') }}</small>
        </div>
        
        <div class="payment-methods">
            <span class="payment-method">
                <i class="fas fa-credit-card"></i> {{ __('Credit Card') }}
            </span>
            <span class="payment-method">
                <i class="fas fa-university"></i> {{ __('Mada') }}
            </span>
            <span class="payment-method">
                <i class="fab fa-apple-pay"></i> {{ __('Apple Pay') }}
            </span>
        </div>
        
        <div class="mysr-form"></div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <h5>{{ __('Processing Payment...') }}</h5>
            <p class="text-muted">{{ __('Please wait while we process your payment') }}</p>
        </div>
    </div>

    <!-- Moyasar Payment Scripts -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=fetch"></script>
    <script src="https://cdn.moyasar.com/mpf/1.12.0/moyasar.js"></script>
    <script>
        // Show loading overlay when payment starts
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }

        // Initialize Moyasar
        Moyasar.init({
            element: '.mysr-form',
            amount: {{ $amount }},
            currency: 'SAR',
            description: '{{ $description }}',
            publishable_key: '{{ $publishable_key }}',
            callback_url: '{{ route('moyasar.status') }}',
            methods: ['creditcard', 'mada', 'applepay'],
            on_initiated: function() {
                console.log('Payment initiated');
                showLoading();
            },
            on_completed: function(payment) {
                console.log('Payment completed:', payment);
                // Keep loading overlay visible during redirect
            },
            on_failed: function(error) {
                console.log('Payment failed:', error);
                document.getElementById('loadingOverlay').style.display = 'none';
                
                // Show error message
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mt-3';
                errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> {{ __('Payment failed. Please try again.') }}';
                document.querySelector('.payment-container').appendChild(errorDiv);
            }
        });

        // Handle page visibility change
        document.addEventListener('visibilitychange', function() {
            if (document.hidden) {
                // User switched tabs/minimized - might be completing payment
                showLoading();
            }
        });
    </script>
</body>
</html>
