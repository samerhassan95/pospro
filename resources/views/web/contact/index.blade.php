<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('Contact us') }}</title>
    
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
        
        .nav-menu a {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 15px;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .nav-menu a:hover {
            color: #0040CE;
        }
        
        .header-btn {
            color: #FFFFFF;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: opacity 0.3s ease;
            white-space: nowrap;
        }
        
        .header-btn:hover {
            opacity: 0.8;
        }
        
        /* Main Content */
        .main-content {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0px 40px;
        }
        
        @media (min-width: 1400px) {
            .main-content {
                padding: 0px 120px !important;
            }
        }
        
        @media (min-width: 1600px) {
            .main-content {
                padding: 120px 160px !important;
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
        .info-section h1 {
            font-size: 71.65px;
           
            line-height: 1;
            margin-bottom: 20px;
        }
        
        .info-section h1 .highlight {
            color: #FFFFFF;
 font-weight: bold;
        }
        
        .info-section h1 .normal {
            color: #FFFFFF;
             font-weight: 400;
        }
        
        .info-section .subtitle {
            font-size: 22px;
            color: #FFFFFFCC;
            line-height: 1.2;
               font-weight: 500;
            margin-bottom: 40px;
        }
        
        /* Right Side - Form */
        .form-container {
            position: relative;
        }
        
        .form-container::after {
            content: '';
            position: absolute;
            bottom: 20px;
            right: 0;
            width: 95%;
            height: 90%;
            background: #FFFFFF2B;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 24px;
            z-index: -1;
            transform: translate(15px, 15px);
        }
        
        .form-inner {
            position: relative;
            z-index: 1;
            background: #FFFFFFB0;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }
        
        @media (min-width: 1400px) {
            .form-inner {
                padding: 50px;
            }
        }
        
        .form-inner h2 {
            color: #000C28;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .form-inner .form-subtitle {
            color: #000C28CC;
            font-size: 15px;
            margin-bottom: 10px;
        }
        
        .form-group {
            margin-bottom: 10px;
        }
        
        .form-group label {
            display: none;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 18px;
            border: 1px solid rgba(0, 12, 40, 0.1);
            border-radius: 12px;
            background: #FFFFFF85;
            color: #000C28;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #0040CE;
            box-shadow: 0 0 0 4px rgba(0, 64, 206, 0.1);
            background: #FFFFFF;
        }
        
        .form-control::placeholder {
            color: #000C2866;
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 90px;
            font-family: inherit;
        }
        
        .checkbox-wrapper {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin: 10px 0 10px 0;
        }
        
        .checkbox-wrapper input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            width: 24px;
            height: 24px;
            min-width: 24px;
            border: 2px solid #00000033;
            border-radius: 6px;
            background: transparent;
            cursor: pointer;
            position: relative;
            margin-top: 0;
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
            font-size: 16px;
            font-weight: bold;
        }
        
        .checkbox-wrapper label {
            color: #000C28CC;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.5;
            cursor: pointer;
            flex: 1;
        }
        
        .checkbox-wrapper a {
            color: #000C28;
            text-decoration: none;
            font-weight: 600;
        }
        
        .checkbox-wrapper a:hover {
            text-decoration: underline;
        }
        
        .submit-btn {
            width: 100%;
            padding: 10px;
            background: var(--clr-primary);
            color: #FFFFFF;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .submit-btn:hover {
            background: var(--clr-primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 64, 206, 0.4);
        }
        
        .submit-btn:active {
            transform: translateY(0);
        }
        
        .submit-btn svg {
            width: 19px;
            height: 19px;
        }
        
        /* RTL Support for Arabic */
        html[dir="rtl"] .submit-btn {
            flex-direction: row-reverse;
        }
        
        html[dir="rtl"] .submit-btn svg {
            transform: scaleX(-1);
        }
        
        /* Responsive */
        @media (max-width: 1024px) {
            .content-grid {
                grid-template-columns: 1fr;
                gap: 50px;
            }
            
            .info-section {
                text-align: center;
            }
            
            .info-section h1 {
                font-size: 42px;
            }
        }
        
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
            
            .nav-right .header-btn {
                padding: 8px 20px;
                font-size: 13px;
            }
        }
            
            .main-content {
                padding: 30px 20px;
            }
            
            .info-section h1 {
             font-size: 71.65px;
            }
            
            .form-container {
                padding: 20px 25px;
            }
            
            .form-inner {
                padding: 15px 25px;
            }
            
            .form-inner h2 {
                font-size: 26px;
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
                    <a href="#products">Products</a>
                    <a href="#services">Services</a>
                    <a href="#expertise">Expertise</a>
                    <a href="#about">About</a>
                    <a href="{{ route('login') }}" class="header-btn">Login / Signup</a>
                </div>
            </div>
        </header>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-grid">
                <!-- Left Side - Info -->
                <div class="info-section">
                    <h1>
                        <span class="highlight">{{ __('Innovative Software.') }}</span>
                        <span class="normal">{{ __('Real Business Impact') }}</span>
                    </h1>
                    <p class="subtitle">
                        {{ __('Smarter than legacy POS. A cloud-native SaaS solution with real-time data, seamless offline sync, and total business control — anywhere, anytime.') }}
                    </p>
                </div>
                
                <!-- Right Side - Form -->
                <div class="form-container">
                    <div class="form-inner">
                        <h2>{{ __('Contact us') }}</h2>
                        <p class="form-subtitle">{{ __('Our experts are ready to support you and help you achieve your goals.') }}</p>
                        
                        <form id="contact-form" action="{{ route('contact.store') }}" method="POST">
                            @csrf
                            
                            <div class="form-group">
                                <label for="name">Full Name</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="{{ __('Full Name') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="{{ __('Phone Number') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="{{ __('Email') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="company_name">Company (Optional)</label>
                                <input type="text" id="company_name" name="company_name" class="form-control" placeholder="{{ __('Company (Optional)') }}">
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Your message</label>
                                <textarea id="message" name="message" class="form-control" placeholder="{{ __('your message') }}"></textarea>
                            </div>
                            
                            <div class="checkbox-wrapper">
                                <input type="checkbox" id="privacy">
                                <label for="privacy">
                                    {{ __('I agree to the friendly') }} <a href="{{ route('term.index') }}" target="_blank">{{ __('privacy policy') }}</a>
                                </label>
                            </div>
                            
                            <button type="submit" class="submit-btn">
                                {{ $page_data['headings']['contact_us_btn_text'] ?? 'Book Demo' }}
                                <svg width="19" height="19" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 0H19.002V9.50098C19.002 14.7482 14.7482 19.002 9.50098 19.002H0V0Z" fill="white"/>
                                    <path d="M13.8298 13.8702H12.4565V7.96739C10.4814 9.94191 8.50815 11.9137 6.55024 13.8702C6.18974 13.5108 5.86718 13.1885 5.54102 12.8626C7.4926 10.9125 9.46587 8.94065 11.478 6.93093H5.54102V5.54236H13.8298V13.8702Z" fill="#011646"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
        
        // Form submission
        $('#contact-form').on('submit', async function(e) {
            e.preventDefault();
            
            const $form = $(this);
            const $submitBtn = $form.find('.submit-btn');
            const originalText = $submitBtn.html();
            
            // Validate required fields
            const name = $('#name').val().trim();
            const phone = $('#phone').val().trim();
            const email = $('#email').val().trim();
            const message = $('#message').val().trim();
            const privacy = $('#privacy').is(':checked');
            
            if (!name) {
                toastr.error('Please enter your full name');
                $('#name').focus();
                return;
            }
            
            if (!phone) {
                toastr.error('Please enter your phone number');
                $('#phone').focus();
                return;
            }
            
            if (!email) {
                toastr.error('Please enter your email address');
                $('#email').focus();
                return;
            }
            
            // Validate email format
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                toastr.error('Please enter a valid email address');
                $('#email').focus();
                return;
            }
            
            if (!message) {
                toastr.error('Please enter your message');
                $('#message').focus();
                return;
            }
            
            if (!privacy) {
                toastr.error('Please agree to the privacy policy');
                $('#privacy').focus();
                return;
            }
            
            // Disable button and show loading
            $submitBtn.prop('disabled', true);
            $submitBtn.html('Sending...');
            
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
                
                if (response.ok) {
                    toastr.success(data.message || 'Message sent successfully!');
                    $form[0].reset();
                } else {
                    toastr.error(data.message || 'Something went wrong. Please try again.');
                }
            } catch (error) {
                toastr.error('Network error. Please check your connection.');
            } finally {
                $submitBtn.prop('disabled', false);
                $submitBtn.html(originalText);
            }
        });
    </script>
</body>
</html>
