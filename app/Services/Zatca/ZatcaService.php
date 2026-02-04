<?php

namespace App\Services\Zatca;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ZatcaService
{
    /**
     * Generate UUID for the invoice.
     */
    public function generateUuid()
    {
        return \Illuminate\Support\Str::uuid()->toString();
    }

    /**
     * Get the latest reported/cleared invoice hash for a business.
     */
    public function getPreviousHash($businessId)
    {
        $lastSale = \App\Models\Sale::where('business_id', $businessId)
            ->whereNotNull('invoice_hash')
            ->whereIn('zatca_status', ['REPORTED', 'CLEARED', 'COMPLIANT'])
            ->latest()
            ->first();

        return $lastSale ? $lastSale->invoice_hash : 'NWZlY2ViNjZmZmM4NmYzOGQ5NTI3ODZjNmQ2OTZjNzljMmRiYzIzOWRkNGU5MWI0NjcyOWQ3M2EyN2ZiNTdlOQ==';
    }

    /**
     * Full ZATCA XML Signing (XAdES-EPES).
     */
    public function signInvoice($xmlContent, $privateKeyContent, $certificateContent)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xmlContent);

        // 1. Generate Invoice Hash (SHA256 of canonized XML without Signature block)
        $invoiceHash = $this->generateInvoiceHash($xmlContent);

        // 2. Prepare SignedProperties
        $signingTime = date('Y-m-d\TH:i:s\Z');
        $certData = openssl_x509_parse("-----BEGIN CERTIFICATE-----\n" . $this->cleanPem($certificateContent) . "\n-----END CERTIFICATE-----");
        
        $certDigest = base64_encode(hash('sha256', base64_decode($this->cleanPem($certificateContent)), true));
        
        $certIssuer = "";
        if (isset($certData['issuer'])) {
            $issuerParts = [];
            foreach ($certData['issuer'] as $key => $val) {
                if (is_array($val)) $val = $val[0];
                $issuerParts[] = "$key=$val";
            }
            $certIssuer = implode(', ', array_reverse($issuerParts));
        }
        $certSerial = $certData['serialNumber'] ?? "0";

        $signedPropertiesXml = '<xades:SignedProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" Id="xadesSignedProperties">
            <xades:SignedSignatureProperties>
                <xades:SigningTime>' . $signingTime . '</xades:SigningTime>
                <xades:SigningCertificate>
                    <xades:Cert>
                        <xades:CertDigest>
                            <ds:DigestMethod xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                            <ds:DigestValue xmlns:ds="http://www.w3.org/2000/09/xmldsig#">' . $certDigest . '</ds:DigestValue>
                        </xades:CertDigest>
                        <xades:IssuerSerial>
                            <ds:X509IssuerName xmlns:ds="http://www.w3.org/2000/09/xmldsig#">' . $certIssuer . '</ds:X509IssuerName>
                            <ds:X509SerialNumber xmlns:ds="http://www.w3.org/2000/09/xmldsig#">' . $certSerial . '</ds:X509SerialNumber>
                        </xades:IssuerSerial>
                    </xades:Cert>
                </xades:SigningCertificate>
            </xades:SignedSignatureProperties>
        </xades:SignedProperties>';

        $signedPropertiesHash = base64_encode(hash('sha256', $this->canonizeXml($signedPropertiesXml), true));

        // 3. Prepare SignedInfo
        $signedInfoXml = '<ds:SignedInfo xmlns:ds="http://www.w3.org/2000/09/xmldsig#">
            <ds:CanonicalizationMethod Algorithm="http://www.w3.org/2001/10/xml-exc-c14n#"/>
            <ds:SignatureMethod Algorithm="http://www.w3.org/2001/04/xmldsig-more#ecdsa-sha256"/>
            <ds:Reference Id="invoiceReference" URI="">
                <ds:Transforms>
                    <ds:Transform Algorithm="http://www.w3.org/2000/09/xmldsig#enveloped-signature"/>
                </ds:Transforms>
                <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                <ds:DigestValue>' . $invoiceHash . '</ds:DigestValue>
            </ds:Reference>
            <ds:Reference Type="http://uri.etsi.org/01903#SignedProperties" URI="#xadesSignedProperties">
                <ds:DigestMethod Algorithm="http://www.w3.org/2001/04/xmlenc#sha256"/>
                <ds:DigestValue>' . $signedPropertiesHash . '</ds:DigestValue>
            </ds:Reference>
        </ds:SignedInfo>';

        $signatureValue = $this->signHash($this->canonizeXml($signedInfoXml), $privateKeyContent);

        // 4. Assemble Signature Block
        $signatureBlock = '
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
                <ds:Signature xmlns:ds="http://www.w3.org/2000/09/xmldsig#" Id="signature">
                    ' . $signedInfoXml . '
                    <ds:SignatureValue>' . $signatureValue . '</ds:SignatureValue>
                    <ds:KeyInfo>
                        <ds:X509Data>
                            <ds:X509Certificate>' . $this->cleanPem($certificateContent) . '</ds:X509Certificate>
                        </ds:X509Data>
                    </ds:KeyInfo>
                    <ds:Object>
                        <xades:QualifyingProperties xmlns:xades="http://uri.etsi.org/01903/v1.3.2#" Target="signature">
                            ' . $signedPropertiesXml . '
                        </xades:QualifyingProperties>
                    </ds:Object>
                </ds:Signature>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>';

        // Inject into XML (Simplified: Replace placeholder if exists, or prepend to child nodes)
        $signedXml = str_replace('</Invoice>', $signatureBlock . '</Invoice>', $xmlContent);
        
        return [
            'xml' => $signedXml,
            'hash' => $invoiceHash,
            'signature' => $signatureValue
        ];
    }

    /**
     * Generate Invoice Hash (SHA256).
     */
    public function generateInvoiceHash($xmlContent)
    {
        // Remove Signature block if any before hashing
        $xmlContent = preg_replace('/<ext:UBLExtensions>.*?<\/ext:UBLExtensions>/s', '', $xmlContent);
        $canonizedXml = $this->canonizeXml($xmlContent);
        return base64_encode(hash('sha256', $canonizedXml, true));
    }

    /**
     * Generate ECDSA Signature.
     */
    public function signHash($data, $privateKeyContent)
    {
        $privateKey = openssl_get_privatekey($privateKeyContent);
        if (!$privateKey) {
            throw new \Exception("Invalid Private Key: " . openssl_error_string());
        }

        $signature = '';
        openssl_sign($data, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        return base64_encode($signature);
    }

    /**
     * Generate ZATCA QR Code Content (TLV Base64).
     */
    public function generateQrCode($sellerName, $vatNumber, $timestamp, $total, $vatTotal, $hash, $signature, $publicKey)
    {
        $tags = [
            1 => $sellerName,
            2 => $vatNumber,
            3 => $timestamp,
            4 => $total,
            5 => $vatTotal,
            6 => $hash,
            7 => $signature,
            8 => $publicKey
        ];

        $result = '';
        foreach ($tags as $key => $value) {
            $valueStr = (string)$value;
            $length = strlen($valueStr);
            $result .= chr($key);
            if ($length <= 255) {
                $result .= chr($length);
            } else {
                // For values > 255, we use multi-byte or just handle the overflow if necessary
                // Most simple ZATCA decoders expect 1 byte for length <= 255
                $result .= chr(255); 
            }
            $result .= $valueStr;
        }

        return base64_encode($result);
    }

    /**
     * Issue Compliance CSID from ZATCA.
     */
    public function issueComplianceCsid($otp, $csrConfig, $environment = 'sandbox')
    {
        $keys = $this->generateCsrAndKey($csrConfig);
        $csrContent = $keys['csr'];
        $privateKey = $keys['private_key'];
        
        $baseUrl = $this->getBaseUrl($environment);
        $url = $baseUrl . '/compliance';

        $cleanCsr = $this->cleanPem($csrContent);

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'OTP' => $otp,
            'Accept-Version' => 'V2',
            'Content-Type' => 'application/json'
        ])->post($url, [
            'csr' => $cleanCsr
        ]);

        if ($response->failed()) {
            throw new \Exception('ZATCA CPID Failed: ' . $response->body());
        }

        $data = $response->json();
        
        return [
            'csid' => $data['binarySecurityToken'] ?? null,
            'secret' => $data['secret'] ?? null,
            'request_id' => $data['requestID'] ?? null,
            'private_key' => $privateKey,
            'csr' => $csrContent,
            'public_key' => $keys['public_key']
        ];
    }

    /**
     * Generate EC Private Key and CSR using OpenSSL.
     */
    public function generateCsrAndKey($config)
    {
        $oa = [
            "private_key_type" => OPENSSL_KEYTYPE_EC,
            "curve_name" => "secp256k1"
        ];
        
        $privateKeyResource = openssl_pkey_new($oa);
        if (!$privateKeyResource) {
              throw new \Exception("OpenSSL PKey generation failed. Check OpenSSL config.");
        }
        
        openssl_pkey_export($privateKeyResource, $privateKeyPem);
        
        $details = openssl_pkey_get_details($privateKeyResource);
        $publicKeyPem = $details['key'];

        $dn = [
            "commonName" => $config['common_name'], 
            "organizationName" => $config['organization_identifier'],
            "organizationalUnitName" => $config['organization_unit_name'],
            "countryName" => "SA"
        ];
        
        $csrResource = openssl_csr_new($dn, $privateKeyResource, $oa);
        if (!$csrResource) {
            throw new \Exception("CSR Generation Failed: " .  openssl_error_string());
        }
        
        openssl_csr_export($csrResource, $csrPem);

        return [
            'private_key' => $privateKeyPem,
            'public_key' => $publicKeyPem,
            'csr' => $csrPem
        ];
    }

    /**
     * Generate real keys but self-signed cert for Mocking.
     */
    public function generateMockKeys($config)
    {
        $keys = $this->generateCsrAndKey($config);
        
        // Use the CSR and Private Key to make a self-signed certificate
        // 365 days validity
        $csrResource = openssl_csr_get_subject($keys['csr']);
        $privateKeyResource = openssl_pkey_get_private($keys['private_key']);
        
        $certResource = openssl_csr_sign($keys['csr'], null, $privateKeyResource, 365, ['digest_alg' => 'sha256']);
        openssl_x509_export($certResource, $certPem);
        
        return [
            'private_key' => $keys['private_key'],
            'public_key' => $keys['public_key'],
            'certificate' => $certPem
        ];
    }
    
    private function sanitizeConf($str) {
        return preg_replace("/[^a-zA-Z0-9 ]/", "", $str);
    }
    
    public function cleanPem($pem)
    {
        $pem = str_replace(['-----BEGIN CERTIFICATE REQUEST-----', '-----END CERTIFICATE REQUEST-----', '-----BEGIN CERTIFICATE-----', '-----END CERTIFICATE-----', '-----BEGIN EC PRIVATE KEY-----', '-----END EC PRIVATE KEY-----', '-----BEGIN PRIVATE KEY-----', '-----END PRIVATE KEY-----', "\n", "\r"], '', $pem);
        return trim($pem);
    }

    private function getBaseUrl($env)
    {
        if ($env === 'production') {
            return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core';
        } elseif ($env === 'simulation') {
            return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation'; 
        }
        return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal';
    }

    /**
     * Report a Simplified Invoice (B2C) to ZATCA.
     */
    public function reportInvoice($signedXml, $uuid, $invoiceHash, $zatcaSettings)
    {
        return $this->sendToZatca($signedXml, $uuid, $invoiceHash, $zatcaSettings, 'reporting');
    }

    /**
     * Clear a Tax Invoice (B2B) with ZATCA.
     */
    public function clearanceInvoice($signedXml, $uuid, $invoiceHash, $zatcaSettings)
    {
        return $this->sendToZatca($signedXml, $uuid, $invoiceHash, $zatcaSettings, 'clearance');
    }

    /**
     * Check Invoice Compliance.
     */
    public function checkInvoiceCompliance($signedXml, $uuid, $invoiceHash, $zatcaSettings)
    {
        return $this->sendToZatca($signedXml, $uuid, $invoiceHash, $zatcaSettings, 'compliance');
    }

    private function sendToZatca($signedXml, $uuid, $invoiceHash, $zatcaSettings, $type)
    {
        $environment = $zatcaSettings['environment'] ?? 'sandbox';
        $baseUrl = $this->getBaseUrl($environment);
        
        switch ($type) {
            case 'compliance':
                $url = $baseUrl . '/compliance/invoices';
                break;
            case 'clearance':
                $url = $baseUrl . '/invoices/clearance/single';
                break;
            case 'reporting':
            default:
                $url = $baseUrl . '/invoices/reporting/single';
                break;
        }

        $csid = $zatcaSettings['csid'];
        $secret = $zatcaSettings['secret'];

        // Mock Response handling for OTP 000000
        if (str_contains($secret, 'MOCK_SECRET')) {
            return [
                'status' => 200,
                'body' => [
                    'validationResults' => [
                        'infoMessages' => [],
                        'warningMessages' => [],
                        'errorMessages' => [],
                        'status' => 'PASS'
                    ],
                    'reportingStatus' => 'REPORTED',
                    'clearanceStatus' => 'CLEARED'
                ],
                'success' => true
            ];
        }

        $basicAuth = base64_encode("$csid:$secret");

        $body = [
            'invoiceHash' => $invoiceHash,
            'uuid' => $uuid,
            'invoice' => base64_encode($signedXml)
        ];

        // For clearance, we need to set Clearance-Status header
        $headers = [
            'Authorization' => "Basic $basicAuth",
            'Accept-Version' => 'V2',
            'Content-Type' => 'application/json',
            'Accept-Language' => 'en',
        ];

        if ($type === 'clearance') {
            $headers['Clearance-Status'] = '1'; // 1 for clearance, 0 for reporting (default)
        } else {
            $headers['Clearance-Status'] = '0';
        }

        $response = \Illuminate\Support\Facades\Http::withHeaders($headers)->post($url, $body);

        return [
            'status' => $response->status(),
            'body' => $response->json(),
            'success' => $response->successful()
        ];
    }
    
    /**
     * Request Production CSID.
     */
    public function requestProductionCsid($complianceRequestId, $zatcaSettings)
    {
        $environment = $zatcaSettings['environment'] ?? 'sandbox';
        $baseUrl = $this->getBaseUrl($environment);
        $url = $baseUrl . '/production/csids';
        
        $csid = $zatcaSettings['csid'];
        $secret = $zatcaSettings['secret'];
        $basicAuth = base64_encode("$csid:$secret");

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Basic $basicAuth",
            'Accept-Version' => 'V2',
            'Content-Type' => 'application/json',
            'Compliance-Request-ID' => $complianceRequestId
        ])->post($url, [
            'complianceRequestId' => $complianceRequestId
        ]);
        
        if ($response->failed()) {
            throw new \Exception('Failed to get Production CSID: ' . $response->body());
        }
        
        return $response->json();
    }

    /**
     * XML canonization (C14N).
     */
    private function canonizeXml($xml)
    {
        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = false;
        $dom->loadXML($xml);
        return $dom->C14N();
    }
}
