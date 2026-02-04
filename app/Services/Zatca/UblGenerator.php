<?php

namespace App\Services\Zatca;

class UblGenerator
{
    /**
     * Generate UBL XML content for the invoice.
     */
    public function generateInvoiceXml($sale, $business, $zatcaSetting)
    {
        $issueDate = date('Y-m-d', strtotime($sale->saleDate));
        $issueTime = date('H:i:s', strtotime($sale->saleDate));
        
        // Dynamic VAT Rate
        $vatRate = $sale->vat->rate ?? 15.00;
        $vatFactor = 1 + ($vatRate / 100);

        $totalAmount = (float)$sale->totalAmount;
        $taxableAmount = (float)$sale->actual_total_amount / $vatFactor;
        $vatTotal = $totalAmount - $taxableAmount;
        $allowanceTotal = (float)$sale->discountAmount;
        
        $invoiceType = $sale->invoice_type === 'b2b' ? '0100000' : '0200000'; // الضريبية vs المبسطة
        $ublProfile = $sale->invoice_type === 'b2b' ? 'reporting:1.0' : 'reporting:1.0'; // In Phase 2 both use reporting

        // Build line items XML
        $lineItemsXml = '';
        $lineNumber = 1;
        
        foreach ($sale->details as $detail) {
            $itemPrice = (float)$detail->price;
            $qty = (float)$detail->quantities;
            $lineExtensionAmount = $itemPrice * $qty;
            $lineTaxAmount = $lineExtensionAmount * ($vatRate / 100);
            
            $lineItemsXml .= '
    <cac:InvoiceLine>
        <cbc:ID>' . $lineNumber . '</cbc:ID>
        <cbc:InvoicedQuantity unitCode="PCE">' . $qty . '</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="SAR">' . number_format($lineExtensionAmount, 2, '.', '') . '</cbc:LineExtensionAmount>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="SAR">' . number_format($lineTaxAmount, 2, '.', '') . '</cbc:TaxAmount>
            <cbc:RoundingAmount currencyID="SAR">' . number_format($lineExtensionAmount + $lineTaxAmount, 2, '.', '') . '</cbc:RoundingAmount>
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Name>' . htmlspecialchars($detail->product->productName ?? 'Product') . '</cbc:Name>
            <cac:ClassifiedTaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>' . number_format($vatRate, 2, '.', '') . '</cbc:Percent>
                <cac:TaxScheme>
                    <cbc:ID>VAT</cbc:ID>
                </cac:TaxScheme>
            </cac:ClassifiedTaxCategory>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="SAR">' . number_format($itemPrice, 2, '.', '') . '</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>';
            $lineNumber++;
        }
        
        // Build complete UBL 2.1 XML
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" 
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <cbc:ProfileID>' . $ublProfile . '</cbc:ProfileID>
    <cbc:ID>' . htmlspecialchars($sale->invoiceNumber) . '</cbc:ID>
    <cbc:UUID>' . $sale->uuid . '</cbc:UUID>
    <cbc:IssueDate>' . $issueDate . '</cbc:IssueDate>
    <cbc:IssueTime>' . $issueTime . '</cbc:IssueTime>
    <cbc:InvoiceTypeCode name="' . $invoiceType . '">388</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>SAR</cbc:DocumentCurrencyCode>
    <cbc:TaxCurrencyCode>SAR</cbc:TaxCurrencyCode>
    
    <cac:AdditionalDocumentReference>
        <cbc:ID>ICV</cbc:ID>
        <cbc:UUID>' . $sale->id . '</cbc:UUID>
    </cac:AdditionalDocumentReference>
    
    <cac:AdditionalDocumentReference>
        <cbc:ID>PIH</cbc:ID>
        <cac:Attachment>
            <cbc:EmbeddedDocumentBinaryObject mimeCode="text/plain">' . ($sale->previous_hash ?? 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==') . '</cbc:EmbeddedDocumentBinaryObject>
        </cac:Attachment>
    </cac:AdditionalDocumentReference>
    
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PartyIdentification>
                <cbc:ID schemeID="CRN">' . htmlspecialchars($business->vat_no) . '</cbc:ID>
            </cac:PartyIdentification>
            <cac:PostalAddress>
                <cbc:StreetName>' . htmlspecialchars($business->street_name ?? 'N/A') . '</cbc:StreetName>
                <cbc:BuildingNumber>' . htmlspecialchars($business->building_number ?? '0000') . '</cbc:BuildingNumber>
                <cbc:PlotIdentification>0000</cbc:PlotIdentification>
                <cbc:CitySubdivisionName>' . htmlspecialchars($business->district ?? 'N/A') . '</cbc:CitySubdivisionName>
                <cbc:CityName>' . htmlspecialchars($business->city ?? 'Riyadh') . '</cbc:CityName>
                <cbc:PostalZone>' . htmlspecialchars($business->postal_code ?? '00000') . '</cbc:PostalZone>
                <cbc:Country>
                    <cbc:IdentificationCode>' . htmlspecialchars($business->country_code ?? 'SA') . '</cbc:IdentificationCode>
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
                <cbc:StreetName>' . htmlspecialchars($sale->party->street_name ?? 'N/A') . '</cbc:StreetName>
                <cbc:BuildingNumber>' . htmlspecialchars($sale->party->building_number ?? '0000') . '</cbc:BuildingNumber>
                <cbc:PlotIdentification>0000</cbc:PlotIdentification>
                <cbc:CitySubdivisionName>' . htmlspecialchars($sale->party->district ?? 'N/A') . '</cbc:CitySubdivisionName>
                <cbc:CityName>' . htmlspecialchars($sale->party->city ?? 'N/A') . '</cbc:CityName>
                <cbc:PostalZone>' . htmlspecialchars($sale->party->postal_code ?? '00000') . '</cbc:PostalZone>
                <cbc:Country>
                    <cbc:IdentificationCode>' . htmlspecialchars($sale->party->country_code ?? 'SA') . '</cbc:IdentificationCode>
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
            <cbc:Percent>' . number_format($vatRate, 2, '.', '') . '</cbc:Percent>
            <cac:TaxScheme>
                <cbc:ID>VAT</cbc:ID>
            </cac:TaxScheme>
        </cac:TaxCategory>
    </cac:AllowanceCharge>' : '') . '
    
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="SAR">' . number_format($taxableAmount, 2, '.', '') . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>' . number_format($vatRate, 2, '.', '') . '</cbc:Percent>
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
        <cbc:LineExtensionAmount currencyID="SAR">' . number_format($taxableAmount + $allowanceTotal, 2, '.', '') . '</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID="SAR">' . number_format($taxableAmount, 2, '.', '') . '</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID="SAR">' . number_format($totalAmount, 2, '.', '') . '</cbc:TaxInclusiveAmount>
        <cbc:AllowanceTotalAmount currencyID="SAR">' . number_format($allowanceTotal, 2, '.', '') . '</cbc:AllowanceTotalAmount>
        <cbc:PayableAmount currencyID="SAR">' . number_format($totalAmount, 2, '.', '') . '</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    ' . $lineItemsXml . '
</Invoice>';
        
