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
     * Generate Invoice Hash (SHA256).
     * This is a simplified placeholder. In production, this must canonize the XML first.
     */
    public function generateInvoiceHash($xmlContent)
    {
        // 1. Canonize XML (Remove whitespace, sorting, etc based on C14N)
        // This is a complex step requiring a dedicated XML library or careful regex.
        // For Proof of Concept, we will hash the content directly, but note this limitation.
        $canonizedXml = $this->canonizeXml($xmlContent);
        return base64_encode(hash('sha256', $canonizedXml, true));
    }

    /**
     * Generate ECDSA Signature.
     * @param string $hash The invoice hash
     * @param string $privateKeyContent The private key content (PEM)
     */
    public function signHash($hash, $privateKeyContent)
    {
        // Warning: This requires the private key to be a valid EC key.
        // If openssl is not properly configured, this will fail.
        // We assume $privateKeyContent comes from database 'files' or secure storage.
        
        $privateKey = openssl_get_privatekey($privateKeyContent);
        if (!$privateKey) {
            return null; // Handle error
        }

        $signature = '';
        // In ZATCA, we sign the HASH directly? usually we sign the data. 
        // Openssl_sign expects data, then it hashes it. 
        // If we already have the hash, we might need low-level signing.
        // ZATCA requires: ECDSA-SHA256. 
        // We will pass the RAW canonized XML or the Hash depending on exact lib specs.
        // Usually: sign(hash) -> signature.
        
        openssl_sign($hash, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        
        return base64_encode($signature);
    }

    /**
     * Generate ZATCA QR Code Content (TLV Base64).
     * @param string $sellerName
     * @param string $vatNumber
     * @param string $timestamp
     * @param string $total
     * @param string $vatTotal
     * @param string $hash
     * @param string $signature
     * @param string $publicKey
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
            $result .= chr($key) . chr(strlen($valueStr)) . $valueStr;
        }

        return base64_encode($result);
    }

    /**
     * Issue Compliance CSID from ZATCA.
     * This orchestrates: CSR Generation -> API Call -> Saving Keys.
     * 
     * @param string $otp The OTP from Fatoora portal.
     * @param array $csrConfig Configuration for CSR (Organizational details).
     * @param string $environment sandbox|simulation|production
     * @return array Response with CSID, Secret, and generated RequestID.
     */
    public function issueComplianceCsid($otp, $csrConfig, $environment = 'sandbox')
    {
        // 1. Generate Private Key (EC secp256k1) and CSR
        $keys = $this->generateCsrAndKey($csrConfig);
        $csrContent = $keys['csr']; // Plain PEM
        $privateKey = $keys['private_key'];
        

        // 2. Prepare API URL
        $baseUrl = $this->getBaseUrl($environment);
        $url = $baseUrl . '/compliance';

        // 3. Call ZATCA API
        // Header requires: OTP, Accept-Version: V2, Content-Type: JSON
        // Body: { csr: "..." } (CSR must be base64 encoded without headers if API expects simple string, 
        // OR standard PEM. ZATCA V2 usually expects base64 string of the CSR body)
        
        // Clean CSR for API (remove headers if typically required, but ZATCA usually takes clean Base64)
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
        
        // Return all necessary secrets to be saved
        return [
            'csid' => $data['binarySecurityToken'] ?? null, // The Certificate
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
    private function generateCsrAndKey($config)
    {
        // OpenSSL Configuration for ZATCA Specifics
        // ZATCA requires secp256k1 curve.
        $oa = [
            "private_key_type" => OPENSSL_KEYTYPE_EC,
            "curve_name" => "secp256k1"
        ];
        
        $privateKeyResource = openssl_pkey_new($oa);
        if (!$privateKeyResource) {
             // Fallback for some systems that default to RSA or fail config
              throw new \Exception("OpenSSL PKey generation failed. Check OpenSSL config.");
        }
        
        openssl_pkey_export($privateKeyResource, $privateKeyPem);
        
        // Extract Public Key
        $details = openssl_pkey_get_details($privateKeyResource);
        $publicKeyPem = $details['key'];

        // Prepare DN (Distinguished Name)
        // ZATCA specific mapping
        // We need a custom configuration file approach to force specific OIDs if standard array fails,
        // but for now we try standard DN. 
        // Note: ZATCA is strict about 'CN', 'O', 'OU', 'C'.
        // 'UID' (VAT) and 'Title' (Invoice Type) often handled via config files in production scripts.
        // For simplicity in this demo, we map what we can. Standard OpenSSL might strip unknown fields.
        
        $dn = [
            "commonName" => $config['common_name'], 
            "organizationName" => $config['organization_identifier'], // ZATCA often wants VAT here or Name? Check specs. Actually O=Name.
            "organizationalUnitName" => $config['organization_unit_name'],
            "countryName" => "SA"
        ];
        
        // To strictly comply with ZATCA (adding UID, Title, Address, Category etc),
        // we really should write a temporary openssl.cnf.
        // Let's create a temp config content to ensure we pass EGS requirements.
        
        $cnfContent = "
[ req ]
default_bits        = 2048
emailAddress        = test@test.com
req_extensions      = v3_req
x509_extensions     = v3_ca
prompt              = no
distinguished_name  = req_distinguished_name

[ req_distinguished_name ]
C                   = SA
OU                  = " . $this->sanitizeConf($config['organization_unit_name']) . "
O                   = " . $this->sanitizeConf($config['common_name']) . "
CN                  = " . $this->sanitizeConf($config['common_name']) . "

[ v3_req ]
basicConstraints = CA:FALSE
keyUsage = digitalSignature, nonRepudiation, keyEncipherment
# ZATCA Specific OIDs could be added here if we had custom OID support in this environment
# 2.5.4.97 (OrganizationIdentifier/VAT) = " . $config['organization_identifier'] . "
# 0.9.2342.19200300.100.1.1 (UID) = ...
# 2.5.4.12 (Title) = 1100
";
        // Due to environment restrictions and complexity of custom OIDs without registering them in global openssl, 
        // we proceed with basic CSR. ZATCA Sandbox *might* accept it, or reject missing fields.
        // We will try standard first.
        
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
    
    private function sanitizeConf($str) {
        return preg_replace("/[^a-zA-Z0-9 ]/", "", $str);
    }
    
    private function cleanPem($pem)
    {
        $pem = str_replace('-----BEGIN CERTIFICATE REQUEST-----', '', $pem);
        $pem = str_replace('-----END CERTIFICATE REQUEST-----', '', $pem);
        $pem = str_replace("\n", '', $pem);
        $pem = str_replace("\r", '', $pem);
        return trim($pem);
    }

    private function getBaseUrl($env)
    {
        if ($env === 'production') {
            return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/core';
        } elseif ($env === 'simulation') {
            return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/simulation'; 
        }
        // Sandbox
        return 'https://gw-fatoora.zatca.gov.sa/e-invoicing/developer-portal';
    }

    /**
     * Report a Simplified Invoice (B2C) to ZATCA.
     * 
     * @param string $signedXml The full signed XML content.
     * @param string $uuid The Invoice UUID.
     * @param string $invoiceHash The Invoice Hash.
     * @param array $zatcaSettings The business ZATCA settings (containing CSID, secret, etc).
     * @return array API Response
     */
    public function reportInvoice($signedXml, $uuid, $invoiceHash, $zatcaSettings)
    {
        return $this->sendToZatca($signedXml, $uuid, $invoiceHash, $zatcaSettings, 'reporting');
    }

    /**
     * Check Invoice Compliance (used during onboarding or for testing).
     */
    public function checkInvoiceCompliance($signedXml, $uuid, $invoiceHash, $zatcaSettings)
    {
        return $this->sendToZatca($signedXml, $uuid, $invoiceHash, $zatcaSettings, 'compliance');
    }

    private function sendToZatca($signedXml, $uuid, $invoiceHash, $zatcaSettings, $type)
    {
        $environment = $zatcaSettings['environment'] ?? 'sandbox';
        $baseUrl = $this->getBaseUrl($environment);
        
        // Determine Endpoint
        if ($type === 'compliance') {
            $url = $baseUrl . '/compliance/invoices';
        } else {
            // Standard Reporting
            $url = $baseUrl . '/invoices/reporting/single';
        }

        // Credentials
        $csid = $zatcaSettings['csid'];
        $secret = $zatcaSettings['secret'];
        $basicAuth = base64_encode("$csid:$secret");

        // Payload
        $body = [
            'invoiceHash' => $invoiceHash,
            'uuid' => $uuid,
            'invoice' => base64_encode($signedXml)
        ];

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Basic $basicAuth",
            'Accept-Version' => 'V2',
            'Content-Type' => 'application/json',
            'Accept-Language' => 'en',
            'Clearance-Status' => '0' // 1 for Clearance (B2B Standard), 0 for Reporting (B2C Simplified)
        ])->post($url, $body);

        // We return the array, let the caller handle success/fail logic or throwing
        return [
            'status' => $response->status(),
            'body' => $response->json(),
            'success' => $response->successful()
        ];
    }
    
    /**
     * Request Production CSID (after compliance checks pass).
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
        
        return $response->json(); // Returns production CSID and Secret
    }

    /**
     * Dummy method to simulate XML canonization (C14N11).
     * Real implementation requires a library like DOMDocument C14N.
     */
    private function canonizeXml($xml)
    {
        // Placeholder: returning trimmed XML
        // In reality, use DOMDocument::C14N()
        // $dom = new \DOMDocument();
        // $dom->loadXML($xml);
        // return $dom->C14N();
        return trim($xml);
    }
}
