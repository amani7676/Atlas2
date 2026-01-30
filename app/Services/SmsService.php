<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\ApiKey;

class SmsService
{
    private $apiUrl = 'https://rest.payamak-panel.com/api/SendSMS/GetCredit';

    /**
     * Get SMS credentials from database
     */
    private function getCredentials()
    {
        return Cache::remember('sms_credentials', 3600, function () {
            $apiKey = ApiKey::where('is_active', true)->first();
            
            if (!$apiKey) {
                throw new \Exception('No active SMS credentials found in database');
            }
            
            return [
                'username' => $apiKey->username,
                'password' => $apiKey->api_key,
            ];
        });
    }

    /**
     * Get SMS credit from Payamak API
     */
    public function getCredit()
    {
        try {
            $credentials = $this->getCredentials();
            
            $response = Http::asForm()->post($this->apiUrl, [
                'username' => $credentials['username'],
                'password' => $credentials['password'],
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['Value'])) {
                    // Handle decimal values - round to nearest whole number
                    return (int) round((float) $data['Value']);
                }
                return null;
            }
        } catch (\Exception $e) {
            \Log::error('SMS Credit API Error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get credit with color information
     */
    public function getCreditWithColor()
    {
        $credit = $this->getCredit();
        
        if ($credit === null) {
            return [
                'value' => null,
                'color' => 'secondary',
                'text' => 'نامشخص'
            ];
        }

        if ($credit < 50) {
            return [
                'value' => $credit,
                'color' => 'danger',
                'text' => $credit
            ];
        } elseif ($credit <= 100) {
            return [
                'value' => $credit,
                'color' => 'warning',
                'text' => $credit
            ];
        } else {
            return [
                'value' => $credit,
                'color' => 'success',
                'text' => $credit
            ];
        }
    }

    /**
     * Clear credentials cache (useful when credentials are updated)
     */
    public function clearCredentialsCache()
    {
        Cache::forget('sms_credentials');
    }
}
