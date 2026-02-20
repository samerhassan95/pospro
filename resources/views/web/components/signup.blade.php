<!-- create free account Modal Start -->
<div class="modal fade" id="createFreeAccount" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Create an Free Account!') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <img class="create-account-logo" src="{{ asset(get_login_page_logo()) }}" alt="" style="height: 60px;">
                </div>
                <p class="text-center mb-4" style="color: #000C28CC;">{{ __('Hey, Enter Your details to get Sign Up to your account') }}</p>

                <form action="{{ route('register') }}" method="post" class="sign_up_form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Email') }}</label>
                        <input type="email" placeholder="{{ __('Enter Email Address') }}" class="form-control" name="email" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('Password') }}</label>
                        <input type="hidden" value="" id="plan_id" name="plan_id">
                        <input type="password" placeholder="{{ __('Enter Password') }}" class="form-control" name="password" />
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        {{ __('Sign Up') }}
                    </button>
                </form>

                @if (moduleCheck('SocialLoginAddon'))
                <div class="d-flex align-items-center my-4">
                    <hr class="flex-grow-1 border-1" style="border-color: rgba(0, 12, 40, 0.2);" />
                    <span class="px-3" style="color: #000C28CC;">{{ __('Or Continue with') }}</span>
                    <hr class="flex-grow-1 border-1" style="border-color: rgba(0, 12, 40, 0.2);" />
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ url('login/twitter') }}" class="btn btn-outline-dark" style="border-radius: 12px; padding: 12px;">
                        <img src="{{ asset('assets/img/icon/X.jpg') }}" alt="X" style="height: 20px; margin-right: 8px;">
                        {{ __('Log in with X') }}
                    </a>
                    <a href="" class="btn btn-outline-dark google-login" style="border-radius: 12px; padding: 12px;">
                        <img src="{{ asset('assets/img/icon/google.svg') }}" alt="Google" style="height: 20px; margin-right: 8px;">
                        {{ __('Log in with Google') }}
                    </a>
                </div>
                @endif

                <p class="text-center mt-4" style="color: #000C28CC;">{{ __('Already have an Account?') }} <a href="{{ route('login') }}" style="color: var(--clr-primary); font-weight: 600;">{{ __('Log In') }}</a></p>
            </div>
        </div>
    </div>
</div>
<!-- create free account Modal end -->

<!--Verify Modal Start -->
<div class="modal fade" id="verifymodal" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Email Verification') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p class="mb-3" style="color: #000C28CC;">{{ __('we sent an OTP in your email address') }}</p>
                <p class="mb-4" style="color: var(--clr-primary); font-weight: 600;" id="dynamicEmail"></p>
                
                <form action="{{ route('otp-submit') }}" method="post" class="verify_form">
                    @csrf
                    <div class="d-flex justify-content-center gap-2 mb-4">
                        <input class="form-control text-center otp-input" id="pin-1" type="number" name="otp[]" maxlength="1" style="width: 50px; height: 50px; font-size: 24px; font-weight: 700;">
                        <input class="form-control text-center otp-input" id="pin-2" type="number" name="otp[]" maxlength="1" style="width: 50px; height: 50px; font-size: 24px; font-weight: 700;">
                        <input class="form-control text-center otp-input" id="pin-3" type="number" name="otp[]" maxlength="1" style="width: 50px; height: 50px; font-size: 24px; font-weight: 700;">
                        <input class="form-control text-center otp-input" id="pin-4" type="number" name="otp[]" maxlength="1" style="width: 50px; height: 50px; font-size: 24px; font-weight: 700;">
                        <input class="form-control text-center otp-input" id="pin-5" type="number" name="otp[]" maxlength="1" style="width: 50px; height: 50px; font-size: 24px; font-weight: 700;">
                        <input class="form-control text-center otp-input" id="pin-6" type="number" name="otp[]" maxlength="1" style="width: 50px; height: 50px; font-size: 24px; font-weight: 700;">
                    </div>

                    <p class="mb-4" style="color: #000C28CC;">
                        {{ __('Code send in') }} <span id="countdown" class="countdown" style="color: var(--clr-primary); font-weight: 600;"></span>
                        <span class="reset cursor-pointer" id="otp-resend" data-route="{{ route('otp-resend') }}" style="color: var(--clr-primary); font-weight: 600; cursor: pointer;">{{ __('Resend code') }}</span>
                    </p>
                    <button class="submit-btn">{{ __('Verify') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
<!--Verify Modal end -->

<!-- setup profile Modal Start -->
<div class="modal fade" id="setupAccountModal" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Setup Your Profile') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('business-setup') }}" method="post" class="business_setup_form">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">{{ __('Company/Business Name') }} <span class="text-danger">*</span></label>
                        <input type="text" placeholder="{{ __('Enter company/business name') }}" class="form-control" name="companyName" required />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Business Category') }}</label>
                        <select class="form-control business-categories" name="business_category_id">
                            <option value="">{{ __('Select a category') }}</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Opening Balance') }}</label>
                        <input type="number" step="any" placeholder="{{ __('Ex: $500') }}" class="form-control" name="shopOpeningBalance" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Phone') }}</label>
                        <input type="text" placeholder="{{ __('Enter phone number') }}" class="form-control" name="phoneNumber" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ __('Company Address') }}</label>
                        <textarea placeholder="{{ __('Enter company address') }}" class="form-control" name="address" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="submit-btn">
                        {{ __('Continue') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- setup profile Modal end -->

<!-- success Modal Start -->
<div class="modal fade" id="successModal" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-5">
                <img src="{{ asset(get_login_page_logo()) }}" alt="" style="height: 80px; margin-bottom: 30px;">
                <h4 style="color: #000C28; font-weight: 700; margin-bottom: 15px;">{{ __('Successfully!') }}</h4>
                <p style="color: #000C28CC; margin-bottom: 30px;">{{ __('Congratulations, Your account has been successfully created') }}</p>
                <a href="{{ get_option('general')['app_link'] ?? '' }}" target="_blank" class="submit-btn" style="display: inline-block; width: auto; padding: 14px 40px; text-decoration: none;">{{ __('Download Apk') }}</a>
            </div>
        </div>
    </div>
</div>
<!--success Modal end -->
