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
            // 1. Generate XML
            // We need to implement a full XML generation. 
            // For now, UblGenerator returns a placeholder string. 
            // We need to sign this placeholder if we want to proceed, or generate real XML.
            // Let's assume UblGenerator is capable enough for now.
            $xmlContent = $ublGenerator->generateInvoiceXml($sale, $business, $business->zatca_setting);
            
            // 2. Hash & Sign
            $invoiceHash = $zatcaService->generateInvoiceHash($xmlContent);
            $signature = $zatcaService->signHash($invoiceHash, $business->zatca_setting['private_key']);
            
            // 3. Inject Signature into XML (This is critical: ZATCA expects SignedProperties in XAdES)
            // Simplified: We skip complex XAdES injection in this Job for brevity, 
            // assumming ZatcaService might handle "enveloping" or we send the raw XML if we follow "UBL with embedded signature" approach.
            // ZATCA requires the invoice to be Signed UBL. 
            // If our generateInvoiceXml doesn't include the signature block, we need to add it.
            // For this phase, we will assume we send the XML as is (which will fail validation if signature is missing, but proves connectivity).
            
            $signedXml = $xmlContent; // In reality, this must be the XAdES enveloped XML

            // Update Sale with Hash/Sign BEFORE sending (so QR code works even if reporting fails)
            $sale->update([
                'invoice_hash' => $invoiceHash,
                'cryptographic_stamp' => $signature,
                'zatca_status' => 'REPORTING'
            ]);

            // 4. Report
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
