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
            // Ensure previous hash is set
            if (empty($sale->previous_hash)) {
                $sale->update([
                    'previous_hash' => $zatcaService->getPreviousHash($business->id)
                ]);
                $sale->refresh();
            }

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

            // 2. Report/Clear to ZATCA
            if ($sale->invoice_type === 'b2b') {
                $response = $zatcaService->clearanceInvoice($signedXml, $sale->uuid, $invoiceHash, $business->zatca_setting);
            } else {
                $response = $zatcaService->reportInvoice($signedXml, $sale->uuid, $invoiceHash, $business->zatca_setting);
            }

            // 3. Update Status
            $status = $response['success'] ? ($sale->invoice_type === 'b2b' ? 'CLEARED' : 'REPORTED') : 'FAILED';
            
            // If it's cleared, we might get back the cleared invoice (signed by ZATCA)
            if ($sale->invoice_type === 'b2b' && isset($response['body']['clearedInvoice'])) {
                // Save ZATCA's signature/stamp if needed
                $sale->update(['cryptographic_stamp' => $response['body']['clearedInvoice']]);
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
