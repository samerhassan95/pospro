<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Business;
// use App\Services\Zatca\ZatcaService; // Future integration

class ZatcaSettingController extends Controller
{
    /**
     * Display the ZATCA settings page.
     */
    public function index()
    {
        $business = Business::find(auth()->user()->business_id);
        return view('business::settings.zatca', compact('business'));
    }

    /**
     * Update ZATCA settings and trigger connection (CSID Request).
     */
    public function update(Request $request)
    {
        $request->validate([
            'environment' => 'required',
            'csr_organization_unit_name' => 'required',
            'csr_location' => 'required',
            'csr_street' => 'required',
            'csr_industry' => 'required',
            // OTP is required if we are initiating a new connection, 
            // but we might allow updating just settings without reconnecting in future.
            'otp' => 'nullable|string' 
        ]);

        $business = Business::find(auth()->user()->business_id);

        $zatcaSettings = $business->zatca_setting ?? [];
        
        // Update CSR Config
        $zatcaSettings['environment'] = $request->environment;
        $zatcaSettings['csr_config'] = [
            'common_name' => $business->companyName, // Usually matches Tax Name
            'organization_unit_name' => $request->csr_organization_unit_name,
            'organization_identifier' => $request->csr_organization_identifier,
            'location' => $request->csr_location,
            'registered_address' => $request->csr_street,
            'business_category' => $request->csr_industry
        ];

        // Save logic with Service Call
        if ($request->filled('otp')) {
             try {
                 $zatcaService = new \App\Services\Zatca\ZatcaService();
                 
                 // Call ZATCA to get CSID
                 $onboardResult = $zatcaService->issueComplianceCsid(
                     $request->otp,
                     $zatcaSettings['csr_config'],
                     $request->environment
                 );
                 
                 // Save keys and CSID
                 $zatcaSettings['csid'] = $onboardResult['csid'];
                 $zatcaSettings['secret'] = $onboardResult['secret'];
                 $zatcaSettings['request_id'] = $onboardResult['request_id'];
                 $zatcaSettings['private_key'] = $onboardResult['private_key']; // WARNING: Encrypt this in production!
                 $zatcaSettings['public_key'] = $onboardResult['public_key'];
                 
                 $msg = 'ZATCA Connected Successfully! Compliance CSID Received.';
             } catch (\Exception $e) {
                 return redirect()->back()->with('error', 'ZATCA Connection Failed: ' . $e->getMessage());
             }
        } else {
             $msg = 'Settings Saved. OTP was not provided, so no connection attempt was made.';
        }

        $business->zatca_setting = $zatcaSettings;
        $business->save();

        return redirect()->back()->with('success', $msg);
    }
}
