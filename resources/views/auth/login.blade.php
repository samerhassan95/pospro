@extends('layouts.auth.app')

@section('title')
    {{ __('Login') }}
@endsection

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
                    <div class="login-body w-100">
                        <h2>{{ __('Welcome to') }}<span>{{ get_option('general')['title'] ?? '' }}</span></h2>
                        <h6>{{ __('Welcome back, Please login in to your account') }}</h6>
                        <form method="POST" action="{{ route('login') }}" class="login_form">
                            @csrf
                            <div class="form-group mb-3">
                                <label for="email" class="form-label">{{ __('User Name') }}</label>
                                <input type="email" name="email" id="email" class="form-control email" placeholder="User Name" style="border-radius: 12px; padding: 15px; border: 1px solid #e0e0e0; background-color: white;">
                            </div>

                            <div class="form-group mb-3">
                                <label for="password" class="form-label">{{ __('Password') }}</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control password" placeholder="Password" style="border-radius: 12px; padding: 15px; border: 1px solid #e0e0e0; background-color: white; padding-right: 50px;">
                                    <span class="hide-pass position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <img src="{{ asset('assets/images/icons/Hide.svg') }}" alt="img" style="width: 20px; height: 20px;" class="hide-icon">
                                        <img src="{{ asset('assets/images/icons/show.svg') }}" alt="img" style="width: 20px; height: 20px; display: none;" class="show-icon">
                                    </span>
                                </div>
                            </div>

                            <div class="mt-lg-3 mb-0 forget-password">
                                <label class="custom-control-label">
                                    <input type="checkbox" name="remember" class="custom-control-input">
                                    <span>{{ __('Remember me') }}</span>
                                </label>
                                <a href="{{ route('password.request') }}">{{ __('Forgot Password?') }}</a>
                            </div>

                            <button type="submit" class="btn login-btn submit-btn">{{ __('Log In') }}</button>

                            <div class="row d-flex flex-wrap mt-2 justify-content-between">
                                <div class="col">
                                    <a href="{{ route('home') }}">{{ __('Back to Home') }}</a>
                                </div>
                                <div class="col text-end">
                                    <a class="text-primary" href="{{ route('plan.index') }}">{{ __('Create an account.') }}</a>
                                </div>
                            </div>
                        </form>

                        @if (moduleCheck('SocialLoginAddon'))
                        <div class="d-flex align-items-center mt-3 ">
                            <hr class="flex-grow-1 border-1 border-secondary-subtle" />
                            <span class="px-3 text-muted">{{__('Or Continue with')}}</span>
                            <hr class="flex-grow-1 border-1 border-secondary-subtle" />
                        </div>

                        <div class="social-login mt-3">
                            <div class="d-flex align-items-center justify-content-center">
                                <a href="{{ url('login/twitter') }}" class="login-social w-100 text-center">
                                    <img src="{{ asset('assets/img/icon/X.jpg') }}" alt="Not found">
                                    {{__('Log in with X')}}
                                </a>
                            </div>
                            <div class="d-flex align-items-center justify-content-center">
                                <a href="{{ url('login/google') }}" class="login-social w-100 text-center">
                                    <img src="{{ asset('assets/img/icon/google.svg') }}" alt="Not found">
                                    {{__('Log in with Google')}}
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" data-model="Login" id="auth">
@endsection

@push('modal')
    @include('web.components.signup')
@endpush

@push('js')
    <script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
