<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;

class MoyasarSettingController extends Controller
{
    /**
     * Display the Moyasar settings page.
     */
    public function index()
    {
        $business = Business::find(auth()->user()->business_id);
        $moyasar_setting = $business->moyasar_setting ?? [];
        
        // فحص إذا كان السوبر أدمن فعل ميسر
        $superAdminSettings = \App\Models\Setting::where('key', 'moyasar_settings')->first();
        $globalSettings = $superAdminSettings ? json_decode($superAdminSettings->value, true) : [];
        $moyasarEnabled = $globalSettings['is_active'] ?? false;
        
        return view('business::settings.moyasar', compact('business', 'moyasar_setting', 'moyasarEnabled'));
    }

    /**
     * Update Moyasar settings.
     */
    public function update(Request $request)
    {
        // التاجر يحدد البيئة فقط (اختبار أم إنتاج)
        $request->validate([
            'environment' => 'required|in:test,live'
        ]);

        $business = Business::find(auth()->user()->business_id);

        $moyasar_setting = [
            'environment' => $request->environment,
            'updated_at' => now()
        ];

        $business->moyasar_setting = $moyasar_setting;
        $business->save();

        return redirect()->back()->with('success', __('Moyasar environment updated successfully'));
    }
}
