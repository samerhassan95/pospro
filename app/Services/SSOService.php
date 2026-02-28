<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SSOService
{
    public function decryptToken(string $token): ?array
    {
        try {
            $secret = config('sso.secret_key');
            
            if (empty($secret)) {
                Log::error('SSO: Secret key not configured');
                return null;
            }

            $decoded = base64_decode($token);
            if ($decoded === false) {
                Log::error('SSO: Invalid base64 token');
                return null;
            }

            $parts = explode('::', $decoded);
            if (count($parts) !== 2) {
                Log::error('SSO: Invalid token format');
                return null;
            }

            [$encrypted, $signature] = $parts;

            $expectedSignature = hash_hmac('sha256', $encrypted, $secret);
            if (!hash_equals($expectedSignature, $signature)) {
                Log::error('SSO: Invalid signature');
                return null;
            }

            $iv = substr(hash('sha256', $secret), 0, 16);
            $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $secret, 0, $iv);

            if ($decrypted === false) {
                Log::error('SSO: Decryption failed');
                return null;
            }

            $data = json_decode($decrypted, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('SSO: Invalid JSON data');
                return null;
            }

            // Log master app URL if provided
            if (isset($data['master_app_url'])) {
                Log::info('SSO: Request from master app', ['url' => $data['master_app_url']]);
            }

            // Validate timestamp (disabled if expiry is 0)
            $tokenExpiry = config('sso.token_expiry', 0);
            if ($tokenExpiry > 0 && isset($data['timestamp']) && (time() - $data['timestamp']) > $tokenExpiry) {
                Log::error('SSO: Token expired');
                return null;
            }

            return $data;

        } catch (\Exception $e) {
            Log::error('SSO: Token decryption error - ' . $e->getMessage());
            return null;
        }
    }

    public function findOrCreateUser(array $data): ?User
    {
        try {
            $user = User::where('external_id', $data['user_id'])
                ->where('sso_provider', 'nomuapps')
                ->first();

            if ($user) {
                $user->update(['last_sso_login' => now()]);
                Log::info('SSO: Existing user logged in', ['user_id' => $user->id]);
                return $user;
            }

            if (!config('sso.allow_auto_registration', true)) {
                Log::warning('SSO: Auto registration disabled');
                return null;
            }

            $user = User::where('email', $data['email'])->first();

            if ($user) {
                $user->update([
                    'external_id' => $data['user_id'],
                    'sso_provider' => 'nomuapps',
                    'last_sso_login' => now(),
                ]);
                Log::info('SSO: Existing user linked to SSO', ['user_id' => $user->id]);
                return $user;
            }

            $user = $this->createNewUser($data);
            Log::info('SSO: New user created', ['user_id' => $user->id]);
            return $user;

        } catch (\Exception $e) {
            Log::error('SSO: User creation error - ' . $e->getMessage());
            return null;
        }
    }

    protected function createNewUser(array $data): User
    {
        $userData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make(Str::random(32)),
            'external_id' => $data['user_id'],
            'sso_provider' => 'nomuapps',
            'last_sso_login' => now(),
            'email_verified_at' => now(),
            'locale' => $data['locale'] ?? 'ar',
        ];

        // Check if plan_id is provided - create business with subscription
        if (isset($data['plan_id']) && !empty($data['plan_id'])) {
            // Verify plan exists
            $plan = \App\Models\Plan::find($data['plan_id']);
            if (!$plan) {
                Log::error('SSO: Invalid plan_id provided', ['plan_id' => $data['plan_id']]);
                throw new \Exception('Invalid plan_id');
            }

            // Create business first
            $business = $this->createBusinessForUser($data, $plan);
            
            // Create subscription
            $subscription = $this->createSubscription($business, $plan, $data);
            
            // Update business with subscription
            $business->update(['plan_subscribe_id' => $subscription->id]);
            
            // Set user as shop-owner with business
            $userData['business_id'] = $business->id;
            $userData['role'] = 'shop-owner';
            
            Log::info('SSO: Created business and subscription', [
                'business_id' => $business->id,
                'subscription_id' => $subscription->id,
                'plan_id' => $plan->id
            ]);
        }
        // Determine role based on business_id (existing business)
        elseif (isset($data['business_id']) && !empty($data['business_id'])) {
            // Business user (shop-owner)
            $userData['business_id'] = $data['business_id'];
            $userData['role'] = 'shop-owner';
            
            // Try to find the business and assign branch if exists
            $business = \App\Models\Business::find($data['business_id']);
            if ($business) {
                $branch = $business->branches()->first();
                if ($branch) {
                    $userData['branch_id'] = $branch->id;
                }
            }
        } else {
            // Admin user - assign based on provided role or default to 'admin'
            $userData['role'] = $data['role'] ?? 'admin';
        }

        $user = User::create($userData);

        // Assign Spatie role if exists
        try {
            if (\Spatie\Permission\Models\Role::where('name', $userData['role'])->exists()) {
                $user->assignRole($userData['role']);
                Log::info('SSO: Assigned Spatie role', ['role' => $userData['role']]);
            }
        } catch (\Exception $e) {
            Log::warning('SSO: Could not assign Spatie role - ' . $e->getMessage());
        }

        return $user;
    }

    protected function createBusinessForUser(array $data, \App\Models\Plan $plan): \App\Models\Business
    {
        // Get default business category or create one if doesn't exist
        $defaultCategory = \App\Models\BusinessCategory::first();
        if (!$defaultCategory) {
            $defaultCategory = \App\Models\BusinessCategory::create([
                'name' => 'General',
                'status' => 1
            ]);
        }
        
        $businessData = [
            'business_category_id' => $data['business_category_id'] ?? $defaultCategory->id,
            'companyName' => $data['business_name'] ?? $data['name'] . ' Business',
            'email' => $data['email'],
            'phoneNumber' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'status' => 1,
            'subscriptionDate' => now(),
            'will_expire' => now()->addDays($plan->duration ?? 30),
            'remainingShopBalance' => 0,
            'shopOpeningBalance' => 0,
        ];

        // Add B2B fields if provided
        if (isset($data['vat_no'])) {
            $businessData['vat_no'] = $data['vat_no'];
        }
        if (isset($data['commercial_registration'])) {
            $businessData['commercial_registration'] = $data['commercial_registration'];
        }
        if (isset($data['building_number'])) {
            $businessData['building_number'] = $data['building_number'];
        }
        if (isset($data['street_name'])) {
            $businessData['street_name'] = $data['street_name'];
        }
        if (isset($data['district'])) {
            $businessData['district'] = $data['district'];
        }
        if (isset($data['city'])) {
            $businessData['city'] = $data['city'];
        }
        if (isset($data['postal_code'])) {
            $businessData['postal_code'] = $data['postal_code'];
        }
        if (isset($data['country_code'])) {
            $businessData['country_code'] = $data['country_code'];
        }

        $business = \App\Models\Business::create($businessData);
        
        Log::info('SSO: Business created', ['business_id' => $business->id]);
        
        return $business;
    }

    protected function createSubscription(\App\Models\Business $business, \App\Models\Plan $plan, array $data): \App\Models\PlanSubscribe
    {
        $subscriptionData = [
            'business_id' => $business->id,
            'plan_id' => $plan->id,
            'price' => $plan->offerPrice ?? $plan->subscriptionPrice,
            'duration' => $plan->duration ?? 30,
            'payment_status' => 'paid', // SSO subscriptions are pre-paid
            'service_start_date' => now(),
            'service_end_date' => now()->addDays($plan->duration ?? 30),
            'allow_multibranch' => $plan->allow_multibranch ?? 0,
            'addon_domain_limit' => $plan->addon_domain_limit ?? 0,
            'subdomain_limit' => $plan->subdomain_limit ?? 1,
            'invoice_type' => 'B2C', // Default to B2C
        ];

        // Add payment gateway if provided
        if (isset($data['gateway_id'])) {
            $subscriptionData['gateway_id'] = $data['gateway_id'];
        }

        // Add notes if provided
        if (isset($data['subscription_notes'])) {
            $subscriptionData['notes'] = $data['subscription_notes'];
        }

        $subscription = \App\Models\PlanSubscribe::create($subscriptionData);
        
        Log::info('SSO: Subscription created', [
            'subscription_id' => $subscription->id,
            'plan_id' => $plan->id,
            'duration' => $subscriptionData['duration']
        ]);
        
        return $subscription;
    }

    public function decryptJWT(string $token): ?array
    {
        try {
            // Split JWT token
            $parts = explode('.', $token);
            if (count($parts) !== 3) {
                Log::error('SSO JWT: Invalid token format');
                return null;
            }

            // Decode payload (second part)
            $payload = base64_decode(strtr($parts[1], '-_', '+/'));
            if ($payload === false) {
                Log::error('SSO JWT: Invalid base64 payload');
                return null;
            }

            $data = json_decode($payload, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('SSO JWT: Invalid JSON payload');
                return null;
            }

            // Map JWT fields to our format
            $mappedData = [
                'user_id' => $data['sub'] ?? null,
                'email' => $data['email'] ?? null,
                'name' => $data['name'] ?? null,
                'timestamp' => $data['iat'] ?? time(),
            ];

            // Add plan_id if exists, otherwise use app_id as plan_id
            if (isset($data['plan_id'])) {
                $mappedData['plan_id'] = $data['plan_id'];
            } elseif (isset($data['app_id'])) {
                // Map app_id to plan_id (you can customize this mapping)
                // For now, we'll use app_id directly as plan_id
                // Or map to a default plan
                $mappedData['plan_id'] = $this->mapAppIdToPlanId($data['app_id']);
                $mappedData['app_id'] = $data['app_id'];
            }

            // Add subscription end date if exists
            if (isset($data['subscription_ends'])) {
                $mappedData['subscription_ends'] = $data['subscription_ends'];
            }

            // Add business name from email if not provided
            if (!isset($mappedData['business_name'])) {
                $emailParts = explode('@', $mappedData['email']);
                $mappedData['business_name'] = ucfirst($emailParts[0]) . ' Business';
            }

            // Check expiration
            if (isset($data['exp']) && $data['exp'] < time()) {
                Log::error('SSO JWT: Token expired');
                return null;
            }

            Log::info('SSO JWT: Token decoded successfully', [
                'user_id' => $mappedData['user_id'],
                'email' => $mappedData['email'],
                'plan_id' => $mappedData['plan_id'] ?? 'none',
            ]);

            return $mappedData;

        } catch (\Exception $e) {
            Log::error('SSO JWT: Decryption error - ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Map app_id from Master App to plan_id in Sub App
     * Customize this based on your Master App's app_id values
     */
    protected function mapAppIdToPlanId($appId): ?int
    {
        // Default mapping - you can customize this
        // For example:
        // app_id 12 = Plan B (30 days)
        // app_id 13 = Plan C (180 days)
        
        $mapping = [
            12 => 2, // Plan B
            13 => 3, // Plan C
            // Add more mappings as needed
        ];

        return $mapping[$appId] ?? 2; // Default to Plan B if not found
    }

    public function logAttempt(string $status, ?array $data = null, ?string $error = null): void
    {
        $logData = [
            'status' => $status,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toDateTimeString(),
        ];

        if ($data) {
            $logData['user_id'] = $data['user_id'] ?? null;
            $logData['email'] = $data['email'] ?? null;
        }

        if ($error) {
            $logData['error'] = $error;
        }

        Log::channel(config('sso.log_channel'))->info('SSO Attempt', $logData);
    }
}
