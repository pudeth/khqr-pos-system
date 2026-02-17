<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class KHQRService
{
    protected string $apiUrl;
    protected string $token;
    protected array $merchant;

    public function __construct()
    {
        $this->apiUrl = config('services.bakong.api_url') ?? 'https://api-bakong.nbc.gov.kh';
        $this->token = config('services.bakong.token') ?? '';
        $this->merchant = config('services.bakong.merchant') ?? [];
    }

    /**
     * Generate Individual KHQR String locally
     */
    public function generateIndividualQR(array $data): array
    {
        try {
            $qrString = $this->buildKHQRString($data);
            $md5 = md5($qrString);

            return [
                'data' => [
                    'qr' => $qrString,
                    'md5' => $md5,
                ],
                'status' => ['code' => 0]
            ];
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Build KHQR String according to EMV QR Code specification
     */
    protected function buildKHQRString(array $data): string
    {
        $bakongId = $data['bakong_account_id'] ?? $this->merchant['bakong_id'] ?? '';
        $amount = $data['amount'] ?? 0;
        $currency = $data['currency'] ?? 'USD';
        $merchantName = $data['merchant_name'] ?? $this->merchant['name'] ?? 'Merchant';
        $merchantCity = $data['merchant_city'] ?? $this->merchant['city'] ?? 'PHNOM PENH';
        $billNumber = $data['bill_number'] ?? '';
        $mobileNumber = $data['mobile_number'] ?? '';
        $storeLabel = $data['store_label'] ?? '';
        $terminalLabel = $data['terminal_label'] ?? '';

        // Build QR data
        $qr = '';
        
        // Payload Format Indicator (ID 00)
        $qr .= $this->tlv('00', '01');
        
        // Point of Initiation Method (ID 01) - 12 = Dynamic QR
        $qr .= $this->tlv('01', '12');
        
        // Merchant Account Information (ID 29 for Bakong)
        $merchantAccount = $this->tlv('00', $bakongId);
        if (!empty($mobileNumber)) {
            $merchantAccount .= $this->tlv('01', $mobileNumber);
        }
        $qr .= $this->tlv('29', $merchantAccount);
        
        // Merchant Category Code (ID 52)
        $qr .= $this->tlv('52', '5999');
        
        // Transaction Currency (ID 53) - 840 = USD, 116 = KHR
        $currencyCode = $currency === 'KHR' ? '116' : '840';
        $qr .= $this->tlv('53', $currencyCode);
        
        // Transaction Amount (ID 54)
        if ($amount > 0) {
            $qr .= $this->tlv('54', number_format($amount, 2, '.', ''));
        }
        
        // Country Code (ID 58)
        $qr .= $this->tlv('58', 'KH');
        
        // Merchant Name (ID 59)
        $qr .= $this->tlv('59', substr($merchantName, 0, 25));
        
        // Merchant City (ID 60)
        $qr .= $this->tlv('60', substr($merchantCity, 0, 15));
        
        // Additional Data Field (ID 62)
        $additionalData = '';
        if (!empty($billNumber)) {
            $additionalData .= $this->tlv('01', $billNumber);
        }
        if (!empty($mobileNumber)) {
            $additionalData .= $this->tlv('02', $mobileNumber);
        }
        if (!empty($storeLabel)) {
            $additionalData .= $this->tlv('03', $storeLabel);
        }
        if (!empty($terminalLabel)) {
            $additionalData .= $this->tlv('07', $terminalLabel);
        }
        if (!empty($additionalData)) {
            $qr .= $this->tlv('62', $additionalData);
        }
        
        // Timestamp (ID 99)
        $timestamp = $this->tlv('00', (string)round(microtime(true) * 1000));
        $qr .= $this->tlv('99', $timestamp);
        
        // CRC (ID 63) - placeholder, will be calculated
        $qr .= '6304';
        
        // Calculate CRC16
        $crc = $this->crc16($qr);
        $qr = substr($qr, 0, -4) . '6304' . strtoupper(str_pad(dechex($crc), 4, '0', STR_PAD_LEFT));
        
        return $qr;
    }

    /**
     * Create TLV (Tag-Length-Value) string
     */
    protected function tlv(string $tag, string $value): string
    {
        $length = str_pad(strlen($value), 2, '0', STR_PAD_LEFT);
        return $tag . $length . $value;
    }

    /**
     * Calculate CRC16 CCITT-FALSE
     */
    protected function crc16(string $data): int
    {
        $crc = 0xFFFF;
        $polynomial = 0x1021;

        for ($i = 0; $i < strlen($data); $i++) {
            $crc ^= (ord($data[$i]) << 8);
            for ($j = 0; $j < 8; $j++) {
                if ($crc & 0x8000) {
                    $crc = (($crc << 1) ^ $polynomial) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return $crc;
    }

    /**
     * Check Payment Status via Bakong API
     */
    public function checkPayment(string $md5): array
    {
        try {
            $response = Http::withOptions([
                'verify' => false, // Disable SSL verification for development
                'timeout' => 30,
            ])->withHeaders([
                'Authorization' => 'Bearer ' . $this->token,
                'Content-Type' => 'application/json',
            ])->post($this->apiUrl . '/v1/check_transaction_by_md5', [
                'md5' => $md5,
            ]);

            if ($response->successful()) {
                return $response->json() ?? ['error' => 'No response'];
            } else {
                return [
                    'error' => 'HTTP ' . $response->status() . ': ' . $response->body(),
                    'status_code' => $response->status()
                ];
            }
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function generateMerchantQR(array $data): array
    {
        return $this->generateIndividualQR($data);
    }

    /**
     * Verify QR code format
     */
    public function verifyQR(string $qrCode): array
    {
        try {
            // Basic validation
            if (empty($qrCode)) {
                return ['success' => false, 'message' => 'QR code is empty'];
            }

            // Check if it starts with proper EMV format
            if (!str_starts_with($qrCode, '00')) {
                return ['success' => false, 'message' => 'Invalid QR format'];
            }

            // Check CRC
            if (strlen($qrCode) < 8) {
                return ['success' => false, 'message' => 'QR code too short'];
            }

            $crcFromQR = substr($qrCode, -4);
            $qrWithoutCRC = substr($qrCode, 0, -4) . '6304';
            $calculatedCRC = strtoupper(str_pad(dechex($this->crc16($qrWithoutCRC)), 4, '0', STR_PAD_LEFT));

            if ($crcFromQR !== $calculatedCRC) {
                return ['success' => false, 'message' => 'Invalid CRC checksum'];
            }

            return ['success' => true, 'message' => 'QR code is valid'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Decode QR code data
     */
    public function decodeQR(string $qrCode): array
    {
        try {
            $data = [];
            $pos = 0;
            $length = strlen($qrCode);

            while ($pos < $length - 4) { // -4 for CRC
                if ($pos + 4 > $length) break;

                $tag = substr($qrCode, $pos, 2);
                $len = (int)substr($qrCode, $pos + 2, 2);
                $value = substr($qrCode, $pos + 4, $len);

                $data[$tag] = $value;
                $pos += 4 + $len;
            }

            return ['success' => true, 'data' => $data];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate deep link for mobile apps
     */
    public function generateDeepLink(string $qrCode): array
    {
        try {
            $encodedQR = urlencode($qrCode);
            
            return [
                'success' => true,
                'deep_links' => [
                    'bakong' => "bakong://qr?data={$encodedQR}",
                    'aba' => "aba://qr?data={$encodedQR}",
                    'acleda' => "acleda://qr?data={$encodedQR}",
                    'generic' => "khqr://pay?data={$encodedQR}",
                ]
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}