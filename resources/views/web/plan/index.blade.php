<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Pricing Plans') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Cairo Font -->
    <link rel="stylesheet" href="{{ asset('fonts/cairo/cairo.css') }}">
    
    <style>
        :root {
            --clr-primary: {{ get_primary_color() }};
            --clr-secondary: {{ get_secondary_color() }};
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #000C28;
            color: #FFFFFF;
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        /* Background blur effect */
        .page-wrapper {
            position: relative;
            min-height: 100vh;
        }
        
        .page-wrapper::before {
            content: '';
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 800px;
            height: 800px;
            background: #0040CE;
            border-radius: 50%;
            filter: blur(500px);
            -webkit-filter: blur(500px);
            z-index: 0;
            pointer-events: none;
        }
        
        /* Header */
        .header {
            position: relative;
            z-index: 10;
            padding: 20px 40px;
        }
        
        .header-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 10px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #FFFFFF1A;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .logo img {
            height: 40px;
            width: auto;
        }
        
        .logo-text {
            color: #FFFFFF;
            font-size: 24px;
            font-weight: 700;
        }
        
        .nav-right {
            display: flex;
            align-items: center;
            gap: 32px;
        }
        
        .nav-right a {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }
        
        .nav-right a:hover {
            opacity: 0.8;
        }
        
        .nav-right .header-btn {
            background: #FFFFFF69;
            padding: 10px 24px;
            border-radius: 8px;
        }
        
        /* Main Content */
        .main-content {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 40px;
        }
        
        @media (min-width: 1400px) {
            .main-content {
                padding: 80px 120px;
            }
        }
        
        /* Section Title */
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-title h1 {
            font-size: 48px;
            font-weight: 700;
            margin-bottom: 15px;
            color: #FFFFFF;
        }
        
        .section-title p {
            font-size: 18px;
            color: #FFFFFFCC;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Plan Cards */
        .plans-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        
        .plan-card {
            background: linear-gradient(174.31deg, rgba(255, 255, 255, 0.15) 6.38%, rgba(255, 255, 255, 0.15) 62.72%, rgba(255, 255, 255, 0) 95.47%);
            backdrop-filter: blur(13.67px);
            -webkit-backdrop-filter: blur(13.67px);
            border-radius: 24px;
            padding: 40px 30px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .plan-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.3);
        }
        
        .plan-card.featured {
            border: 2px solid var(--clr-primary);
            background: linear-gradient(174.31deg, rgba(255, 255, 255, 0.2) 6.38%, rgba(255, 255, 255, 0.2) 62.72%, rgba(255, 255, 255, 0) 95.47%);
        }
        
        .plan-card.featured::before {
            content: 'POPULAR';
            position: absolute;
            top: 20px;
            right: -30px;
            background: var(--clr-primary);
            color: #FFFFFF;
            padding: 5px 40px;
            font-size: 12px;
            font-weight: 700;
            transform: rotate(45deg);
        }
        
        .plan-name {
            font-size: 24px;
            font-weight: 700;
            color: #FFFFFF;
            margin-bottom: 15px;
        }
        
        .plan-price {
            font-size: 48px;
            font-weight: 900;
            color: #FFFFFF;
            margin-bottom: 5px;
        }
        
        .plan-price span {
            font-size: 18px;
            font-weight: 400;
            color: #FFFFFFCC;
        }
        
        .plan-duration {
            font-size: 16px;
            color: #FFFFFFCC;
            margin-bottom: 30px;
        }
        
        .plan-features {
            list-style: none;
            padding: 0;
            margin: 30px 0;
        }
        
        .plan-features li {
            padding: 12px 0;
            color: #FFFFFFCC;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .plan-features li i {
            font-size: 18px;
            min-width: 20px;
        }
        
        .plan-features li i.fa-check-circle {
            color: #4CAF50;
            font-family: "Font Awesome 6 Free" !important;
        }
        
        .plan-features li i.fa-times-circle {
            color: #FF5252;
            font-family: "Font Awesome 6 Free" !important;
        }
        
        .plan-btn {
            width: 100%;
            padding: 14px;
            background: #000C28;
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: block;
            text-align: center;
        }
        
        .plan-btn:hover {
            background: #001640;
            transform: translateY(-2px);
            color: #FFFFFF;
        }
        
        .plan-card.featured .plan-btn {
            background: var(--clr-primary);
        }
        
        .plan-card.featured .plan-btn:hover {
            background: var(--clr-secondary);
        }
        
        /* Modal Styles */
        .modal-content {
            background: linear-gradient(174.31deg, rgba(255, 255, 255, 0.95) 6.38%, rgba(255, 255, 255, 0.95) 62.72%, rgba(255, 255, 255, 0.9) 95.47%);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: 24px;
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid rgba(0, 12, 40, 0.1);
            padding: 20px 30px;
        }
        
        .modal-body {
            padding: 30px;
        }
        
        .modal-title {
            color: #000C28;
            font-size: 24px;
            font-weight: 700;
        }
        
        .form-label {
            color: #000C28;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-control {
            padding: 12px 16px;
            border: 1px solid rgba(0, 12, 40, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.8);
            color: #000C28;
            font-size: 15px;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--clr-primary);
            box-shadow: 0 0 0 4px rgba(0, 64, 206, 0.1);
            background: #FFFFFF;
        }
        
        .btn-close {
            filter: invert(1);
        }
        
        .submit-btn {
            width: 100%;
            padding: 14px;
            background: var(--clr-primary);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            background: var(--clr-secondary);
            transform: translateY(-2px);
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .header {
                padding: 15px 20px;
            }
            
            .header-container {
                padding: 10px 20px;
            }
            
            .nav-right {
                gap: 16px;
            }
            
            .nav-right a:not(.header-btn) {
                display: none;
            }
            
            .main-content {
                padding: 40px 20px;
            }
            
            .section-title h1 {
                font-size: 32px;
            }
            
            .plans-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Header -->
        <header class="header">
            <div class="header-container">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset(get_footer_logo()) }}" alt="Logo">
                    <span class="logo-text">{{ get_system_title() }}</span>
                </a>
                
                <div class="nav-right">
                    <div class="home-page-language-change">
                        <div class="dropdown">
                            <button class="language-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: transparent; border: none; color: #FFFFFF; font-size: 15px; font-weight: 500;">
                                <img src="{{ asset('flags/' . languages()[app()->getLocale()]['flag'] . '.svg') }}" alt="" class="flag-icon me-2" style="width: 20px; height: 15px;">{{ languages()[app()->getLocale()]['name'] }}
                            </button>
                            <ul class="dropdown-menu dropdown-menu-scroll">
                                @foreach (languages() as $key => $language)
                                <li class="language-li">
                                    <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $key]) }}" style="color: #000C28;">
                                        <img src="{{ asset('flags/' . $language['flag'] . '.svg') }}" alt="" class="flag-icon me-2" style="width: 20px; height: 15px;">
                                        {{ $language['name'] }}
                                    </a>
                                    @if (app()->getLocale() == $key)
                                        <i class="fas fa-check language-check" style="color: var(--clr-primary);"></i>
                                    @endif
                                </li>
                               @endforeach
                            </ul>
                        </div>
                    </div>
                    <a href="{{ route('login') }}" class="header-btn">{{ __('Login / Signup') }}</a>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="section-title">
                <h1>{{ $page_data['headings']['pricing_title'] ?? __('Choose Your Plan') }}</h1>
                <p>{{ $page_data['headings']['pricing_description'] ?? __('Select the perfect plan for your business needs') }}</p>
            </div>
            
            <div class="plans-grid">
                @foreach ($plans as $index => $plan)
                    <div class="plan-card {{ $index === 1 ? 'featured' : '' }}">
                        <div class="plan-name">{{ $plan['subscriptionName'] ?? '' }}</div>
                        <div class="plan-price">
                            @if (($plan['offerPrice'] && $plan['subscriptionPrice'] !== null) || $plan['offerPrice'] || $plan['subscriptionPrice'])
                                @if ($plan['offerPrice'])
                                    {{ currency_format($plan['offerPrice']) }}
                                @else
                                    {{ currency_format($plan['subscriptionPrice']) }}
                                @endif
                            @else
                                @if ($plan['offerPrice'] || $plan['subscriptionPrice'])
                                    {{ currency_format($plan['offerPrice'] ?? $plan['subscriptionPrice']) }}
                                @else
                                    {{ __('Free') }}
                                @endif
                            @endif
                        </div>
                        <div class="plan-duration">/{{ $plan['duration'] . ' ' . __('Days') }}</div>
                        
                        <ul class="plan-features">
                            @foreach ($plan['features'] ?? [] as $key => $item)
                            <li>
                                <i class="fas {{ isset($item[1]) ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                {{ $item[0] ?? '' }}
                            </li>
                            @endforeach
                            
                            @if (moduleCheck('MultiBranchAddon'))
                                <li>
                                    <i class="fas {{ $plan->allow_multibranch == 1 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ __('Multi-branch Allowed') }}
                                </li>
                            @endif
                            
                            @if (moduleCheck('CustomDomainAddon'))
                                <li>
                                    <i class="fas {{ $plan->addon_domain_limit > 0 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $plan->addon_domain_limit > 0 ? __('Addon Limit:') . ' ' . $plan->addon_domain_limit : __('Addon Domain Available?') }}
                                </li>
                                
                                <li>
                                    <i class="fas {{ $plan->subdomain_limit > 0 ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                                    {{ $plan->subdomain_limit > 0 ? __('Subdomain Limit:') . ' ' . $plan->subdomain_limit : __('Subdomain Available?') }}
                                </li>
                            @endif
                        </ul>
                        
                        <button class="plan-btn subscribe-plan" data-plan-id="{{ $plan->id }}" data-google-url="{{ url('login/google?plan_id=') . $plan->id }}" data-twitter-url="{{ url('login/twitter?plan_id=') . $plan->id }}">
                            {{ __('Buy Now') }}
                        </button>
                    </div>
                @endforeach
            </div>
        </main>
    </div>
    
    @include('web.components.signup')
    
    <input type="hidden" value="{{ route('get-business-categories') }}" id="get-business-categories">
    
    <!-- jQuery -->
    <script src="{{ asset('assets/web/js/jquery-3.6.0.min.js') }}"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="{{ asset('assets/js/toastr.min.js') }}"></script>
    <!-- jQuery Validation -->
    <script src="{{ asset('assets/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
    <!-- Validation Setup -->
    <script src="{{ asset('assets/plugins/validation-setup/validation-setup.js') }}"></script>
    <!-- jQuery Confirm -->
    <script src="{{ asset('assets/plugins/jquery-confirm/jquery-confirm.min.js') }}"></script>
    <!-- Notification -->
    <script src="{{ asset('assets/plugins/custom/notification.js') }}"></script>
    <!-- Form JS -->
    <script src="{{ asset('assets/plugins/custom/form.js') }}"></script>
    
    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
    </script>
</body>
</html>
