<?php

use App\Models\User;
use App\Models\Branch;
use App\Models\Option;
use App\Models\Business;
use App\Models\Currency;
use App\Models\Transaction;
use App\Models\UserCurrency;
use Kreait\Firebase\Factory;
use App\Models\PlanSubscribe;
use App\Models\ProductSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Notifications\SendNotification;
use Illuminate\Support\Facades\Artisan;
use Modules\MarketingAddon\App\Models\Sim;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Support\Facades\Notification;
use Modules\MarketingAddon\App\Models\Device;
use Modules\MarketingAddon\App\Models\Smsgateway;

function cache_remember(string $key, callable $callback, int $ttl = 5000): mixed
{
    return cache()->remember($key, env('CACHE_LIFETIME', $ttl), $callback);
}

function get_option($key)
{
    return cache_remember($key, function () use ($key) {
        return Option::where('key', $key)->first()->value ?? [];
    });
}

function invoice_setting()
{
    return get_option('invoice_setting_' . auth()->user()->business_id);
}

function invoice_language()
{
    return get_option('invoice_language_' . auth()->user()->business_id);
}

function generate_invoice_number($type = 'sale', $business_id = null)
{
    $business_id = $business_id ?? auth()->user()->business_id;
    
    if ($type === 'sale') {
        $count = \App\Models\Sale::where('business_id', $business_id)->count();
        return 'S-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    } elseif ($type === 'purchase') {
        $count = \App\Models\Purchase::where('business_id', $business_id)->count();
        return 'P-' . str_pad($count + 1, 5, '0', STR_PAD_LEFT);
    }
    
    return null;
}

function product_setting()
{
    $businessId = auth()->user()->business_id;
    $cacheKey = 'product_setting_' . $businessId;

    return cache()->remember($cacheKey, 60, function () use ($businessId) {
        $productSetting = ProductSetting::where('business_id', $businessId)->first();

        if ($productSetting) {
            $productSetting->modules = $productSetting->modules ?? [];
        }

        return $productSetting;
    });
}

function is_module_enabled(?array $modules, string $key): bool
{
    // Keys that should default to true if not set
    $defaultTrueKeys = [
        'show_product_category',
        'show_product_stock',
        'show_exclusive_price',
        'show_inclusive_price',
        'show_profit_percent',
        'show_product_sale_price',
        'show_product_wholesale_price',
        'show_product_dealer_price',
        'show_action',
    ];

    if (in_array($key, $defaultTrueKeys)) {
        return !isset($modules[$key]) || (bool)$modules[$key];
    }

    // All other keys: show only if explicitly set to true
    return isset($modules[$key]) && (bool)$modules[$key];
}

function formatted_date(?string $date = null, string $format = 'd M, Y'): ?string
{
    return !empty($date) ? Date::parse($date)->format($format) : null;
}

function formatted_time(?string $time = null, string $format = 'h:i A'): ?string
{
    return !empty($time) ? Date::parse($time)->format($format) : null;
}

function sendNotification($id, $url, $message, $user = null)
{
    $notify = [
        'id' => $id,
        'url' => $url,
        'user' => $user,
        'message' => $message,
    ];

    $notify_user = User::where('role', 'superadmin')->first();
    Notification::send($notify_user, new SendNotification($notify));
}

function sendNotifyToUser($id, $url, $message, $user)
{
    $notify = [
        'id' => $id,
        'url' => $url,
        'user' => $user,
        'message' => $message,
    ];

    $notify_user = User::where('business_id', $user)->first();
    Notification::send($notify_user, new SendNotification($notify));
}

function currency_format($amount, $type = "icon", $decimals = 2, $currency = null, $abbreviate = false, $apply_rounding = false)
{
    $currency = $currency ?? default_currency();

    if ($apply_rounding) {
        $amount = sale_rounding((float)$amount);
    }

    if ($abbreviate) {
        $amount = format_number($amount, $decimals);
    } else {
        $has_fraction = $amount != floor($amount);
        $amount = $has_fraction ? number_format($amount, $decimals) : number_format($amount, 0);
    }

    // Fix SAR symbol - use SVG
    $symbol = $currency->symbol;
    if ($symbol === '^' || $symbol === 'ر.س') {
        $symbol = '<svg class="sar-symbol-svg" width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin-left: 3px;"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"></path><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"></path></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"></rect></clipPath></defs></svg>';
    }

    if ($type == "icon" || $type == "symbol") {
        if ($currency->position == "right") {
            return $amount . $symbol;
        } else {
            return $symbol . $amount;
        }
    } else {
        if ($currency->position == "right") {
            return $amount . ' ' . $currency->code;
        } else {
            return $currency->code . ' ' . $amount;
        }
    }
}

function format_number(float|int $number, int $decimals = 2): string
{
    if ($number >= 1e9) {
        return remove_trailing_zeros($number / 1e9, $decimals) . "B";
    } elseif ($number >= 1e6) {
        return remove_trailing_zeros($number / 1e6, $decimals) . "M";
    } elseif ($number >= 1e3) {
        return remove_trailing_zeros($number / 1e3, $decimals) . "K";
    } else {
        return remove_trailing_zeros($number, $decimals);
    }
}

