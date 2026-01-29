<?php

namespace Modules\Business\App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Business;
use App\Models\Sale;
use App\Services\Zatca\ZatcaService;
use App\Services\Zatca\UblGenerator;

class ZatcaSettingController extends Controller
{
    /**
     * Display the ZATCA settings page.
     */
    public function index()
    {
        $business = Business::find(auth()->user()->business_id);
        
        // Get some recent sales to test with
        $sales = Sale::where('business_id', $business->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('business::settings.zatca', compact('business', 'sales'));
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
            // OTP is required if we are initiating a new connection
            'otp' => 'nullable|string' 
        ]);

        $business = Business::find(auth()->user()->business_id);

        // Update Business Address Fields (Required for XML)
        $business->update([
            'building_number' => $request->csr_organization_identifier, // Often same as building or ID
            'street_name' => $request->csr_street,
            'city' => $request->csr_location,
            'district' => $request->csr_organization_unit_name, // Mapping to district if needed
        ]);

        $zatcaSettings = $business->zatca_setting ?? [];
        
        // Update CSR Config
        $zatcaSettings['environment'] = $request->environment;
        $zatcaSettings['csr_config'] = [
            'common_name' => $business->companyName,
            'organization_unit_name' => $request->csr_organization_unit_name,
            'organization_identifier' => $request->csr_organization_identifier,
            'location' => $request->csr_location,
            'registered_address' => $request->csr_street,
            'business_category' => $request->csr_industry
        ];

        // Save logic with Service Call
        if ($request->filled('otp')) {
             try {
                 $zatcaService = new ZatcaService();
                 
                 // Support MOCK Onboarding for Sandbox testing
                 if ($request->environment == 'sandbox' && $request->otp == '000000') {
                    // Generate REAL keys using the service but skip ZATCA API
                    $keys = $zatcaService->generateMockKeys($zatcaSettings['csr_config']);
                    $onboardResult = [
                        'csid' => $keys['certificate'], // Use self-signed cert as CSID for mock
                        'secret' => 'MOCK_SECRET_' . Str::random(10),
                        'request_id' => '12345',
                        'private_key' => $keys['private_key'],
                        'public_key' => $keys['public_key']
                    ];
                 } else {
                    // Call REAL ZATCA to get CSID
                    $onboardResult = $zatcaService->issueComplianceCsid(
                        $request->otp,
                        $zatcaSettings['csr_config'],
                        $request->environment
                    );
                 }
                 
                 // Save keys and CSID
                 $zatcaSettings['csid'] = $onboardResult['csid'];
                 $zatcaSettings['secret'] = $onboardResult['secret'];
                 $zatcaSettings['request_id'] = $onboardResult['request_id'];
                 $zatcaSettings['private_key'] = $onboardResult['private_key'];
                 $zatcaSettings['public_key'] = $onboardResult['public_key'];
                 
                 $msg = 'ZATCA Connected Successfully!';
             } catch (\Exception $e) {
                 return redirect()->back()->with('error', 'ZATCA Connection Failed: ' . $e->getMessage());
             }
        } else {
             $msg = 'Settings Saved. OTP was not provided.';
        }

        $business->zatca_setting = $zatcaSettings;
        $business->save();

        return redirect()->back()->with('success', $msg);
    }

    /**
     * Test an invoice for ZATCA compliance.
     */
    public function testInvoice(Request $request, $id)
    {
        $sale = Sale::findOrFail($id);
        $business = Business::find(auth()->user()->business_id);

        if (empty($business->zatca_setting['csid'])) {
            return response()->json([
                'success' => false,
                'message' => 'Please connect to ZATCA (Step 1) before testing invoices.'
            ], 400);
        }

        try {
            $zatcaService = new ZatcaService();
            $ublGenerator = new UblGenerator();

            // Ensure previous hash is set
            if (empty($sale->previous_hash)) {
                $sale->update([
                    'previous_hash' => $zatcaService->getPreviousHash($business->id)
                ]);
                $sale->refresh();
            }

            // 1. Generate XML
            $xmlContent = $ublGenerator->generateInvoiceXml(
                $sale, 
                $business, 
                $business->zatca_setting
            );

            // 2. Sign Invoice
            $signingResult = $zatcaService->signInvoice(
                $xmlContent,
                $business->zatca_setting['private_key'],
                $business->zatca_setting['csid']
            );

            // 3. Check Compliance
            $response = $zatcaService->checkInvoiceCompliance(
                $signingResult['xml'],
                $sale->uuid,
                $signingResult['hash'],
                $business->zatca_setting
            );

            // 4. Update Sale Status
            $sale->update([
                'zatca_status' => $response['success'] ? 'COMPLIANT' : 'FAILED',
                'zatca_response' => json_encode($response['body']),
                'invoice_hash' => $signingResult['hash']
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Compliance check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Request Production CSID.
     */
    public function getProductionCsid()
    {
        $business = Business::find(auth()->user()->business_id);
        $zatcaSettings = $business->zatca_setting;

        if (empty($zatcaSettings['request_id'])) {
            return redirect()->back()->with('error', 'Please complete the compliance testing first.');
        }

        try {
            $zatcaService = new ZatcaService();
            
            $result = $zatcaService->requestProductionCsid(
                $zatcaSettings['request_id'],
                $zatcaSettings
            );

            // Update settings to production
            $zatcaSettings['csid'] = $result['binarySecurityToken'];
            $zatcaSettings['secret'] = $result['secret'];
            $zatcaSettings['status'] = 'production';
            $zatcaSettings['environment'] = 'production';

            $business->zatca_setting = $zatcaSettings;
            $business->save();

            return redirect()->back()->with('success', 'Production CSID Received! System is now live with ZATCA.');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to get Production CSID: ' . $e->getMessage());
        }
    }
}
