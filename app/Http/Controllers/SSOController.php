<?php

namespace App\Http\Controllers;

use App\Services\SSOService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;

class SSOController extends Controller
{
    protected $ssoService;

    public function __construct(SSOService $ssoService)
    {
        $this->ssoService = $ssoService;
    }

    public function login(Request $request)
    {
        if (!config('sso.enabled', false)) {
            Log::warning('SSO: Attempt while SSO is disabled');
            return redirect()->route('login')->with('error', __('SSO is not enabled'));
        }

        $key = 'sso-login:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, config('sso.rate_limit.max_attempts', 10))) {
            $this->ssoService->logAttempt('rate_limited', null, 'Too many attempts');
            return redirect()->route('login')->with('error', __('Too many login attempts. Please try again later.'));
        }

        RateLimiter::hit($key, config('sso.rate_limit.decay_minutes', 1) * 60);

        $token = $request->query('token');

        if (empty($token)) {
            $this->ssoService->logAttempt('failed', null, 'No token provided');
            return redirect()->route('login')->with('error', __('Invalid SSO request'));
        }

        $data = $this->ssoService->decryptToken($token);

        if (!$data) {
            $this->ssoService->logAttempt('failed', null, 'Invalid token');
            return redirect()->route('login')->with('error', __('Invalid or expired SSO token'));
        }

        if (!isset($data['user_id']) || !isset($data['email']) || !isset($data['name'])) {
            $this->ssoService->logAttempt('failed', $data, 'Missing required fields');
            return redirect()->route('login')->with('error', __('Invalid SSO data'));
        }

        $user = $this->ssoService->findOrCreateUser($data);

        if (!$user) {
            $this->ssoService->logAttempt('failed', $data, 'User creation failed');
            return redirect()->route('login')->with('error', __('Unable to create user account'));
        }

        Auth::login($user, true);
        RateLimiter::clear($key);
        $this->ssoService->logAttempt('success', $data);

        // Determine redirect route based on user type
        $redirectRoute = 'admin.dashboard.index';
        if ($user->business_id) {
            $redirectRoute = 'business.dashboard.index';
        }

        return redirect()->intended(route($redirectRoute))->with('success', __('Welcome back!'));
    }

    public function auth(Request $request)
    {
        if (!config('sso.enabled', false)) {
            Log::warning('SSO: Attempt while SSO is disabled');
            return redirect()->route('login')->with('error', __('SSO is not enabled'));
        }

        $key = 'sso-auth:' . $request->ip();
        
        if (RateLimiter::tooManyAttempts($key, config('sso.rate_limit.max_attempts', 10))) {
            $this->ssoService->logAttempt('rate_limited', null, 'Too many attempts');
            return redirect()->route('login')->with('error', __('Too many login attempts. Please try again later.'));
        }

        RateLimiter::hit($key, config('sso.rate_limit.decay_minutes', 1) * 60);

        $token = $request->query('token') ?? $request->input('token');

        if (empty($token)) {
            $this->ssoService->logAttempt('failed', null, 'No token provided');
            return redirect()->route('login')->with('error', __('Invalid SSO request'));
        }

        // Try JWT format first (from Master App)
        $data = $this->ssoService->decryptJWT($token);

        // If JWT fails, try custom encryption format
        if (!$data) {
            $data = $this->ssoService->decryptToken($token);
        }

        if (!$data) {
            $this->ssoService->logAttempt('failed', null, 'Invalid token');
            return redirect()->route('login')->with('error', __('Invalid or expired SSO token'));
        }

        if (!isset($data['user_id']) || !isset($data['email']) || !isset($data['name'])) {
            $this->ssoService->logAttempt('failed', $data, 'Missing required fields');
            return redirect()->route('login')->with('error', __('Invalid SSO data'));
        }

        $user = $this->ssoService->findOrCreateUser($data);

        if (!$user) {
            $this->ssoService->logAttempt('failed', $data, 'User creation failed');
            return redirect()->route('login')->with('error', __('Unable to create user account'));
        }

        Auth::login($user, true);
        RateLimiter::clear($key);
        $this->ssoService->logAttempt('success', $data);

        // Determine redirect route based on user type
        $redirectRoute = 'admin.dashboard.index';
        if ($user->business_id) {
            $redirectRoute = 'business.dashboard.index';
        }

        return redirect()->intended(route($redirectRoute))->with('success', __('Welcome back!'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $returnUrl = $request->query('return_url');
        if ($returnUrl && filter_var($returnUrl, FILTER_VALIDATE_URL)) {
            return redirect($returnUrl);
        }

        return redirect()->route('login');
    }
}
