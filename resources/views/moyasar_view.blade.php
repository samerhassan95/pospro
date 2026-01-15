<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Secure Payment') }}</title>
    <!-- Moyasar Payment Styles -->
    <link rel="stylesheet" href="https://cdn.moyasar.com/mpf/1.12.0/moyasar.css" />
    <style>
        body {
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        .payment-container {
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0 10px 25px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 450px;
        }
        .payment-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .payment-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.5rem;
        }
        .amount-display {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
            margin: 1rem 0;
        }
    </style>
</head>
<body>
    <div class="payment-container">
        <div class="payment-header">
            <h2>{{ __('Complete Your Payment') }}</h2>
            <div class="amount-display">{{ $amount / 100 }} SAR</div>
            <p class="text-muted">{{ $description }}</p>
        </div>
        
        <div class="mysr-form"></div>
    </div>

    <!-- Moyasar Payment Scripts -->
    <script src="https://polyfill.io/v3/polyfill.min.js?features=fetch"></script>
    <script src="https://cdn.moyasar.com/mpf/1.12.0/moyasar.js"></script>
    <script>
        Moyasar.init({
            element: '.mysr-form',
            amount: {{ $amount }},
            currency: 'SAR',
            description: '{{ $description }}',
            publishable_key: '{{ $publishable_key }}',
            callback_url: '{{ route('moyasar.status') }}',
            methods: ['creditcard', 'mada', 'applepay']
        });
    </script>
</body>
</html>
