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
        return view('business::settings.moyasar', compact('business', 'moyasar_setting'));
    }

    /**
     * Update Moyasar settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'api_key' => 'required|string',
            'publishable_key' => 'required|string',
        ]);

        $business = Business::find(auth()->user()->business_id);

        $moyasar_setting = [
            'api_key' => $request->api_key,
            'publishable_key' => $request->publishable_key,
        ];

        $business->moyasar_setting = $moyasar_setting;
        $business->save();

        return redirect()->back()->with('success', __('Moyasar Settings updated successfully'));
    }
}
