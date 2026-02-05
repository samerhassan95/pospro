<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Crypt;

class SuperAdminMoyasarController extends Controller
{
    public function index()
    {
        $moyasarSettings = Setting::where('key', 'moyasar_settings')->first();
        $settings = $moyasarSettings ? json_decode($moyasarSettings->value, true) : [];
        
        return view('super-admin.moyasar.index', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'test_publishable_key' => 'required|string',
            'test_secret_key' => 'required|string',
            'live_publishable_key' => 'nullable|string',
            'live_secret_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'default_currency' => 'required|string|in:SAR,USD,EUR,AED',
            'commission_rate' => 'nullable|numeric|min:0|max:100'
        ]);

        $settings = [
            'test_publishable_key' => $request->test_publishable_key,
            'test_secret_key' => Crypt::encryptString($request->test_secret_key),
            'live_publishable_key' => $request->live_publishable_key,
            'live_secret_key' => $request->live_secret_key ? Crypt::encryptString($request->live_secret_key) : null,
            'webhook_secret' => $request->webhook_secret ? Crypt::encryptString($request->webhook_secret) : null,
            'default_currency' => $request->default_currency,
            'commission_rate' => $request->commission_rate ?? 0,
            'is_active' => $request->has('is_active'),
            'updated_at' => now()
        ];

        Setting::updateOrCreate(
            ['key' => 'moyasar_settings'],
            ['value' => json_encode($settings)]
        );

        return response()->json([
            'message' => __('Moyasar settings saved successfully.')
        ]);
    }
}