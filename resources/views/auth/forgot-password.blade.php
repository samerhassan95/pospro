@extends('layouts.auth.app', [
    'title' => __('Forget Password')
])

@section('main_content')
<div class="footer">
        <div class="footer-logo">
             <a  href="{{ route('home') }}" class="logo-link">
            <img src="{{ asset('assets/images/Logo.png') }}" alt="Logo" class="sidebar-logo-img w-[32x] h-[32px]">
            <span class="sidebar-logo-text "><span class="bytes-text">Bytes</span> Pos</span>
        </a>        </div>
    <div class="mybazar-login-section">
        <div class="mybazar-login-avatar">
                         <img src="{{ asset('assets/images/login.png') }}" alt="Logo" class="sidebar-logo-img w-[32x] h-[32px]">
        </div>
        <div class="mybazar-login-wrapper">
            <div class="login-wrapper">
                <div class="login-header">
                    <h4>{{ get_option('general')['name'] ?? '' }}</h4>
                </div>
                <div class="login-body w-100">
                    <h2>{{ __('Forgot Password') }}</h2>
                    <h6>{{ __('Enter the email address associated with your account') }}</h6>
                    <form method="POST" action="{{ route('password.email') }}" class="ajaxform">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="{{ __('Enter your Email') }}" style="border-radius: 12px; padding: 15px; border: 1px solid #e0e0e0; background-color: white;">
                        </div>

                        <button type="submit" class="btn login-btn submit-btn">{{ __('Continue') }}</button>
                    </form>
                    <div class="back-to-login">
                        <img src="{{ asset('assets/images/user-img/user.png') }}" alt="img">
                        <a href="{{ route('login') }}">{{ __('Back to Login') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