function remove_trailing_zeros(float|int $number, int $decimals = 2): string
{
    return rtrim(rtrim(number_format($number, $decimals, '.', ''), '0'), '.');
}

function amountInWords(float $amount, int $decimals = 2): string
{
    if (!extension_loaded('intl')) {
        return '';
    }

    $has_fraction = fmod($amount, 1) != 0;
    $amount = $has_fraction ? round($amount, $decimals) : round($amount);

    $formatter = new \NumberFormatter('en_US', \NumberFormatter::SPELLOUT);
    $words = $formatter->format($amount);

    return $words . ' ' . (business_currency()->name ?? '');
}

function convert_money($amount, $currency)
{
    if ($currency->code == default_currency('code') || $amount == 0) {
        return round($amount, 2);
    } else {
        return $amount * $currency->rate / default_currency()->rate;
    }
}

function default_currency($key = null, ?Currency $currency = null): object|int|string
{
    $currency = $currency ?? cache_remember('default_currency', function () {
        $currency = Currency::whereIsDefault(1)->first();

        if (!$currency) {
            $currency = (object)['name' => 'US Dollar', 'code' => 'USD', 'rate' => 1, 'symbol' => '$', 'position' => 'left', 'status' => true, 'is_default' => true,];
        }

        return $currency;
    });

    return $key ? $currency->$key : $currency;
}

function paymentReminderMessage($sale, $business, $customer_name = 'Customer')
{
    $message = get_option('sms-templates-' . $business->id)['purchase_sms'] ?? "Hello [customer_name], kindly clear your outstanding balance of [amount]. For details, contact us at [business_phone].";

    $data = [
        'customer_name' => $customer_name,
        'invoice_no' => $sale->invoiceNumber,
        'due_amount' => $sale->totalAmount,
        'date' => formatted_date($sale->saleDate),
        'business_name' => $business->companyName,
        'business_phone' => $business->phoneNumber,
    ];

    foreach ($data as $key => $value) {
        $message = str_replace("[$key]", $value, $message);
    }

    return $message;
}

function sendMessage($numbers, $contents, $business_id = null)
{
    $gateways = Smsgateway::where('business_id', $business_id ?? auth()->user()->business_id)->where('status', 1)->get();

    foreach ($gateways as $gateway) {
        $gateway->namespace::send_message($gateway, $numbers, $contents);
        session()->put('gateway_id', $gateway->id);

        return [
            'status' => true,
            'message' => "Message send successfully."
        ];
    }
}

