<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Login') }}</title>
    
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Font Awesome Backup -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.0/css/all.min.css" crossorigin="anonymous">
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
        
        /* Background blur effect - positioned at start (left) */
        .page-wrapper {
            position: relative;
            min-height: 100vh;
        }
        
        .page-wrapper::before {
            content: '';
            position: fixed;
            top: 50%;
            left: 20%;
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
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* RTL positioning for Arabic */
        html[dir="rtl"] .header {
            left: auto;
            right: 20px;
            flex-direction: row-reverse;
        }
        
        /* RTL icon flip for Arabic */
        html[dir="rtl"] .home-link svg {
            transform: scaleX(-1);
        }
        
        .home-link {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 8px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #FFFFFF;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .home-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #FFFFFF;
        }
        
        .home-link svg {
            width: 16px;
            height: 16px;
            fill: currentColor;
        }
        
        .language-toggle {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 12px;
            padding: 8px 16px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .language-btn {
            background: transparent !important;
            border: none !important;
            color: #FFFFFF !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            padding: 0 !important;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .flag-icon {
            width: 20px !important;
            height: 15px !important;
            object-fit: cover;
            border-radius: 2px;
        }
        
        .dropdown-menu {
            background: #FFFFFF;
            border: 1px solid rgba(0, 12, 40, 0.1);
            border-radius: 12px;
            padding: 8px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            min-width: 180px;
        }
        
        .dropdown-item {
            padding: 8px 16px;
            color: #000C28 !important;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }
        
        .dropdown-item:hover {
            background: rgba(0, 64, 206, 0.1);
        }
        
        .language-check {
            position: absolute;
            right: 16px;
            color: var(--clr-primary) !important;
            font-size: 14px;
        }
        
        /* Main Content */
        .main-content {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 80px 40px;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        @media (min-width: 1400px) {
            .main-content {
                padding: 80px 120px !important;
            }
        }
        
        @media (min-width: 1600px) {
            .main-content {
                padding: 80px 160px !important;
            }
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }
        
        @media (min-width: 1400px) {
            .content-grid {
                gap: 100px;
            }
        }
        
        /* Left Side - Info */
        .info-section .logo-img {
            width: 80px;
            height: auto;
            margin-bottom: 25px;
        }
        
        .info-section h1 {
            font-size: 65.65px;
            font-weight: 700;
        line-height: 1.3;
            margin-bottom: 20px;
            color: #FFFFFF;
        }
        
        .info-section .subtitle {
            font-size: 22px;
            color: #FFFFFF;
            line-height: 1.6;
                font-weight: 500;
            margin-bottom: 30px;
        }
        
        /* Right Side - Form */
        .form-container {
            position: relative;
        }
        
        .form-inner {
            position: relative;
            z-index: 1;
            background: linear-gradient(174.31deg, rgba(255, 255, 255, 0.2) 6.38%, rgba(255, 255, 255, 0.2) 62.72%, rgba(255, 255, 255, 0) 95.47%);
            backdrop-filter: blur(13.67px);
            -webkit-backdrop-filter: blur(13.67px);
            border-radius: 41.02px;
            padding:  50px 20px;
        }
        
        @media (min-width: 1400px) {
            .form-inner {
            padding:  50px 30px;
            }
            
        }
        
        
        .form-inner h2 {
            color: #FFFFFF;
            font-size: 32px;
            font-weight: 900;
            margin-bottom: 12px ;
            margin-top: 10px ;
            line-height: 1.2;
        }
        .form-inner .star {
            color: #FFFFFF;
            font-size: 15px;
            font-weight: 400;
            margin-bottom: 20px ;
            line-height: 1;
        }
        .form-inner .form-subtitle {
            font-size: 20px;
            font-weight: 400;
            margin-bottom: 20px;
            line-height: 1;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: none;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 15px;
            border: none;
            border-radius: 8.49px;
            background: rgba(255, 255, 255, 0.3);
            color: #FFFFFF;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.4);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        /* Password field with toggle icon */
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper .form-control {
            padding-right: 45px;
        }
        
        .password-toggle {
            position: absolute;
            top: 50%;
            right: 15px;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s ease;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .password-toggle:hover {
            color: rgba(255, 255, 255, 0.9);
        }
        
        .password-toggle svg {
            width: 20px;
            height: 20px;
            fill: currentColor;
        }
        
        /* RTL positioning for Arabic */
        html[dir="rtl"] .password-wrapper .form-control {
            padding-right: 15px;
            padding-left: 45px;
        }
        
        html[dir="rtl"] .password-toggle {
            right: auto;
            left: 15px;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 25px 0 40px 0;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 20px;
            height: 20px;
            min-width: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            background: transparent;
            cursor: pointer;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .checkbox-wrapper input[type="checkbox"]:checked {
            background: var(--clr-primary);
            border-color: var(--clr-primary);
        }
        
        .checkbox-wrapper input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 14px;
            font-weight: bold;
        }
        
        .checkbox-wrapper label {
            color: #FFFFFFCC;
            font-size: 14px;
            font-weight: 400;
            cursor: pointer;
            display: block;
        }
        
        .checkbox-wrapper a {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
        }
        
        .checkbox-wrapper a:hover {
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 10px 15px;
            background: #000C28;
            color: #FFFFFF;
            border: none;
            border-radius: 8.49px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .submit-btn:hover {
            background: #001640;
            transform: translateY(-2px);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .submit-btn svg {
            width: 19px;
            height: 19px;
        }
        
        .create-account-btn {
            width: 100%;
            padding: 10px 15px;
            background: transparent;
            color: #FFFFFF;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 8.49px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: block;
            text-align: center;
            text-decoration: none;
        }
        
        .create-account-btn:hover {
            border-color: rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.05);
        }
        
        .form-footer {
            display: none;
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            
            .info-section {
                text-align: center;
                   margin-top:24px;
            }
            
              .info-section h1 {
                line-height: 0.8;
            }
                    .info-section h1 .highlight, .info-section h1  .normal{

 font-size:32px;
        }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 50px 20px;
            }
            
            .info-section h1 {
                font-size: 36px;
            }
            
            .form-inner {
                padding: 35px 25px;
            }
            
            .form-inner h2 {
                font-size: 26px;
            }
        }
        
        /* RTL Support for Arabic */
        html[dir="rtl"] .submit-btn {
            flex-direction: row-reverse;
        }
        
        html[dir="rtl"] .submit-btn svg {
            transform: scaleX(-1);
        }
    </style>
</head>
<body>
    <div class="page-wrapper">
        <!-- Header -->
        <div class="header">
            @if(app()->getLocale() == 'ar')
                <!-- Arabic: Language Toggle first, then Home Link -->
                <div class="language-toggle">
                    <div class="dropdown">
                        <button class="language-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('flags/' . languages()[app()->getLocale()]['flag'] . '.svg') }}" alt="" class="flag-icon">{{ languages()[app()->getLocale()]['name'] }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach (languages() as $key => $language)
                            <li>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $key]) }}">
                                    <img src="{{ asset('flags/' . $language['flag'] . '.svg') }}" alt="" class="flag-icon">
                                    {{ $language['name'] }}
                                    @if (app()->getLocale() == $key)
                                        <i class="fas fa-check language-check"></i>
                                    @endif
                                </a>
                            </li>
                           @endforeach
                        </ul>
                    </div>
                </div>
                
                <a href="{{ route('home') }}" class="home-link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    {{ __('Home') }}
                </a>
            @else
                <!-- English: Home Link first, then Language Toggle -->
                <a href="{{ route('home') }}" class="home-link">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M20 11H7.83l5.59-5.59L12 4l-8 8 8 8 1.41-1.41L7.83 13H20v-2z"/>
                    </svg>
                    {{ __('Home') }}
                </a>
                
                <div class="language-toggle">
                    <div class="dropdown">
                        <button class="language-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ asset('flags/' . languages()[app()->getLocale()]['flag'] . '.svg') }}" alt="" class="flag-icon">{{ languages()[app()->getLocale()]['name'] }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            @foreach (languages() as $key => $language)
                            <li>
                                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['lang' => $key]) }}">
                                    <img src="{{ asset('flags/' . $language['flag'] . '.svg') }}" alt="" class="flag-icon">
                                    {{ $language['name'] }}
                                    @if (app()->getLocale() == $key)
                                        <i class="fas fa-check language-check"></i>
                                    @endif
                                </a>
                            </li>
                           @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-grid">
                <!-- Left Side - Info -->
                <div class="info-section">
                    <img src="{{ asset(get_footer_logo()) }}" alt="{{ get_system_title() }}" class="logo-img">
                    <h1>{{ __("Let's Get You Started") }}</h1>
                    <p class="subtitle">
                        {{ __('Sign in to unlock your dashboard, tools, and insights — all in one place.') }}
                    </p>
                </div>
                
                <!-- Right Side - Form -->
                <div class="form-container">
                    <div class="form-inner">
                        <span class="star">✦ {{ get_system_title() }}</span>
                        <h2>{{ __('Welcome to POSpro') }}</h2>
                        <p class="form-subtitle">{{ __('Welcome back, Please login in to your account') }}</p>
                        
                        <form method="POST" action="{{ route('login') }}">
                            @csrf
                            
                            <div class="form-group">
                                <label for="email">{{ __('User Name') }}</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="{{ __('User Name') }}" value="{{ old('email') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="password">{{ __('Password') }}</label>
                                <div class="password-wrapper">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="{{ __('Password') }}">
                                    <button type="button" class="password-toggle" onclick="togglePassword()" title="Show/Hide Password">
                                        <svg id="password-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.02 20.25H7.5C6.75 20.25 6.2 20.22 5.73 20.15C2.4 19.78 1.75 17.8 1.75 14.5V9.5C1.75 6.2 2.41 4.21 5.76 3.84C6.2 3.78 6.75 3.75 7.5 3.75H10.96C11.37 3.75 11.71 4.09 11.71 4.5C11.71 4.91 11.37 5.25 10.96 5.25H7.5C6.82 5.25 6.34 5.28 5.95 5.33C3.92 5.55 3.25 6.19 3.25 9.5V14.5C3.25 17.81 3.92 18.44 5.92 18.67C6.34 18.73 6.82 18.75 7.5 18.75H11.02C11.43 18.75 11.77 19.09 11.77 19.5C11.77 19.91 11.43 20.25 11.02 20.25Z" fill="currentColor"/>
                                            <path d="M16.4995 20.25H15.0195C14.6095 20.25 14.2695 19.91 14.2695 19.5C14.2695 19.09 14.6095 18.75 15.0195 18.75H16.4995C17.1795 18.75 17.6595 18.72 18.0495 18.67C20.0795 18.45 20.7495 17.81 20.7495 14.5V9.5C20.7495 6.19 20.0795 5.56 18.0795 5.33C17.6595 5.27 17.1795 5.25 16.4995 5.25H15.0195C14.6095 5.25 14.2695 4.91 14.2695 4.5C14.2695 4.09 14.6095 3.75 15.0195 3.75H16.4995C17.2495 3.75 17.7995 3.78 18.2695 3.85C21.5995 4.22 22.2495 6.2 22.2495 9.5V14.5C22.2495 17.8 21.5895 19.79 18.2395 20.16C17.7995 20.22 17.2495 20.25 16.4995 20.25Z" fill="currentColor"/>
                                            <path d="M15 22.75C14.59 22.75 14.25 22.41 14.25 22V2C14.25 1.59 14.59 1.25 15 1.25C15.41 1.25 15.75 1.59 15.75 2V22C15.75 22.41 15.41 22.75 15 22.75Z" fill="currentColor"/>
                                            <path d="M6.7002 13.0002C6.5702 13.0002 6.4402 12.9702 6.3202 12.9202C6.2002 12.8702 6.0902 12.8002 5.9902 12.7102C5.9002 12.6102 5.8202 12.5002 5.7702 12.3802C5.7202 12.2602 5.7002 12.1302 5.7002 12.0002C5.7002 11.7402 5.8102 11.4802 5.9902 11.2902C6.3602 10.9202 7.0302 10.9202 7.4102 11.2902C7.5902 11.4802 7.7002 11.7402 7.7002 12.0002C7.7002 12.1302 7.6702 12.2602 7.6202 12.3802C7.5702 12.5002 7.5002 12.6102 7.4102 12.7102C7.3102 12.8002 7.2002 12.8702 7.0802 12.9202C6.9602 12.9702 6.8302 13.0002 6.7002 13.0002Z" fill="currentColor"/>
                                            <path d="M10.7002 13.0002C10.5702 13.0002 10.4402 12.9702 10.3202 12.9202C10.2002 12.8702 10.0902 12.8002 9.9902 12.7102C9.9002 12.6102 9.8302 12.5002 9.7702 12.3802C9.7302 12.2602 9.7002 12.1302 9.7002 12.0002C9.7002 11.7402 9.8102 11.4802 9.9902 11.2902C10.3602 10.9202 11.0402 10.9202 11.4102 11.2902C11.5902 11.4802 11.7002 11.7402 11.7002 12.0002C11.7002 12.1302 11.6702 12.2602 11.6202 12.3802C11.5702 12.5002 11.5002 12.6102 11.4102 12.7102C11.3102 12.8002 11.2002 12.8702 11.0802 12.9202C10.9602 12.9702 10.8302 13.0002 10.7002 13.0002Z" fill="currentColor"/>
                                            <line id="eye-slash" x1="3" y1="3" x2="21" y2="21" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display: none;"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="checkbox-wrapper">
                                <div class="checkbox-label">
                                    <input type="checkbox" id="remember" name="remember">
                                    <label for="remember">{{ __('Remember me') }}</label>
                                </div>
                                <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                {{ __('Log in') }}
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0H19.002V9.50098C19.002 14.7482 14.7482 19.002 9.50098 19.002H0V0Z" fill="white"/>
                                    <path d="M13.8298 13.8702H12.4565V7.96739C10.4814 9.94191 8.50815 11.9137 6.55024 13.8702C6.18974 13.5108 5.86718 13.1885 5.54102 12.8626C7.4926 10.9125 9.46587 8.94065 11.478 6.93093H5.54102V5.54236H13.8298V13.8702Z" fill="#011646"/>
                                </svg>
                            </button>
                            
                            <a href="{{ route('plan.index') }}" class="create-account-btn">{{ __('Create an account') }}</a>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Configure toastr
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };
        
        // Password toggle functionality
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeSlash = document.getElementById('eye-slash');
            
            if (passwordInput.type === 'password') {
                // Show password - show the slash line
                passwordInput.type = 'text';
                eyeSlash.style.display = 'block';
            } else {
                // Hide password - hide the slash line
                passwordInput.type = 'password';
                eyeSlash.style.display = 'none';
            }
        }
        
        // Show Laravel validation errors
        @if ($errors->any())
            @foreach ($errors->all() as $error)
                toastr.error('{{ $error }}');
            @endforeach
        @endif
        
        // Show success message
        @if (session('status'))
            toastr.success('{{ session('status') }}');
        @endif
        
        // Handle form submission
        $('form').on('submit', async function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('.submit-btn');
            const originalText = $submitBtn.html();
            
            // Disable button and show loading
            $submitBtn.prop('disabled', true);
            $submitBtn.html('Logging in...');
            
            try {
                const formData = new FormData($form[0]);
                const response = await fetch($form.attr('action'), {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.redirect) {
                    toastr.success(data.message || 'Logged in successfully!');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 500);
                } else if (response.ok) {
                    // Fallback if no redirect URL
                    toastr.success(data.message || 'Logged in successfully!');
                    setTimeout(() => {
                        window.location.href = '/business/dashboard';
                    }, 500);
                } else {
                    if (data.errors) {
                        Object.values(data.errors).forEach(errors => {
                            errors.forEach(error => toastr.error(error));
                        });
                    } else {
                        toastr.error(data.message || 'Login failed. Please try again.');
                    }
                    $submitBtn.prop('disabled', false);
                    $submitBtn.html(originalText);
                }
            } catch (error) {
                toastr.error('Network error. Please check your connection.');
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalText);
            }
        });
    </script>
</body>
</html>
