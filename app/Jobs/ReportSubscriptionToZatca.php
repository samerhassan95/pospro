<?php

namespace App\Jobs;

use App\Models\PlanSubscribe;
use App\Models\Option;
use App\Services\Zatca\ZatcaService;
use App\Services\Zatca\UblGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReportSubscriptionToZatca implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $subscribeId;

    /**
     * Create a new job instance.
     */
    public function __construct($subscribeId)
    {
        $this->subscribeId = $subscribeId;
    }

    /**
     * Execute the job.
     */
    public function handle(ZatcaService $zatcaService, UblGenerator $ublGenerator): void
    {
        $subscribe = PlanSubscribe::with(['business', 'plan'])->find($this->subscribeId);
        if (!$subscribe) return;

        $zatcaOption = Option::where('key', 'superadmin_zatca_setting')->first();
        if (!$zatcaOption || empty($zatcaOption->value['csid'])) {
            return;
        }

        $zatcaSettings = $zatcaOption->value;

        try {
            // Helper for Admin Business
            $adminBusiness = (object)[
                'companyName' => $zatcaSettings['csr_config']['common_name'],
                'vat_no' => $zatcaSettings['csr_config']['organization_identifier'],
                'building_number' => $zatcaSettings['building_number'] ?? '1234',
                'street_name' => $zatcaSettings['csr_config']['registered_address'],
                'district' => $zatcaSettings['csr_config']['organization_unit_name'],
                'city' => $zatcaSettings['csr_config']['location'],
                'postal_code' => $zatcaSettings['postal_code'] ?? '12345',
                'country_code' => 'SA'
            ];

            // 1. Generate XML
            $xmlContent = $ublGenerator->generateSubscriptionXml($subscribe, $adminBusiness);

            // 2. Sign XML
            $signingResult = $zatcaService->signInvoice(
                $xmlContent,
                $zatcaSettings['private_key'],
                $zatcaSettings['csid']
            );

            // 3. Send to ZATCA (Subscriptions are usually B2B)
            $response = $zatcaService->clearanceInvoice(
                $signingResult['xml'],
                $subscribe->uuid,
                $signingResult['hash'],
                $zatcaSettings
            );

            // 4. Update Status
            $subscribe->update([
                'zatca_status' => $response['success'] ? 'CLEARED' : 'FAILED',
                'invoice_hash' => $signingResult['hash'],
                'cryptographic_stamp' => $signingResult['signature'], // Important for QR Code
                'zatca_response' => $response['body']
            ]);

        } catch (\Exception $e) {
            Log::error("ZATCA Reporting Error Subscription #{$subscribe->id}: " . $e->getMessage());
            $subscribe->update([
                'zatca_status' => 'FAILED',
                'zatca_response' => ['error' => $e->getMessage()]
            ]);
        }
    }
}
