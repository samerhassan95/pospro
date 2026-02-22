<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Option;
use App\Models\PlanSubscribe;
use App\Services\Zatca\ZatcaService;
use App\Services\Zatca\UblGenerator;

class ZatcaSubscriptionSettingController extends Controller
{
    public function index()
    {
        $zatcaOption = Option::where('key', 'superadmin_zatca_setting')->first();
        $zatcaSettings = $zatcaOption ? $zatcaOption->value : [
            'status' => 'pending',
            'environment' => 'sandbox',
            'csid' => null,
            'private_key' => null,
            'public_key' => null,
            'secret' => null,
            'request_id' => null,
            'csr_config' => [
                'common_name' => config('app.name'),
                'organization_unit_name' => 'HQ',
                'organization_identifier' => '300000000000003',
                'location' => 'Riyadh',
                'registered_address' => 'King Fahad Road',
                'business_category' => 'Software'
            ]
        ];

        $recentSubscriptions = PlanSubscribe::with(['business', 'plan'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.settings.zatca', compact('zatcaSettings', 'recentSubscriptions', 'zatcaOption'));
    }

    public function update(Request $request)
    {
        $zatcaOption = Option::firstOrNew(['key' => 'superadmin_zatca_setting']);
        $zatcaSettings = $zatcaOption->value ?? [];

        // Save CSR Config
        $zatcaSettings['environment'] = $request->environment;
        $zatcaSettings['building_number'] = $request->building_number;
        $zatcaSettings['postal_code'] = $request->postal_code;
        $zatcaSettings['csr_config'] = [
            'common_name' => $request->common_name,
            'organization_unit_name' => $request->csr_organization_unit_name,
            'organization_identifier' => $request->csr_organization_identifier,
            'location' => $request->csr_location,
            'registered_address' => $request->csr_street,
            'business_category' => $request->csr_industry
        ];

        if ($request->filled('otp')) {
            try {
                $zatcaService = new ZatcaService();
                
                if ($request->environment == 'sandbox' && $request->otp == '000000') {
                    $keys = $zatcaService->generateMockKeys($zatcaSettings['csr_config']);
                    $onboardResult = [
                        'csid' => $keys['certificate'],
                        'secret' => 'MOCK_SECRET_' . Str::random(10),
                        'request_id' => '12345',
                        'private_key' => $keys['private_key'],
                        'public_key' => $keys['public_key']
                    ];
                } else {
                    $onboardResult = $zatcaService->issueComplianceCsid(
                        $request->otp,
                        $zatcaSettings['csr_config'],
                        $request->environment
                    );
                }

                $zatcaSettings['csid'] = $onboardResult['csid'];
                $zatcaSettings['secret'] = $onboardResult['secret'];
                $zatcaSettings['request_id'] = $onboardResult['request_id'];
                $zatcaSettings['private_key'] = $onboardResult['private_key'];
                $zatcaSettings['public_key'] = $onboardResult['public_key'];
                $zatcaSettings['status'] = 'connected';
                
                $msg = __('ZATCA Connected Successfully!');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('ZATCA Connection Failed:') . ' ' . $e->getMessage());
            }
        } else {
            $msg = __('Settings Saved.');
        }

        $zatcaOption->value = $zatcaSettings;
        $zatcaOption->save();

        return redirect()->back()->with('success', $msg);
    }

    public function testInvoice($id)
    {
        $subscribe = PlanSubscribe::findOrFail($id);
        $zatcaOption = Option::where('key', 'superadmin_zatca_setting')->first();
        
        if (!$zatcaOption || empty($zatcaOption->value['csid'])) {
            return response()->json(['success' => false, 'message' => __('Please connect to ZATCA first.')]);
        }

        try {
            $zatcaService = new ZatcaService();
            $ublGenerator = new UblGenerator();

            // Mocking Admin Business helper (since Super Admin isn't a Business record usually)
            $adminBusiness = (object)[
                'companyName' => $zatcaOption->value['csr_config']['common_name'],
                'vat_no' => $zatcaOption->value['csr_config']['organization_identifier'],
                'building_number' => $zatcaOption->value['building_number'] ?? '1234',
                'street_name' => $zatcaOption->value['csr_config']['registered_address'],
                'district' => $zatcaOption->value['csr_config']['organization_unit_name'],
                'city' => $zatcaOption->value['csr_config']['location'],
                'postal_code' => $zatcaOption->value['postal_code'] ?? '12345',
                'country_code' => 'SA'
            ];

            // Generate XML (We need a wrapper for Subscription XML)
            $xmlContent = $ublGenerator->generateSubscriptionXml($subscribe, $adminBusiness);

            $signingResult = $zatcaService->signInvoice(
                $xmlContent,
                $zatcaOption->value['private_key'],
                $zatcaOption->value['csid']
            );

            $response = $zatcaService->checkInvoiceCompliance(
                $signingResult['xml'],
                $subscribe->uuid,
                $signingResult['hash'],
                $zatcaOption->value
            );

            $subscribe->update([
                'zatca_status' => $response['success'] ? 'COMPLIANT' : 'FAILED',
                'zatca_response' => $response['body'],
                'invoice_hash' => $signingResult['hash'],
                'cryptographic_stamp' => $signingResult['signature']
            ]);

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getProductionCsid()
    {
        $zatcaOption = Option::where('key', 'superadmin_zatca_setting')->first();
        if (!$zatcaOption) return redirect()->back()->with('error', __('Settings not found'));
        
        $zatcaSettings = $zatcaOption->value;
        $zatcaService = new ZatcaService();

        try {
            $result = $zatcaService->requestProductionCsid(
                $zatcaSettings['request_id'],
                $zatcaSettings
            );

            $zatcaSettings['csid'] = $result['binarySecurityToken'];
            $zatcaSettings['secret'] = $result['secret'];
            $zatcaSettings['status'] = 'production';
            $zatcaSettings['environment'] = 'production';

            $zatcaOption->value = $zatcaSettings;
            $zatcaOption->save();

            return redirect()->back()->with('success', __('Production CSID Received! You are now LIVE.'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('Failed:') . ' ' . $e->getMessage());
        }
    }
}
