@extends('layouts.auth.app', [
    'title' => __('Reset Password'),
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
                        <h2>{{ __('Change Password') }}</h2>
                        <h6>{{ __('Create new password, it must be Strong password.') }}</h6>
                        <form action="{{ route('password.store') }}" method="post" class="ajaxform_instant_reload">
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">
                            <input type="hidden" name="email" value="{{ $request->email }}">

                            <div class="form-group mb-3">
                                <label for="password" class="form-label">{{ __('New Password') }}</label>
                                <div class="position-relative">
                                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••••••••••••••" style="border-radius: 12px; padding: 15px; border: 1px solid #e0e0e0; background-color: white; padding-right: 50px;">
                                    <span class="hide-pass position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <img src="{{ asset('assets/images/icons/Hide.svg') }}" alt="img" style="width: 20px; height: 20px;" class="hide-icon">
                                        <img src="{{ asset('assets/images/icons/show.svg') }}" alt="img" style="width: 20px; height: 20px; display: none;" class="show-icon">
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="password_confirmation" class="form-label">{{ __('Confirm Password') }}</label>
                                <div class="position-relative">
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="••••••••••••••••••••••••" style="border-radius: 12px; padding: 15px; border: 1px solid #e0e0e0; background-color: white; padding-right: 50px;">
                                    <span class="hide-pass position-absolute" style="right: 15px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                        <img src="{{ asset('assets/images/icons/Hide.svg') }}" alt="img" style="width: 20px; height: 20px;" class="hide-icon">
                                        <img src="{{ asset('assets/images/icons/show.svg') }}" alt="img" style="width: 20px; height: 20px; display: none;" class="show-icon">
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="btn login-btn submit-btn">{{ __('Continue') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <script src="{{ asset('assets/js/auth.js') }}"></script>
@endpush
