<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Models\Business;
use App\Services\Zatca\ZatcaService;
use App\Services\Zatca\UblGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReportSaleToZatca implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $saleId;

    /**
     * Create a new job instance.
     */
    public function __construct($saleId)
    {
        $this->saleId = $saleId;
    }

    /**
     * Execute the job.
     */
    public function handle(ZatcaService $zatcaService, UblGenerator $ublGenerator): void
    {
        $sale = Sale::find($this->saleId);
        if (!$sale) {
            return;
        }

        $business = Business::find($sale->business_id);
        if (!$business || empty($business->zatca_setting) || empty($business->zatca_setting['csid'])) {
            // Not connected to ZATCA
            return;
        }

        try {
            // 1. Generate & Sign XML
            $xmlContent = $ublGenerator->generateInvoiceXml($sale, $business, $business->zatca_setting);
            
            $signingResult = $zatcaService->signInvoice(
                $xmlContent, 
                $business->zatca_setting['private_key'], 
                $business->zatca_setting['csid']
            );
            
            $signedXml = $signingResult['xml'];
            $invoiceHash = $signingResult['hash'];
            $signature = $signingResult['signature'];

            // Update Sale with Hash/Sign BEFORE sending
            $sale->update([
                'invoice_hash' => $invoiceHash,
                'cryptographic_stamp' => $signature,
                'zatca_status' => 'REPORTING'
            ]);

            // 2. Report to ZATCA
            $response = $zatcaService->reportInvoice($signedXml, $sale->uuid, $invoiceHash, $business->zatca_setting);

            // 5. Update Status
            $status = $response['success'] ? 'REPORTED' : 'FAILED';
            if (isset($response['body']['validationResults']) && isset($response['body']['validationResults']['errorMessages'])) {
                 if (count($response['body']['validationResults']['errorMessages']) > 0) {
                     $status = 'FAILED'; // or WARNING
                 }
            }

            $sale->update([
                'zatca_status' => $status,
                'zatca_response' => $response['body']
            ]);

        } catch (\Exception $e) {
            Log::error("ZATCA Reporting Error Sale #{$sale->id}: " . $e->getMessage());
            $sale->update([
                'zatca_status' => 'ERROR',
                'zatca_response' => ['error' => $e->getMessage()]
            ]);
        }
    }
}
