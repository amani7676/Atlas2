<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\MessageTemplate;
use Illuminate\Support\Facades\Log;

class PayamakService
{
    private $wsdlUrl = 'https://api.payamak-panel.com/post/SharedService.asmx?wsdl';

    public function getSharedServiceBody($username, $password)
    {
        try {
            // Disable WSDL cache for development
            ini_set("soap.wsdl_cache_enabled", "0");
            
            // Create SOAP client
            $client = new \SoapClient($this->wsdlUrl, [
                "encoding" => "UTF-8",
                "trace" => 1,
                "exceptions" => 1
            ]);

            // Prepare parameters
            $params = [
                "username" => $username,
                "password" => $password
            ];

            // Call the method
            $result = $client->GetSharedServiceBody($params);
            
            // Extract the actual data
            $templates = $result->GetSharedServiceBodyResult;
            
            // Debug: Log the raw response
            Log::info('Payamak API Raw Response', [
                'type' => gettype($templates),
                'value' => is_string($templates) ? substr($templates, 0, 500) : print_r($templates, true)
            ]);
            
            // Handle different response types
            if (is_string($templates)) {
                // Parse the JSON response
                $data = json_decode($templates, true);
                Log::info('Payamak API: Parsed JSON', ['data' => $data]);
            } elseif (is_object($templates)) {
                // Convert object to array
                $data = json_decode(json_encode($templates), true);
                Log::info('Payamak API: Converted object to array', ['data' => $data]);
            } else {
                // Direct array
                $data = $templates;
                Log::info('Payamak API: Direct array', ['data' => $data]);
            }
            
            if (isset($data['ShareServiceBody'])) {
                return $data['ShareServiceBody'];
            }
            
            // Try direct access if structure is different
            if (is_array($templates)) {
                Log::info('Payamak API: Returning direct array', ['count' => count($templates)]);
                return $templates;
            }

            Log::error('Payamak API: Invalid response structure', [
                'response' => $templates
            ]);

            return null;
            
        } catch (\SoapFault $e) {
            Log::error('Payamak SOAP Fault', [
                'message' => $e->getMessage(),
                'faultcode' => $e->faultcode,
                'faultstring' => $e->faultstring
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('Payamak Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    public function syncTemplates($apiKey)
    {
        $templates = $this->getSharedServiceBody($apiKey->username, $apiKey->api_key);

        if (!$templates) {
            Log::error('No templates received from Payamak API');
            return false;
        }

        $syncedCount = 0;
        
        foreach ($templates as $template) {
            try {
                // Validate required fields
                if (!isset($template['BodyID']) || !isset($template['Title']) || !isset($template['Body'])) {
                    Log::warning('Invalid template data', ['template' => $template]);
                    continue;
                }

                MessageTemplate::updateOrCreate(
                    ['body_id' => $template['BodyID']],
                    [
                        'title' => $template['Title'],
                        'body' => $template['Body'],
                        'insert_date' => $this->parseJalaliDate($template['InsertDate'] ?? null),
                        'body_status' => $template['BodyStatus'] ?? 1,
                        'description' => $template['Description'] ?? null,
                        'is_active' => true,
                    ]
                );
                
                $syncedCount++;
                
            } catch (\Exception $e) {
                Log::error('Error syncing template', [
                    'template' => $template,
                    'error' => $e->getMessage()
                ]);
            }
        }

        Log::info("Payamak templates synced successfully", [
            'total_received' => count($templates),
            'synced_count' => $syncedCount,
            'username' => $apiKey->username
        ]);

        return true;
    }

    private function parseJalaliDate($jalaliDate)
    {
        if (empty($jalaliDate)) {
            return now();
        }

        try {
            // Parse Jalali date format: "۱۴۰۴/۱۰/۱۷ ۲۳:۰۱"
            $parts = explode(' ', trim($jalaliDate));
            
            if (count($parts) < 2) {
                return now();
            }

            $dateParts = explode('/', $parts[0]);
            $timeParts = explode(':', $parts[1]);

            if (count($dateParts) < 3 || count($timeParts) < 2) {
                return now();
            }

            // Convert Persian numbers to English
            $year = $this->convertPersianNumbers($dateParts[0]);
            $month = $this->convertPersianNumbers($dateParts[1]);
            $day = $this->convertPersianNumbers($dateParts[2]);
            $hour = $this->convertPersianNumbers($timeParts[0]);
            $minute = $this->convertPersianNumbers($timeParts[1]);

            // Create a simple Gregorian date (you might want to use a proper Jalali library)
            // For now, we'll use a rough approximation
            $gregorianYear = $year - 621; // Rough conversion
            $gregorianMonth = $month;
            $gregorianDay = $day;

            return \Carbon\Carbon::create($gregorianYear, $gregorianMonth, $gregorianDay, $hour, $minute, 0);
            
        } catch (\Exception $e) {
            Log::error('Error parsing Jalali date', [
                'jalali_date' => $jalaliDate,
                'error' => $e->getMessage()
            ]);
            return now();
        }
    }

    private function convertPersianNumbers($string)
    {
        $persianNumbers = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $englishNumbers = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
        
        return str_replace($persianNumbers, $englishNumbers, $string);
    }
}
