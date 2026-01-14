<?php

namespace App\Services\Zatca;

class UblGenerator
{
    /**
     * Generate UBL XML content for the invoice.
     * This is a simplified placeholder. Real implementation requires huge string building or DOM manipulation.
     */
    public function generateInvoiceXml($sale, $business, $zatcaSetting)
    {
        $issueDate = date('Y-m-d', strtotime($sale->saleDate));
        $issueTime = date('H:i:s', strtotime($sale->saleDate));
        
        // Build line items XML
        $lineItemsXml = '';
        $lineNumber = 1;
        $taxableAmount = 0;
        $taxAmount = 0;
        
        foreach ($sale->details as $detail) {
            $itemTotal = $detail->price * $detail->quantities;
            $taxableAmount += $itemTotal;
            
            // Calculate tax for this line (assuming standard 15% VAT)
            $lineTaxAmount = $itemTotal * 0.15;
            $taxAmount += $lineTaxAmount;
            
            $lineItemsXml .= '
    <cac:InvoiceLine>
        <cbc:ID>' . $lineNumber . '</cbc:ID>
        <cbc:InvoicedQuantity unitCode="PCE">' . $detail->quantities . '</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="SAR">' . number_format($itemTotal, 2, '.', '') . '</cbc:LineExtensionAmount>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="SAR">' . number_format($lineTaxAmount, 2, '.', '') . '</cbc:TaxAmount>
            <cbc:RoundingAmount currencyID="SAR">' . number_format($itemTotal + $lineTaxAmount, 2, '.', '') . '</cbc:RoundingAmount>
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Name>' . htmlspecialchars($detail->product->productName ?? 'Product') . '</cbc:Name>
            <cac:ClassifiedTaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>15.00</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:ClassifiedTaxCategory>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="SAR">' . number_format($detail->price, 2, '.', '') . '</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>';
            $lineNumber++;
        }
        
        // Calculate totals
        $subtotal = $taxableAmount;
        $vatTotal = $sale->vat_amount ?? $taxAmount;
        $totalWithVat = $sale->totalAmount;
        $allowanceTotal = $sale->discountAmount ?? 0;
        
        // Build complete UBL 2.1 XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" 
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <cbc:ProfileID>reporting:1.0</cbc:ProfileID>
    <cbc:ID>' . htmlspecialchars($sale->invoiceNumber) . '</cbc:ID>
    <cbc:UUID>' . $sale->uuid . '</cbc:UUID>
    <cbc:IssueDate>' . $issueDate . '</cbc:IssueDate>
    <cbc:IssueTime>' . $issueTime . '</cbc:IssueTime>
    <cbc:InvoiceTypeCode name="0100000">388</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>SAR</cbc:DocumentCurrencyCode>
    <cbc:TaxCurrencyCode>SAR</cbc:TaxCurrencyCode>
    
    <cac:AdditionalDocumentReference>
        <cbc:ID>ICV</cbc:ID>
        <cbc:UUID>' . $sale->id . '</cbc:UUID>
    </cac:AdditionalDocumentReference>
    
    <cac:AdditionalDocumentReference>
        <cbc:ID>PIH</cbc:ID>
        <cac:Attachment>
            <cbc:EmbeddedDocumentBinaryObject mimeCode="text/plain">' . base64_encode($sale->previous_hash ?? 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==') . '</cbc:EmbeddedDocumentBinaryObject>
        </cac:Attachment>
    </cac:AdditionalDocumentReference>
    
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="CRN">' . htmlspecialchars($business->vat_no) . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PostalAddress>
                <cbc:StreetName>' . htmlspecialchars($business->address ?? 'N/A') . '</cbc:StreetName>
                <cbc:BuildingNumber>0000</cbc:BuildingNumber>
                <cbc:PlotIdentification>0000</cbc:PlotIdentification>
                <cbc:CitySubdivisionName>N/A</cbc:CitySubdivisionName>
                <cbc:CityName>' . htmlspecialchars($zatcaSetting['csr_config']['location'] ?? 'Riyadh') . '</cbc:CityName>
                <cbc:PostalZone>00000</cbc:PostalZone>
                <cbc:Country>
                    <cbc:IdentificationCode>SA</cbc:IdentificationCode>
                </cbc:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cbc:CompanyID>' . htmlspecialchars($business->vat_no) . '</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>' . htmlspecialchars($business->companyName) . '</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PostalAddress>
                <cbc:StreetName>' . htmlspecialchars($sale->party->address ?? 'N/A') . '</cbc:StreetName>
                <cbc:BuildingNumber>0000</cbc:BuildingNumber>
                <cbc:PlotIdentification>0000</cbc:PlotIdentification>
                <cbc:CitySubdivisionName>N/A</cbc:CitySubdivisionName>
                <cbc:CityName>N/A</cbc:CityName>
                <cbc:PostalZone>00000</cbc:PostalZone>
                <cbc:Country>
                    <cbc:IdentificationCode>SA</cbc:IdentificationCode>
                </cbc:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>' . htmlspecialchars($sale->party->name ?? 'Walk-in Customer') . '</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    
    <cac:PaymentMeans>
        <cbc:PaymentMeansCode>10</cbc:PaymentMeansCode>
    </cac:PaymentMeans>
    ' . ($allowanceTotal > 0 ? '
    <cac:AllowanceCharge>
        <cbc:ChargeIndicator>false</cbc:ChargeIndicator>
        <cbc:AllowanceChargeReason>discount</cbc:AllowanceChargeReason>
        <cbc:Amount currencyID="SAR">' . number_format($allowanceTotal, 2, '.', '') . '</cbc:Amount>
        <cac:TaxCategory>
            <cbc:ID>S</cbc:ID>
            <cbc:Percent>15.00</cbc:Percent>
            <cac:TaxScheme>
                <cbc:ID>VAT</cbc:ID>
            </cac:TaxScheme>
        </cac:TaxCategory>
    </cac:AllowanceCharge>' : '') . '
    
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="SAR">' . number_format($subtotal - $allowanceTotal, 2, '.', '') . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>15.00</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
    </cac:TaxTotal>
    
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="SAR">' . number_format($subtotal, 2, '.', '') . '</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID="SAR">' . number_format($subtotal - $allowanceTotal, 2, '.', '') . '</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID="SAR">' . number_format($totalWithVat, 2, '.', '') . '</cbc:TaxInclusiveAmount>
        <cbc:AllowanceTotalAmount currencyID="SAR">' . number_format($allowanceTotal, 2, '.', '') . '</cbc:AllowanceTotalAmount>
        <cbc:PayableAmount currencyID="SAR">' . number_format($totalWithVat, 2, '.', '') . '</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    ' . $lineItemsXml . '
</Invoice>';
        
        return $xml;
    }
}