        return $xml;
    }

    /**
     * Generate UBL XML for Subscription Plan.
     */
    public function generateSubscriptionXml($subscribe, $adminBusiness)
    {
        $issueDate = $subscribe->created_at->format('Y-m-d');
        $issueTime = $subscribe->created_at->format('H:i:s');
        
        $vatRate = 15.00; // Standard Saudi VAT
        $vatFactor = 1.15;

        $totalAmount = (float)$subscribe->price;
        $taxableAmount = $totalAmount / $vatFactor;
        $vatTotal = $totalAmount - $taxableAmount;

        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<Invoice xmlns="urn:oasis:names:specification:ubl:schema:xsd:Invoice-2" 
         xmlns:cac="urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2" 
         xmlns:cbc="urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2" 
         xmlns:ext="urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2">
    <cbc:ProfileID>reporting:1.0</cbc:ProfileID>
    <cbc:ID>' . htmlspecialchars($subscribe->invoice_number) . '</cbc:ID>
    <cbc:UUID>' . $subscribe->uuid . '</cbc:UUID>
    <cbc:IssueDate>' . $issueDate . '</cbc:IssueDate>
    <cbc:IssueTime>' . $issueTime . '</cbc:IssueTime>
    <cbc:InvoiceTypeCode name="0100000">388</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>SAR</cbc:DocumentCurrencyCode>
    <cbc:TaxCurrencyCode>SAR</cbc:TaxCurrencyCode>
    
    <cac:AdditionalDocumentReference>
        <cbc:ID>ICV</cbc:ID>
        <cbc:UUID>' . $subscribe->id . '</cbc:UUID>
    </cac:AdditionalDocumentReference>
    
    <cac:AdditionalDocumentReference>
        <cbc:ID>PIH</cbc:ID>
        <cac:Attachment>
            <cbc:EmbeddedDocumentBinaryObject mimeCode="text/plain">' . ($subscribe->previous_hash ?? 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==') . '</cbc:EmbeddedDocumentBinaryObject>
        </cac:Attachment>
    </cac:AdditionalDocumentReference>
    
    <cac:AccountingSupplierParty>
        <cac:Party>
            <cac:PostalAddress>
                <cbc:StreetName>' . htmlspecialchars($adminBusiness->street_name) . '</cbc:StreetName>
                <cbc:BuildingNumber>' . htmlspecialchars($adminBusiness->building_number) . '</cbc:BuildingNumber>
                <cbc:CitySubdivisionName>' . htmlspecialchars($adminBusiness->district) . '</cbc:CitySubdivisionName>
                <cbc:CityName>' . htmlspecialchars($adminBusiness->city) . '</cbc:CityName>
                <cbc:PostalZone>' . htmlspecialchars($adminBusiness->postal_code) . '</cbc:PostalZone>
                <cac:Country>
                    <cbc:IdentificationCode>' . htmlspecialchars($adminBusiness->country_code) . '</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cbc:CompanyID>' . htmlspecialchars($adminBusiness->vat_no) . '</cbc:CompanyID>
                <cac:TaxScheme><cbc:ID>VAT</cbc:ID></cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>' . htmlspecialchars($adminBusiness->companyName) . '</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingSupplierParty>
    
    <cac:AccountingCustomerParty>
        <cac:Party>
            <cac:PostalAddress>
                <cbc:StreetName>' . htmlspecialchars($subscribe->business->street_name ?? 'N/A') . '</cbc:StreetName>
                <cbc:BuildingNumber>' . htmlspecialchars($subscribe->business->building_number ?? '0000') . '</cbc:BuildingNumber>
                <cbc:CitySubdivisionName>' . htmlspecialchars($subscribe->business->district ?? 'N/A') . '</cbc:CitySubdivisionName>
                <cbc:CityName>' . htmlspecialchars($subscribe->business->city ?? 'N/A') . '</cbc:CityName>
                <cbc:PostalZone>' . htmlspecialchars($subscribe->business->postal_code ?? '00000') . '</cbc:PostalZone>
                <cac:Country>
                    <cbc:IdentificationCode>' . htmlspecialchars($subscribe->business->country_code ?? 'SA') . '</cbc:IdentificationCode>
                </cac:Country>
            </cac:PostalAddress>
            <cac:PartyTaxScheme>
                <cbc:CompanyID>' . htmlspecialchars($subscribe->business->vat_no ?? 'N/A') . '</cbc:CompanyID>
                <cac:TaxScheme><cbc:ID>VAT</cbc:ID></cac:TaxScheme>
            </cac:PartyTaxScheme>
            <cac:PartyLegalEntity>
                <cbc:RegistrationName>' . htmlspecialchars($subscribe->business->companyName) . '</cbc:RegistrationName>
            </cac:PartyLegalEntity>
        </cac:Party>
    </cac:AccountingCustomerParty>
    
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID="SAR">' . number_format($taxableAmount, 2, '.', '') . '</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
            <cac:TaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>' . number_format($vatRate, 2, '.', '') . '</cbc:Percent>
                <cac:TaxScheme><cbc:ID>VAT</cbc:ID></cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID="SAR">' . number_format($taxableAmount, 2, '.', '') . '</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID="SAR">' . number_format($taxableAmount, 2, '.', '') . '</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID="SAR">' . number_format($totalAmount, 2, '.', '') . '</cbc:TaxInclusiveAmount>
        <cbc:PayableAmount currencyID="SAR">' . number_format($totalAmount, 2, '.', '') . '</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    
    <cac:InvoiceLine>
        <cbc:ID>1</cbc:ID>
        <cbc:InvoicedQuantity unitCode="PCE">1</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID="SAR">' . number_format($taxableAmount, 2, '.', '') . '</cbc:LineExtensionAmount>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID="SAR">' . number_format($vatTotal, 2, '.', '') . '</cbc:TaxAmount>
            <cbc:RoundingAmount currencyID="SAR">' . number_format($totalAmount, 2, '.', '') . '</cbc:RoundingAmount>
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Name>' . htmlspecialchars($subscribe->plan->subscriptionName ?? 'Subscription Plan') . '</cbc:Name>
            <cac:ClassifiedTaxCategory>
                <cbc:ID>S</cbc:ID>
                <cbc:Percent>' . number_format($vatRate, 2, '.', '') . '</cbc:Percent>
                <cac:TaxScheme><cbc:ID>VAT</cbc:ID></cac:TaxScheme>
            </cac:ClassifiedTaxCategory>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID="SAR">' . number_format($taxableAmount, 2, '.', '') . '</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>
</Invoice>';

        return $xml;
    }
}
