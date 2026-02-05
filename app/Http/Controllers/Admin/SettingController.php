<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\HasUploader;
use App\Http\Controllers\Controller;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{
    use HasUploader;

    public function __construct()
    {
        $this->middleware('permission:settings-read')->only('index');
        $this->middleware('permission:settings-update')->only('update');
    }

    public function index()
    {
        $general = Option::where('key','general')->first();
        $languages = json_decode(file_get_contents(base_path('lang/langlist.json')), true);

        return view('admin.settings.general',compact('general', 'languages'));
    }

    public function update(Request $request, $id)
    {
        // Log what we receive
        \Log::info('Settings Update - Received Data:', [
            'primary_color' => $request->input('primary_color'),
            'secondary_color' => $request->input('secondary_color'),
            'has_primary' => $request->has('primary_color'),
            'has_secondary' => $request->has('secondary_color'),
        ]);
        
        $request->validate([
            'title' => 'required|string|max:100',
            'logo' => 'nullable|image',
            'favicon' => 'nullable|image',
            'common_header_logo' => 'nullable|image',
            'footer_logo' => 'nullable|image',
            'admin_logo' => 'nullable|image',
            'login_page_logo' => 'nullable|image',
            'login_page_image' => 'nullable|image',
            'dashboard_banner_image' => 'nullable|image',
            'dashboard_banner_title' => 'nullable|string|max:255',
            'dashboard_banner_description' => 'nullable|string|max:500',
            'dashboard_banner_button_text' => 'nullable|string|max:100',
            'app_link' => 'nullable|url',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
        ]);

        $general = Option::findOrFail($id);
        Cache::forget($general->key);
        
        // Get current value
        $currentValue = $general->value;
        
        // Build the value array from request
        $valueData = $request->except('_token','_method','logo','favicon','common_header_logo','footer_logo','admin_logo', 'login_page_logo', 'login_page_image', 'dashboard_banner_image');
        
        // Handle image uploads
        $valueData['logo'] = $request->logo ? $this->upload($request, 'logo', $currentValue['logo'] ?? null) : ($currentValue['logo'] ?? null);
        $valueData['favicon'] = $request->favicon ? $this->upload($request, 'favicon', $currentValue['favicon'] ?? null) : ($currentValue['favicon'] ?? null);
        $valueData['common_header_logo'] = $request->common_header_logo ? $this->upload($request, 'common_header_logo', $currentValue['common_header_logo'] ?? null) : ($currentValue['common_header_logo'] ?? null);
        $valueData['footer_logo'] = $request->footer_logo ? $this->upload($request, 'footer_logo', $currentValue['footer_logo'] ?? null) : ($currentValue['footer_logo'] ?? null);
        $valueData['admin_logo'] = $request->admin_logo ? $this->upload($request, 'admin_logo', $currentValue['admin_logo'] ?? null) : ($currentValue['admin_logo'] ?? null);
        $valueData['login_page_logo'] = $request->login_page_logo ? $this->upload($request, 'login_page_logo', $currentValue['login_page_logo'] ?? null) : ($currentValue['login_page_logo'] ?? null);
        $valueData['login_page_image'] = $request->login_page_image ? $this->upload($request, 'login_page_image', $currentValue['login_page_image'] ?? null) : ($currentValue['login_page_image'] ?? null);
        $valueData['dashboard_banner_image'] = $request->dashboard_banner_image ? $this->upload($request, 'dashboard_banner_image', $currentValue['dashboard_banner_image'] ?? null) : ($currentValue['dashboard_banner_image'] ?? null);
        
        // Save
        $general->value = $valueData;
        $general->save();
        
        // Log what was saved
        \Log::info('Settings Update - Saved Data:', [
            'primary_color' => $general->value['primary_color'] ?? 'NOT SAVED',
            'secondary_color' => $general->value['secondary_color'] ?? 'NOT SAVED',
        ]);

        return response()->json([
            'message'   => __('General Setting updated successfully'),
            'redirect'  => route('admin.settings.index')
        ]);
    }
}