function sendPushNotify($request, int|string $total_numbers, ?int $device_id = null): bool
{
    $credentialPath = public_path('uploads/service-account-credentials.json');
    if (!file_exists($credentialPath)) return false;

    $firebase = (new Factory)->withServiceAccount($credentialPath)->createMessaging();
    $tokens = [];

    if ($device_id) {
        $tokens = Device::whereNotNull('device_token')
            ->where('business_id', auth()->user()->business_id)
            ->where('id', $device_id)
            ->pluck('device_token');
    } else {
        $device_ids = Sim::whereIn('id', $request->sim_ids)->pluck('device_id')->unique();
        $tokens = Device::whereNotNull('device_token')->whereIn('id', $device_ids)->pluck('device_token');
    }

    foreach ($tokens as $token) {
        $message = CloudMessage::fromArray([
            'notification' => [
                'title' => 'New ' . $request->type . ' has been created.',
                'body' => $total_numbers . ' ' . $request->type . ' has been created.',
            ],
            'data' => ['type' => $request->type],
            'token' => $token,
        ]);

        try {
            $firebase->send($message);
        } catch (\Kreait\Firebase\Exception\MessagingException $e) {
            Log::error('Firebase Notification Error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    return true;
}

function restorePublicImages()
{
    if (!env('DEMO_MODE')) {
        return true;
    }

    DB::table('sales')->where('business_id', 1)->delete();
    DB::table('sale_returns')->where('business_id', 1)->delete();
    DB::table('purchases')->where('business_id', 1)->delete();
    DB::table('purchase_returns')->where('business_id', 1)->delete();
    DB::table('due_collects')->where('business_id', 1)->delete();
    DB::table('parties')->where('business_id', 1)->delete();
    DB::table('expense_categories')->where('business_id', 1)->delete();
    DB::table('income_categories')->where('business_id', 1)->delete();

    Artisan::call('db:seed', ['--class' => 'DemoSeeder']);
}

if (!function_exists('formatTimeToWords')) {
    function formatTimeToWords(string|null $time): string
    {
        if (empty($time)) {
            return '0';
        }

        if (!preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $time)) {
            return '0';
        }

        $parts = explode(':', $time);

        $hours = isset($parts[0]) ? (int)$parts[0] : 0;
        $minutes = isset($parts[1]) ? (int)$parts[1] : 0;

        $hourString = $hours == 1 ? 'hour' : 'hours';
        $minuteString = $minutes == 1 ? 'minute' : 'minutes';

        $formattedTime = [];

        if ($hours > 0) {
            $formattedTime[] = "$hours $hourString";
        }

        if ($minutes > 0) {
            $formattedTime[] = "$minutes $minuteString";
        }

        return empty($formattedTime) ? '0' : implode(' and ', $formattedTime);
    }
}


function languages()
{
    return [
        'en' => ['name' => 'English', 'flag' => 'us'],
        'ar' => ['name' => 'Arabic', 'flag' => 'sa'],
        'bn' => ['name' => 'Bengali', 'flag' => 'bd'],
        'zh' => ['name' => 'Chinese', 'flag' => 'cn'],
        'fr' => ['name' => 'French', 'flag' => 'fr'],
        'de' => ['name' => 'German', 'flag' => 'de'],
        'hi' => ['name' => 'Hindi', 'flag' => 'in'],
        'es' => ['name' => 'Spanish', 'flag' => 'es'],
        'ja' => ['name' => 'Japanese', 'flag' => 'jp'],
        'rum' => ['name' => 'Romanian', 'flag' => 'ro'],
        'vi' => ['name' => 'Vietnamese', 'flag' => 'vn'],
        'it' => ['name' => 'Italian', 'flag' => 'it'],
        'th' => ['name' => 'Thai', 'flag' => 'th'],
        'bs' => ['name' => 'Bosnian', 'flag' => 'ba'],
        'nl' => ['name' => 'Dutch', 'flag' => 'nl'],
        'pt' => ['name' => 'Portuguese', 'flag' => 'pt'],
        'pl' => ['name' => 'Polish', 'flag' => 'pl'],
        'hu' => ['name' => 'Hungarian', 'flag' => 'hu'],
        'fi' => ['name' => 'Finnish', 'flag' => 'fi'],
        'el' => ['name' => 'Greek', 'flag' => 'gr'],
        'ko' => ['name' => 'Korean', 'flag' => 'kr'],
        'ms' => ['name' => 'Malay', 'flag' => 'my'],
        'id' => ['name' => 'Indonesian', 'flag' => 'id'],
        'fa' => ['name' => 'Persian', 'flag' => 'ir'],
        'tr' => ['name' => 'Turkish', 'flag' => 'tr'],
        'sr' => ['name' => 'Serbian', 'flag' => 'rs'],
        'km' => ['name' => 'Khmer', 'flag' => 'khm'],
        'uk' => ['name' => 'Ukrainian', 'flag' => 'ua'],
        'lo' => ['name' => 'Lao', 'flag' => 'la'],
        'ru' => ['name' => 'Russian', 'flag' => 'ru'],
        'cs' => ['name' => 'Czech', 'flag' => 'cz'],
        'kn' => ['name' => 'Kannada', 'flag' => 'ka'],
        'mr' => ['name' => 'Marathi', 'flag' => 'mh'],
        'sv' => ['name' => 'Swedish', 'flag' => 'se'],
        'da' => ['name' => 'Danish', 'flag' => 'dk'],
        'ur' => ['name' => 'Urdu', 'flag' => 'pk'],
        'sq' => ['name' => 'Albanian', 'flag' => 'al'],
        'sk' => ['name' => 'Slovak', 'flag' => 'sk'],
        'bur' => ['name' => 'Burmese', 'flag' => 'mm'],
        'ti' => ['name' => 'Tigrinya', 'flag' => 'er'],
        'kz' => ['name' => 'Kazakh', 'flag' => 'kz'],
        'az' => ['name' => 'Azerbaijani', 'flag' => 'az'],
        'zh-cn' => ['name' => 'Chinese (CN)', 'flag' => 'zh-cn'],
        'zh-tw' => ['name' => 'Chinese (TW)', 'flag' => 'zh-tw'],
        'pt-br' => ['name' => 'Portuguese (BR)', 'flag' => 'pt-br'],
        'tz' => ['name' => 'Swahili', 'flag' => 'tz'],
        'ps' => ['name' => 'Pashto', 'flag' => 'af'],
        'prs' => ['name' => 'Dari', 'flag' => 'afdari'],
        'ca' => ['name' => 'Catalan', 'flag' => 'ad'],
        'bt' => ['name' => 'Dzongkha', 'flag' => 'dz'],
        'drcfr' => ['name' => 'Congo (DRC)', 'flag' => 'drc'],
        'cgfr' => ['name' => 'Congo (Republic)', 'flag' => 'cg'],
        'escr' => ['name' => 'Costa Rica (Spanish)', 'flag' => 'cr'],
        'enbw' => ['name' => 'Botswana (English)', 'flag' => 'bw'],
        'bws' => ['name' => 'Botswana (Setswana)', 'flag' => 'bws'],
        'deat' => ['name' => 'Austria(German)', 'flag' => 'at'],
        'enbs' => ['name' => 'Bahamas(English)', 'flag' => 'bs'],
        'arbh' => ['name' => 'Bahrain(Arabic)', 'flag' => 'bh'],
        'pt-ao' => ['name' => 'Angola(Portuguese)', 'flag' => 'ao'],
        'es-ar' => ['name' => 'Argentina(Spanish)', 'flag' => 'ar'],
        'hy' => ['name' => 'Armenian', 'flag' => 'am'],
        'au-en' => ['name' => 'Australia', 'flag' => 'au'],
        'bb-en' => ['name' => 'Barbados(English)', 'flag' => 'bb'],
        'be' => ['name' => 'Belarusian', 'flag' => 'by'],
        'nl-be' => ['name' => 'Belgium(Dutch)', 'flag' => 'be'],
        'bz-en' => ['name' => 'Belize(English)', 'flag' => 'bz'],
        'bj-fr' => ['name' => 'Benin(French)', 'flag' => 'bj'],
        'bo-es' => ['name' => 'Bolivia(Spanish)', 'flag' => 'bo'],
        'bn-ms' => ['name' => 'Brunei(Malay)', 'flag' => 'bn'],
        'bg' => ['name' => 'Bulgarian', 'flag' => 'bg'],
        'bf-fr' => ['name' => 'Burkina Faso(French)', 'flag' => 'bf'],
        'cm-fr' => ['name' => 'Cameroon(French)', 'flag' => 'cm'],
        'ca-en' => ['name' => 'Canada(English)', 'flag' => 'ca'],
        'cl-es' => ['name' => 'Chile(Spanish)', 'flag' => 'cl'],
        'co-es' => ['name' => 'Colombia(Spanish)', 'flag' => 'co'],
        'km-ar' => ['name' => 'Comoros(Arabic)', 'flag' => 'km'],
        'hr' => ['name' => 'Croatian', 'flag' => 'hr'],
        'cu-es' => ['name' => 'Cuba(Spanish)', 'flag' => 'cu'],
        'cy-el' => ['name' => 'Cyprus(Greek)', 'flag' => 'cy'],
        'dj-fr' => ['name' => 'Djibouti(French)', 'flag' => 'dj'],
        'dm-en' => ['name' => 'Dominica(English)', 'flag' => 'dm'],
        'tet' => ['name' => 'Tetum', 'flag' => 'tl'],
        'ec-es' => ['name' => 'Ecuador(Spanish)', 'flag' => 'ec'],
        'eg-ar' => ['name' => 'Egypt(Arabic)', 'flag' => 'eg'],
        'sv-es' => ['name' => 'El Salvador(Spanish)', 'flag' => 'sv'],
        'gq-es' => ['name' => 'Equatorial Guinea(Spanish)', 'flag' => 'gq'],
        'et' => ['name' => 'Estonian', 'flag' => 'ee'],
        'ss' => ['name' => 'Swati', 'flag' => 'sz'],
        'am' => ['name' => 'Amharic', 'flag' => 'et'],
        'fj' => ['name' => 'Fijian', 'flag' => 'fj'],
        'ga-fr' => ['name' => 'Gabon(French)', 'flag' => 'ga'],
        'gm-en' => ['name' => 'Gambia(English)', 'flag' => 'gm'],
        'ka' => ['name' => 'Georgian', 'flag' => 'ge'],
        'gh-en' => ['name' => 'Ghana(English)', 'flag' => 'gh'],
        'gd-en' => ['name' => 'Grenada(English)', 'flag' => 'gd'],
        'gt-en' => ['name' => 'Guatemala(English)', 'flag' => 'gt'],
        'gn-fr' => ['name' => 'Guinea(French)', 'flag' => 'gn'],
        'gy-en' => ['name' => 'Guyana(English)', 'flag' => 'gy'],
        'ht-fr' => ['name' => 'Haiti(French)', 'flag' => 'ht'],
        'hn-es' => ['name' => 'Honduras(Spanish)', 'flag' => 'hn'],
    ];
}

// BUSINESS PANEL

// user role permission
if (!function_exists('visible_permission')) {
    function visible_permission($permission)
    {
        $user = auth()->user();

        // Ensure the user is authenticated and has a business_id
        if (!$user || !$user->business_id) {
            return false;
        }

        // Handle visibility field directly as an array or decode it if it's a string
        $permissions = is_array($user->visibility)
            ? $user->visibility
            : json_decode($user->visibility, true);

        return $permissions[$permission] ?? false;
    }
}

function get_business_option($key)
{
    $cacheKey = "business_setting_" . auth()->user()->business_id;

    return Cache::remember($cacheKey, now()->addDay(), function () use ($key) {
        if ($key == 'business-settings') {
            return Option::where('key', 'business-settings')
                ->whereJsonContains('value->business_id', auth()->user()->business_id)
                ->first()
                ->value ?? null;
        }
        return null;
    });
}

function plan_data($business_id = null)
{
    $business_id = $business_id ?? auth()->user()->business_id;

    return cache_remember('plan-data-' . $business_id, function () use ($business_id) {
        $planSubscribe = PlanSubscribe::with('plan:id,subscriptionName,addon_domain_limit,subdomain_limit,allow_multibranch')->where('business_id', $business_id)->latest()->first();

        if ($planSubscribe) {
            $business = Business::findOrFail($planSubscribe->business_id);
            $planSubscribe->will_expire = $business->will_expire;
        }
        return $planSubscribe;
    });
}

function branch_count()
{
    $business_id = auth()->user()->business_id;

    return cache_remember('branch-count-' . $business_id, function () use ($business_id) {
        $totalBranch = Branch::where('business_id', $business_id)->count();

        return $totalBranch;
    });
}

function multibranch_active()
{
    return plan_data()['allow_multibranch'] ?? false;
}

function business_currency($business_id = null)
{
    $business_id = $business_id ?? auth()->user()->business_id;

    return cache_remember("business_currency_{$business_id}", function () use ($business_id) {
        $businessCurrency = UserCurrency::where('business_id', $business_id)->first() ?? Currency::where('is_default', 1)->first();;

        if ($businessCurrency) {
            return (object)[
                'name' => $businessCurrency->name,
                'rate' => $businessCurrency->rate,
                'code' => $businessCurrency->code,
                'symbol' => $businessCurrency->symbol,
                'position' => $businessCurrency->position,
            ];
        }

        return default_currency();
    });
}

function sale_rounding(?float $amount = null, ?string $round_option = null): float|string
{
    $business_id = auth()->user()->business_id;

    // If $round_option is not passed, try to fetch from settings
    if (is_null($round_option)) {
        $round_option = cache_remember("business_sale_rounding_{$business_id}", function () use ($business_id) {
            return Option::where('key', 'business-settings')
                ->whereJsonContains('value->business_id', $business_id)
                ->first()
                ->value['sale_rounding_option'] ?? 'none';
        });
    }

    if (is_null($amount)) {
        return $round_option;
    }

    // Apply rounding if amount is provided
    return match ($round_option) {
        'round_up' => ceil($amount),
        'nearest_whole_number' => round($amount),
        'nearest_0.05' => round($amount * 20) / 20,
        'nearest_0.1' => round($amount * 10) / 10,
        'nearest_0.5' => round($amount * 2) / 2,
        default => $amount,
    };
}

function moduleCheck($module)
{
    $module = Module::find($module);

    if ($module && $module->isEnabled()) {
        return true;
    }

    return false;
}

function remaining_days($date)
{
    $today = \Carbon\Carbon::today();
    $expiry = \Carbon\Carbon::parse($date);
    $diff = $today->diffInDays($expiry, false);

    return $diff > 0 ? "$diff days" : "";
}

// update RemainingBalance
function updateBalance($amount, string $type, $branch_id = null)
{
    $amount = is_numeric($amount) ? (float)$amount : 0;
    $businessId = auth()->user()->business_id;

    // if active branch, then update active branch
    $branch = auth()->user()->active_branch;
    if ($branch) {
        if ($type == 'increment') {
            $branch->increment('branchRemainingBalance', $amount);
        } elseif ($type == 'decrement') {
            $branch->decrement('branchRemainingBalance', $amount);
        }
        return;
    }

    //If branch_id is provided, update that branch
    if ($branch_id) {
        $branch = Branch::find($branch_id);
        if ($branch) {
            if ($type == 'increment') {
                $branch->increment('branchRemainingBalance', $amount);
            } elseif ($type == 'decrement') {
                $branch->decrement('branchRemainingBalance', $amount);
            }
        }
        return;
    }

    // If no branch, update business balance
    $business = Business::find($businessId);
    if ($business) {
        if ($type == 'increment') {
            $business->increment('remainingShopBalance', $amount);
        } elseif ($type == 'decrement') {
            $business->decrement('remainingShopBalance', $amount);
        }
    }
}

function manipulateBranchData($business_id)
{
    $business = auth()->user()->business;
    $shop_owner = User::where(['business_id' => $business_id, 'role' => 'shop-owner'])->firstOrFail();

    $branch = Branch::create([
        'is_main' => 1,
        'email' => $shop_owner->email,
        'name' => $business->companyName,
        'phone' => $business->phoneNumber,
        'address' => $business->address
    ]);

    $updates = [
        'users'            => ['branch_id' => $branch->id, 'where' => ['role' => 'staff']],
        'stocks'           => ['branch_id' => $branch->id, 'where' => ['business_id' => $business_id]],
        'product_settings' => ['branch_id' => $branch->id],
        'sale_returns'     => ['branch_id' => $branch->id],
        'purchase_returns' => ['branch_id' => $branch->id],
        'expenses'         => ['branch_id' => $branch->id],
        'incomes'          => ['branch_id' => $branch->id],
        'sales'            => ['branch_id' => $branch->id],
        'purchases'        => ['branch_id' => $branch->id],
        'due_collects'     => ['branch_id' => $branch->id],
        'parties'          => ['branch_id' => $branch->id],
        'combo_products'   => ['branch_id' => $branch->id],
        'transactions'     => ['branch_id' => $branch->id],
    ];

    foreach ($updates as $table => $data) {
        $query = DB::table($table);
        if (!empty($data['where'])) {
            $query->where($data['where']);
            unset($data['where']);
        }
        $query->update($data);
    }

    if (moduleCheck('HrmAddon')) {
        DB::table('holidays')->update(['branch_id' => $branch->id]);
        DB::table('attendances')->update(['branch_id' => $branch->id]);
        DB::table('leaves')->update(['branch_id' => $branch->id]);
        DB::table('payrolls')->update(['branch_id' => $branch->id]);
        DB::table('employees')->update(['branch_id' => $branch->id]);
    }

    if (moduleCheck('WarehouseAddon')) {
        DB::table('warehouses')->update(['branch_id' => $branch->id]);
        DB::table('transfers')->update([
            'from_branch_id' => $branch->id,
            'to_branch_id'   => $branch->id
        ]);
    }

    return true;
}

function get_root_domain()
{
    $appUrl = config('app.url');
    return parse_url($appUrl, PHP_URL_HOST);
}

function checkDomainStatus($domain)
{
    $result = [
        'domain' => $domain,
        'exists' => false,
        'http'   => false,
        'https'  => false,
    ];

    // 1. Check if domain resolves (DNS record exists)
    if (dns_get_record($domain, DNS_A) || dns_get_record($domain, DNS_AAAA)) {
        $result['exists'] = true;

        // 2. Check HTTP (port 80)
        try {
            $response = Http::timeout(5)->get("http://{$domain}");
            if ($response->successful()) {
                $result['http'] = true;
            }
        } catch (\Exception $e) {
            $result['http'] = false;
        }

        // 3. Check HTTPS (port 443)
        try {
            $response = Http::timeout(5)->get("https://{$domain}");
            if ($response->successful()) {
                $result['https'] = true;
            }
        } catch (\Exception $e) {
            $result['https'] = false;
        }
    }

    return $result;
}

function custom_reports()
{
    $business_id = auth()->user()->business_id;

    if (moduleCheck('CustomReportsAddon')) {
        return cache_remember('custom-reports-' . $business_id, function () use ($business_id) {
            return Modules\CustomReportsAddon\App\Models\CustomReport::where('business_id', auth()->user()->business_id)->where('status', 1)->get();
        });
    } else {
        return [];
    }
}

function cash_balance()
{
    $businessId = auth()->user()->business_id;

    if (!$businessId) {
        return 0;
    }

    // Sum of all credits and bank_to_cash
    $totalCredit = Transaction::where('business_id', $businessId)
        ->where(function ($q) {
            $q->where('type', 'credit')
                ->orWhere('transaction_type', 'bank_to_cash');
        })
        ->sum('amount');

    // Sum of all debits and cash_to_bank
    $totalDebit = Transaction::where('business_id', $businessId)
        ->where(function ($q) {
            $q->where('type', 'debit')
                ->orWhere('transaction_type', 'cash_to_bank');
        })
        ->sum('amount');

    return $totalCredit - $totalDebit;
}

function transaction_types($transactions): string
{
    if (!$transactions) {
        return '';
    }

    return $transactions
        ->map(function ($transaction) {
            if (
                $transaction->transaction_type === 'bank_payment' &&
                !empty($transaction->paymentType?->name)
            ) {
                return $transaction->paymentType->name;
            }

            return $transaction->transaction_type
                ? ucfirst(explode('_', $transaction->transaction_type)[0])
                : '';
        })
        ->filter()
        ->unique()
        ->implode(', ');
}

if (!function_exists('generateZatcaQrCode')) {
    function generateZatcaQrCode($sellerName, $vatRegistrationNumber, $timestamp, $invoiceTotal, $vatTotal, $xmlHash = null, $ecdsaSignature = null, $publicKey = null, $stampSignature = null)
    {
        $data = [
            1 => $sellerName,
            2 => $vatRegistrationNumber,
            3 => $timestamp,
            4 => $invoiceTotal,
            5 => $vatTotal
        ];

        // Add Phase 2 tags if available
        if ($xmlHash) {
            $data[6] = $xmlHash;
        }
        if ($ecdsaSignature) {
            $data[7] = $ecdsaSignature;
        }
        if ($publicKey) {
            $data[8] = $publicKey;
        }
        if ($stampSignature) {
            $data[9] = $stampSignature;
        }

        $result = '';
        foreach ($data as $tag => $value) {
            $valueStr = (string) $value;
            $length = strlen($valueStr);
            $result .= chr($tag) . chr($length) . $valueStr;
        }

        return base64_encode($result);
    }
}

if (!function_exists('checkZatcaComplianceIssues')) {
    /**
     * Check ZATCA compliance issues for a sale invoice
     *
     * @param \App\Models\Sale $sale
     * @return array
     */
    function checkZatcaComplianceIssues($sale)
    {
        $issues = [];

        // Load business with zatca_setting
        $business = $sale->business ?? \App\Models\Business::find($sale->business_id);

        if (!$business) {
            $issues[] = __('Business not found');
            return $issues;
        }

        // Check ZATCA settings
        if (empty($business->zatca_setting)) {
            $issues[] = __('ZATCA settings not configured');
            return $issues;
        }

        $zatcaSetting = $business->zatca_setting;

        // Check CSID
        if (empty($zatcaSetting['csid'])) {
            $issues[] = __('ZATCA CSID not configured');
        }

        // Check Secret
        if (empty($zatcaSetting['secret'])) {
            $issues[] = __('ZATCA Secret not configured');
        }

        // Check Private Key
        if (empty($zatcaSetting['private_key'])) {
            $issues[] = __('ZATCA Private Key not configured');
        }

        // Check Business VAT Number
        if (empty($business->vat_no)) {
            $issues[] = __('Business VAT number is missing');
        }

        // Check Business Address
        if (empty($business->address)) {
            $issues[] = __('Business address is missing');
        }

        // Check Business Company Name
        if (empty($business->companyName)) {
            $issues[] = __('Business company name is missing');
        }

        // Check Sale UUID
        if (empty($sale->uuid)) {
            $issues[] = __('Invoice UUID is missing');
        }

        // Check Sale has details
        if ($sale->details->count() == 0) {
            $issues[] = __('Invoice has no items');
        }

        // Check Sale Date
        if (empty($sale->saleDate)) {
            $issues[] = __('Invoice date is missing');
        }

        // Check VAT amount calculation
        if ($sale->totalAmount > 0 && $sale->vat_amount === null) {
            $issues[] = __('VAT amount is not calculated');
        }

        // Check if sale has been returned completely
        if ($sale->details->sum('quantities') == 0) {
            $issues[] = __('Invoice has been completely returned');
        }

        return $issues;
    }
}

/**
 * Get admin logo from settings with fallback
 */
if (!function_exists('get_admin_logo')) {
    function get_admin_logo(): string
    {
        $general = get_option('general');
        return $general['admin_logo'] ?? 'assets/images/Logo.png';
    }
}

/**
 * Get login page logo from settings with fallback
 */
if (!function_exists('get_login_page_logo')) {
    function get_login_page_logo(): string
    {
        $general = get_option('general');
        return $general['login_page_logo'] ?? 'assets/images/Logo.png';
    }
}

/**
 * Get login page image from settings with fallback
 */
if (!function_exists('get_login_page_image')) {
    function get_login_page_image(): string
    {
        $general = get_option('general');
        return $general['login_page_image'] ?? 'assets/images/login.png';
    }
}

/**
 * Get main header logo from settings with fallback
 */
if (!function_exists('get_main_header_logo')) {
    function get_main_header_logo(): string
    {
        $general = get_option('general');
        return $general['logo'] ?? 'assets/images/Logo.png';
    }
}

/**
 * Get common header logo from settings with fallback
 */
if (!function_exists('get_common_header_logo')) {
    function get_common_header_logo(): string
    {
        $general = get_option('general');
        return $general['common_header_logo'] ?? 'assets/images/Logo.png';
    }
}

/**
 * Get footer logo from settings with fallback
 */
if (!function_exists('get_footer_logo')) {
    function get_footer_logo(): string
    {
        $general = get_option('general');
        return $general['footer_logo'] ?? 'assets/images/Logo.png';
    }
}
/**
 * Get system title from settings with fallback
 */
if (!function_exists('get_system_title')) {
    function get_system_title(): string
    {
        $general = get_option('general');
        return $general['title'] ?? config('app.name', 'BytesPos');
    }
}

/**
 * Get dashboard banner image from settings with fallback
 */
if (!function_exists('get_dashboard_banner_image')) {
    function get_dashboard_banner_image(): string
    {
        $general = get_option('general');
        return $general['dashboard_banner_image'] ?? 'assets/images/dashboard/banner-bg.jpg';
    }
}

/**
 * Get dashboard banner title from settings with fallback
 */
if (!function_exists('get_dashboard_banner_title')) {
    function get_dashboard_banner_title(): string
    {
        $general = get_option('general');
        return $general['dashboard_banner_title'] ?? __('Revolutionizing Your Online Presence');
    }
}

/**
 * Get dashboard banner description from settings with fallback
 */
if (!function_exists('get_dashboard_banner_description')) {
    function get_dashboard_banner_description(): string
    {
        $general = get_option('general');
        return $general['dashboard_banner_description'] ?? __('BYTES guides your business through the digital landscape with innovative solutions and personalized strategies.');
    }
}

/**
 * Get dashboard banner button text from settings with fallback
 */
if (!function_exists('get_dashboard_banner_button_text')) {
    function get_dashboard_banner_button_text(): string
    {
        $general = get_option('general');
        return $general['dashboard_banner_button_text'] ?? __('Create Sale');
    }
}

/**
 * Get favicon from settings with fallback
 */
if (!function_exists('get_favicon')) {
    function get_favicon(): string
    {
        $general = get_option('general');
        return $general['favicon'] ?? 'assets/images/favicon.ico';
    }
}

/**
 * Get primary color from settings with fallback
 */
if (!function_exists('get_primary_color')) {
    function get_primary_color(): string
    {
        $general = get_option('general');
        return $general['primary_color'] ?? '#011646';
    }
}

/**
 * Get secondary color from settings with fallback
 */
if (!function_exists('get_secondary_color')) {
    function get_secondary_color(): string
    {
        $general = get_option('general');
        return $general['secondary_color'] ?? '#0071bc';
    }
}


/**
 * Convert hex color to CSS filter for SVG/IMG colorization
 */
if (!function_exists('hex_to_filter')) {
    function hex_to_filter(string $hex): string
    {
        // Remove # if present
        $hex = str_replace('#', '', $hex);
        
        // Default filter for #011646 (dark blue)
        if (strtolower($hex) === '011646') {
            return 'brightness(0) saturate(100%) invert(7%) sepia(98%) saturate(4299%) hue-rotate(211deg) brightness(94%) contrast(105%)';
        }
        
        // Convert hex to RGB
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        
        // Calculate brightness
        $brightness = ($r + $g + $b) / 3 / 255;
        
        // Simple approximation - works for most colors
        return sprintf('brightness(0) saturate(100%) brightness(%.2f)', $brightness);
    }
}


/**
 * Get currency symbol with SVG support for SAR
 * Replaces ^ symbol with official Saudi Riyal SVG icon
 */
if (!function_exists('currency_symbol_svg')) {
    function currency_symbol_svg($symbol = null, $code = null): string
    {
        // Get currency if not provided
        if ($symbol === null || $code === null) {
            $currency = business_currency();
            $symbol = $currency->symbol ?? '';
            $code = $currency->code ?? '';
        }
        
        // Check if currency is SAR
        $isSAR = $code === 'SAR' || $symbol === '^';
        
        if ($isSAR) {
            // Return SVG icon for SAR with proper inline styling to prevent flash
            return '<svg width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle; margin: 0 3px; width: 11px; height: 12px;"><g clip-path="url(#clip0_price_sar_' . uniqid() . ')"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="currentColor"/><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="currentColor"/></g><defs><clipPath id="clip0_price_sar_' . uniqid() . '"><rect width="10.7368" height="12" fill="white"/></clipPath></defs></svg>';
        }
        
        // Return regular symbol for other currencies
        return htmlspecialchars($symbol, ENT_QUOTES, 'UTF-8');
    }
}


/**
 * Check if current business plan allows a specific permission
 */
if (!function_exists('plan_allows')) {
    function plan_allows(string $permission): bool
    {
        $user = auth()->user();
        
        if (!$user || !$user->business) {
            return false;
        }

        return $user->business->allows($permission);
    }
}

/**
 * Check if current business can add more warehouses
 */
if (!function_exists('can_add_warehouse')) {
    function can_add_warehouse(): bool
    {
        $user = auth()->user();
        
        if (!$user || !$user->business) {
            return false;
        }

        return $user->business->canAddWarehouse();
    }
}

/**
 * Check if current business can add more branches
 */
if (!function_exists('can_add_branch')) {
    function can_add_branch(): bool
    {
        $user = auth()->user();
        
        if (!$user || !$user->business) {
            return false;
        }

        return $user->business->canAddBranch();
    }
}

/**
 * Get warehouse limit for current business
 */
if (!function_exists('warehouse_limit')) {
    function warehouse_limit()
    {
        $user = auth()->user();
        
        if (!$user || !$user->business) {
            return 0;
        }

        return $user->business->getWarehouseLimit();
    }
}

/**
 * Get branch limit for current business
 */
if (!function_exists('branch_limit')) {
    function branch_limit()
    {
        $user = auth()->user();
        
        if (!$user || !$user->business) {
            return 0;
        }

        return $user->business->getBranchLimit();
    }
}

/**
 * Get current business plan name
 */
if (!function_exists('current_plan_name')) {
    function current_plan_name(): string
    {
        $user = auth()->user();
        
        if (!$user || !$user->business) {
            return 'N/A';
        }

        $plan = $user->business->plan();
        return $plan ? $plan->subscriptionName : 'N/A';
    }
}


/**
 * Format currency with symbol
 */
if (!function_exists('currency')) {
    function currency($amount, $symbol = null): string
    {
        if ($symbol === null) {
            // Try to get business currency first
            if (auth()->check() && auth()->user()->business_id) {
                $currency = business_currency();
                $symbol = $currency->symbol ?? '';
            } else {
                // For admin/super-admin, use default currency
                $currency = default_currency();
                $symbol = $currency->symbol ?? '';
            }
        }
        
        // Fix SAR symbol - use SVG directly
        if ($symbol === '^' || $symbol === 'ر.س') {
            $symbol = '<svg class="sar-symbol-svg" width="11" height="12" viewBox="0 0 11 12" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_price_5-1)"><path d="M6.68122 10.6309C6.48962 11.0558 6.36297 11.5168 6.31445 12.0003L10.369 11.1384C10.5606 10.7137 10.6872 10.2525 10.7358 9.76904L6.68122 10.6309Z" fill="#298000"></path><path d="M10.3691 8.55619C10.5607 8.13144 10.6873 7.67031 10.7359 7.18683L7.57749 7.85857V6.56725L10.369 5.97403C10.5606 5.54929 10.6873 5.08815 10.7358 4.60467L7.57739 5.27584V0.631863C7.09343 0.903594 6.66363 1.2653 6.31425 1.69195V5.54441L5.05111 5.8129V0.000244141C4.56715 0.27188 4.13735 0.633678 3.78797 1.06033V6.08129L0.961685 6.68186C0.770089 7.1066 0.643345 7.56773 0.594729 8.05122L3.78797 7.3726V8.99879L0.365788 9.72601C0.174192 10.1508 0.0475433 10.6119 -0.000976562 11.0954L3.58109 10.3341C3.87269 10.2735 4.12331 10.1011 4.28625 9.86384L4.94318 8.8899V8.88971C5.01138 8.78895 5.05111 8.66746 5.05111 8.53661V7.10412L6.31425 6.83564V9.41827L10.369 8.55599L10.3691 8.55619Z" fill="#298000"></path></g><defs><clipPath id="clip0_price_5-1"><rect width="10.7368" height="12" fill="white"></rect></clipPath></defs></svg>';
        }
        
        return $symbol . ' ' . number_format($amount, 2);
    }
}
