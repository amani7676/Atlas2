<?php

namespace App\Livewire;

use App\Jobs\SendWelcomeMessageJob;
use App\Models\ApiKey;
use App\Models\MessageTemplate;
use App\Models\MessageVariable;
use App\Models\Resident;
use App\Models\WelcomeMessage;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class MessageSender extends Component
{
    use WithPagination;

    // Welcome Message Form Properties
    public $body_id = '';
    public $send_date = '';
    public $is_active = true;

    // Send Message Form Properties
    public $showMessageModal = false;
    public $selectedApiKey = null;
    public $selectedTemplateId = null;
    public $selectedVariables = [];
    public $recipientNumber = '';
    public $messageContent = '';
    public $selectedRecipientField = null;
    
    // Preview Properties
    public $showPreview = false;
    public $previewResidents = [];
    public $totalResidentsCount = 0;
    
    public function render()
    {
        // Get SMS responses from cache
        $smsResponses = [];
        $cacheKeys = \Cache::get('sms_response_keys', []);
        
        foreach ($cacheKeys as $key) {
            $response = \Cache::get($key);
            if ($response) {
                $smsResponses[] = $response;
            }
        }
        
        // Get template debug info from cache
        $templateDebugs = [];
        $debugKeys = \Cache::get('template_debug_keys', []);
        
        foreach ($debugKeys as $key) {
            $debug = \Cache::get($key);
            if ($debug) {
                $templateDebugs[] = $debug;
            }
        }

        return view('livewire.message-sender', [
            'apiKeys' => ApiKey::where('is_active', true)->get(),
            'templates' => MessageTemplate::where('is_active', true)->where('body_status', 1)->get(),
            'variables' => MessageVariable::where('is_active', true)->get(),
            'welcomeMessages' => WelcomeMessage::where('is_active', true)->orderBy('send_date', 'desc')->paginate(10),
            'availableTemplates' => MessageTemplate::where('is_active', true)->where('body_status', 1)->get(),
            'smsResponses' => $smsResponses,
            'templateDebugs' => $templateDebugs
        ]);
    }

    // Welcome Message Methods
    public function saveWelcomeMessage()
    {
        $this->validate([
            'body_id' => 'required|string|max:20',
            'send_date' => 'required|date',
        ]);

        WelcomeMessage::create([
            'body_id' => $this->body_id,
            'send_date' => $this->send_date,
            'is_active' => $this->is_active,
        ]);

        $this->resetWelcomeForm();
        session()->flash('message', 'پیام خوشامدگویی با موفقیت ذخیره شد.');
    }

    public function editWelcomeMessage($id)
    {
        $welcomeMessage = WelcomeMessage::findOrFail($id);
        $this->body_id = $welcomeMessage->body_id;
        $this->send_date = $welcomeMessage->send_date->format('Y-m-d');
        $this->is_active = $welcomeMessage->is_active;
    }

    public function deleteWelcomeMessage($id)
    {
        WelcomeMessage::findOrFail($id)->delete();
        session()->flash('message', 'پیام خوشامدگویی با موفقیت حذف شد.');
    }

    public function toggleActive($id)
    {
        $welcomeMessage = WelcomeMessage::findOrFail($id);
        $welcomeMessage->update(['is_active' => !$welcomeMessage->is_active]);
        $status = $welcomeMessage->is_active ? 'فعال' : 'غیرفعال';
        session()->flash('message', "پیام به عنوان {$status} علامت‌گذاری شد.");
    }

    public function resetWelcomeForm()
    {
        $this->body_id = '';
        $this->send_date = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    // Send Message Methods
    public function openMessageModal()
    {
        $this->resetMessageForm();
        $this->showMessageModal = true;
    }

    public function closeMessageModal()
    {
        $this->showMessageModal = false;
    }

    public function selectApiKey($apiKeyId)
    {
        $this->selectedApiKey = $apiKeyId;
        $this->selectedTemplateId = null;
        $this->selectedVariables = [];
        $this->recipientNumber = '';
        $this->messageContent = '';
        $this->selectedRecipientField = null;
    }

    // Template Methods
    public function selectTemplate($templateId)
    {
        $this->selectedTemplateId = $templateId;
        
        // Load the template data
        $template = MessageTemplate::find($templateId);
        if ($template) {
            $this->messageContent = $template->body;
        }
    }

    public function syncTemplates()
    {
        if (!$this->selectedApiKey) {
            session()->flash('error', 'لطفاً ابتدا یک API key انتخاب کنید.');
            return;
        }

        $apiKey = ApiKey::find($this->selectedApiKey);
        $payamakService = new \App\Services\PayamakService();

        if ($payamakService->syncTemplates($apiKey)) {
            session()->flash('message', 'قالب‌ها با موفقیت همگام‌سازی شد.');
        } else {
            session()->flash('error', 'خطا در همگام‌سازی قالب‌ها. لطفاً اطلاعات API key را بررسی کنید.');
        }
    }

    public function toggleVariable($variableId)
    {
        if (in_array($variableId, $this->selectedVariables)) {
            $this->selectedVariables = array_diff($this->selectedVariables, [$variableId]);
        } else {
            $this->selectedVariables[] = $variableId;
        }
        $this->updateMessageContent();
    }

    public function selectRecipientField($fieldId)
    {
        $this->selectedRecipientField = $fieldId;
    }

    public function updateMessageContent()
    {
        if ($this->selectedTemplateId) {
            $template = MessageTemplate::find($this->selectedTemplateId);
            if ($template) {
                $content = $template->body;
                
                // Replace variables with actual values
                foreach ($this->selectedVariables as $variableId) {
                    $variable = MessageVariable::find($variableId);
                    if ($variable) {
                        // Try to get the actual value from the resident model
                        $value = $this->getVariableValue($variable->field_name);
                        $content = str_replace($variable->code, $value, $content);
                    }
                }
                
                $this->messageContent = $content;
            }
        }
    }

    private function getVariableValue($fieldName)
    {
        // Try to get value from resident model
        try {
            $resident = \App\Models\Resident::first(); // You might need to adjust this based on your actual model
            if ($resident && isset($resident->{$fieldName})) {
                return $resident->{$fieldName};
            }
        } catch (\Exception $e) {
            return '{' . $fieldName . '}';
        }
        
        return '{' . $fieldName . '}';
    }

    public function sendMessage()
    {
        $this->validate([
            'recipientNumber' => 'required|string|max:11',
            'messageContent' => 'required|string',
        ]);

        if (!$this->selectedApiKey) {
            session()->flash('error', 'لطفاً یک API key انتخاب کنید.');
            return;
        }

        if (!$this->selectedTemplateId) {
            session()->flash('error', 'لطفاً یک قالب پیام انتخاب کنید.');
            return;
        }

        // Here you would integrate with your SMS service
        // For now, we'll just show success message
        $apiKey = ApiKey::find($this->selectedApiKey);
        $template = MessageTemplate::find($this->selectedTemplateId);

        // Close modal and reset form
        $this->closeMessageModal();
        session()->flash('message', 'پیام با موفقیت ارسال شد به ' . $this->recipientNumber);
        
        // In a real implementation, you would call your SMS service here
        // $this->sendSms($this->recipientNumber, $this->messageContent, $apiKey);
    }

    private function resetMessageForm()
    {
        $this->selectedApiKey = null;
        $this->selectedTemplateId = null;
        $this->selectedVariables = [];
        $this->recipientNumber = '';
        $this->messageContent = '';
        $this->selectedRecipientField = null;
    }

    public function sendWelcomeMessages()
    {
        try {
            // Get all active welcome messages
            $welcomeMessages = WelcomeMessage::where('is_active', true)->get();
            
            if ($welcomeMessages->isEmpty()) {
                session()->flash('error', 'هیچ پیام خوشامدگویی فعالی یافت نشد.');
                return;
            }

            // Find residents who meet the criteria
            $contracts = \App\Models\Contract::with(['resident', 'bed.room'])
                ->where('state', 'active') // فقط قراردادهای فعال (state)
                ->where('welcome_sent', false) // فقط قراردادهایی که پیام خوشامدگویی نگرفتند
                ->whereHas('resident', function ($query) {
                    // فقط اقامتگران فعال (حذف نشده)
                    $query->whereNull('deleted_at');
                })
                ->get();

            // Filter residents based on welcome message dates
            $eligibleResidents = [];
            
            foreach ($contracts as $contract) {
                foreach ($welcomeMessages as $welcomeMessage) {
                    // Check if resident was created after welcome message date
                    if ($contract->resident->created_at->gte($welcomeMessage->send_date)) {
                        // Get variables and their values for this resident
                        $variables = MessageVariable::where('is_active', true)->orderBy('id')->get();
                        $variableValues = [];
                        
                        foreach ($variables as $variable) {
                            $value = $this->getVariableValueForResident($variable->field_name, $contract->resident);
                            $variableValues[] = [
                                'code' => $variable->code,
                                'field_name' => $variable->field_name,
                                'description' => $variable->description,
                                'value' => $value
                            ];
                        }
                        
                        $eligibleResidents[] = [
                            'contract_id' => $contract->id,
                            'resident_id' => $contract->resident->id,
                            'resident_name' => $contract->resident->full_name,
                            'resident_phone' => $contract->resident->phone,
                            'payment_date' => $contract->payment_date,
                            'room_name' => $contract->bed && $contract->bed->room ? $contract->bed->room->name : 'نامشخص',
                            'bed_name' => $contract->bed ? $contract->bed->name : 'نامشخص',
                            'welcome_message_id' => $welcomeMessage->id,
                            'welcome_message_code' => $welcomeMessage->body_id,
                            'welcome_send_date' => $welcomeMessage->send_date->format('Y/m/d'),
                            'resident_created_at' => $contract->resident->created_at->format('Y/m/d'),
                            'variables' => $variableValues,
                            'preview_text' => $this->generatePreviewText($welcomeMessage, $variableValues)
                        ];
                    }
                }
            }

            if (empty($eligibleResidents)) {
                session()->flash('message', 'هیچ اقامتگر واجد شرایطی برای ارسال پیام خوشامدگویی یافت نشد.');
                $this->showPreview = false;
                return;
            }

            // Store the eligible residents for preview
            $this->previewResidents = $eligibleResidents;
            $this->totalResidentsCount = count($eligibleResidents);
            $this->showPreview = true;

            session()->flash('message', "تعداد {$this->totalResidentsCount} اقامتگر واجد شرایط برای ارسال پیام خوشامدگویی یافت شد.");

        } catch (\Exception $e) {
            Log::error('Error in sendWelcomeMessages: ' . $e->getMessage());
            session()->flash('error', 'خطا در بررسی اقامتگران: ' . $e->getMessage());
        }
    }

    public function confirmSendMessages()
    {
        try {
            $sentCount = 0;
            $failedCount = 0;
            $results = [];
            
            foreach ($this->previewResidents as $resident) {
                $contract = \App\Models\Contract::find($resident['contract_id']);
                $welcomeMessage = WelcomeMessage::find($resident['welcome_message_id']);
                
                if ($contract && $welcomeMessage) {
                    try {
                        // Get API key for sending
                        $apiKey = ApiKey::where('is_active', true)->first();
                        
                        if (!$apiKey) {
                            throw new \Exception('هیچ API key فعالی یافت نشد.');
                        }

                        // Get message template and variables
                        $template = MessageTemplate::where('body_id', $welcomeMessage->body_id)
                            ->where('body_status', 1)
                            ->first();
                            
                        if (!$template) {
                            throw new \Exception("قالب پیام با کد {$welcomeMessage->body_id} یافت نشد یا غیرفعال است.");
                        }

                        // Get all active variables and build text array
                        $variables = MessageVariable::where('is_active', true)->orderBy('id')->get();
                        $textArray = [];
                        
                        Log::info('Processing variables for resident: ' . $contract->resident->full_name);
                        
                        foreach ($variables as $index => $variable) {
                            $value = $this->getVariableValueForResident($variable->field_name, $contract->resident);
                            $textArray[] = $value;
                            Log::info("Variable [{$index}]: {$variable->field_name} = {$value}");
                        }
                        
                        Log::info('Final text array: ' . json_encode($textArray));

                        // Send SMS using Payamak API
                        Log::info('Sending SMS to: ' . $contract->resident->phone . ' with bodyId: ' . $welcomeMessage->body_id);
                        Log::info('Text array: ' . json_encode($textArray));
                        $result = $this->sendSmsViaPayamak($apiKey, $textArray, $contract->resident->phone, $welcomeMessage->body_id, $contract);
                        
                        // Check if result is successful
                        if (is_numeric($result) && $result > 0) {
                            // Update contract welcome_sent to true
                            $contract->update(['welcome_sent' => true]);
                            Log::info("Contract {$contract->id} marked as welcome_sent = true");
                            
                            $sentCount++;
                            $results[] = [
                                'resident_name' => $contract->resident->full_name,
                                'resident_phone' => $contract->resident->phone,
                                'status' => true,
                                'rec_id' => $result,
                                'message' => 'پیام با موفقیت ارسال شد'
                            ];
                        } else {
                            $failedCount++;
                            $results[] = [
                                'resident_name' => $contract->resident->full_name,
                                'resident_phone' => $contract->resident->phone,
                                'status' => false,
                                'rec_id' => $result,
                                'message' => is_string($result) && strpos($result, 'ERROR:') === 0 
                                    ? substr($result, 7) 
                                    : $this->getErrorMessage($result)
                            ];
                        }

                    } catch (\Exception $e) {
                        $failedCount++;
                        $results[] = [
                            'resident_name' => $contract->resident->full_name,
                            'resident_phone' => $contract->resident->phone,
                            'status' => false,
                            'rec_id' => null,
                            'message' => $e->getMessage()
                        ];
                    }
                }
            }

            // Store results in session for display
            session()->flash('send_results', $results);
            
            // Close preview and reset
            $this->closePreview();
            
            session()->flash('message', "ارسال پیام‌ها تکمیل شد: {$sentCount} موفق، {$failedCount} ناموفق.");

        } catch (\Exception $e) {
            Log::error('Error in confirmSendMessages: ' . $e->getMessage());
            session()->flash('error', 'خطا در ارسال پیام‌ها: ' . $e->getMessage());
        }
    }

    public function sendSmsViaPayamak($apiKey, $textArray, $to, $bodyId, $contract = null)
    {
        try {
            Log::info('=== MELI PAYAMAK API CALL (cURL Method) ===');
            Log::info('Username: ' . $apiKey->username);
            Log::info('To: ' . $to);
            Log::info('Body ID: ' . $bodyId);
            Log::info('Text Array: ' . json_encode($textArray, JSON_UNESCAPED_UNICODE));
            
            // Create SOAP XML manually for SendByBaseNumber
            $soapXml = '<?xml version="1.0" encoding="UTF-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
  <soap:Body>
    <SendByBaseNumber xmlns="http://tempuri.org/">
      <username>' . htmlspecialchars($apiKey->username) . '</username>
      <password>' . htmlspecialchars($apiKey->api_key) . '</password>
      <text>';
            
            foreach ($textArray as $text) {
                $soapXml .= '<string>' . htmlspecialchars($text) . '</string>';
            }
            
            $soapXml .= '</text>
      <to>' . htmlspecialchars($to) . '</to>
      <bodyId>' . htmlspecialchars($bodyId) . '</bodyId>
    </SendByBaseNumber>
  </soap:Body>
</soap:Envelope>';
            
            Log::info('SOAP XML: ' . $soapXml);
            
            // Use cURL to send SOAP request (WSDL is broken, so we use non-WSDL)
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://api.payamak-panel.com/post/send.asmx");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $soapXml);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: "http://tempuri.org/SendByBaseNumber"'
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                Log::error('cURL Error: ' . $error);
                return 'ERROR: ' . $error;
            }
            
            Log::info('HTTP Status: ' . $httpCode);
            Log::info('Response: ' . $response);
            
            if ($httpCode != 200) {
                Log::error('HTTP Error: ' . $httpCode);
                return 'ERROR: HTTP ' . $httpCode;
            }
            
            // Parse XML response
            try {
                Log::info('Attempting to parse XML response...');
                
                // Remove any potential BOM or whitespace
                $cleanResponse = trim($response);
                $cleanResponse = preg_replace('/^\s+|\s+$|\s+(?=\s)/', '', $cleanResponse);
                
                Log::info('Cleaned response: ' . substr($cleanResponse, 0, 200) . '...');
                
                // Try multiple XML parsing methods
                $xml = null;
                
                // Method 1: SimpleXML
                try {
                    $xml = simplexml_load_string($cleanResponse);
                    if ($xml) {
                        Log::info('✅ SimpleXML parsing successful');
                    }
                } catch (\Exception $e) {
                    Log::warning('SimpleXML failed: ' . $e->getMessage());
                }
                
                // Method 2: DOMDocument if SimpleXML fails
                if (!$xml) {
                    try {
                        $dom = new \DOMDocument();
                        $dom->loadXML($cleanResponse);
                        $xml = simplexml_import_dom($dom);
                        if ($xml) {
                            Log::info('✅ DOMDocument parsing successful');
                        }
                    } catch (\Exception $e) {
                        Log::warning('DOMDocument failed: ' . $e->getMessage());
                    }
                }
                
                // Method 3: Manual regex parsing as last resort
                if (!$xml) {
                    Log::warning('XML parsing failed, trying regex extraction...');
                    
                    // Extract result using regex
                    if (preg_match('/<SendByBaseNumberResult>(.*?)<\/SendByBaseNumberResult>/', $cleanResponse, $matches)) {
                        $result = $matches[1];
                        Log::info('✅ Regex extraction successful: ' . $result);
                        
                        // Check response according to Meli Payamak documentation
                        if (is_numeric($result) && $result > 0 && strlen($result) >= 15) {
                            Log::info('✅ SMS sent successfully, RecId: ' . $result);
                            
                            // Update contract welcome_sent to true if contract is provided
                            if ($contract) {
                                $contract->update(['welcome_sent' => true]);
                                Log::info("Contract {$contract->id} marked as welcome_sent = true");
                            }
                            
                            return $result;
                        } else {
                            // Handle error codes according to documentation
                            $errorMessage = $this->getMeliPayamakErrorMessage($result);
                            Log::error('❌ SMS sending failed, Code: ' . $result . ', Message: ' . $errorMessage);
                            return 'ERROR: ' . $errorMessage . ' (کد: ' . $result . ')';
                        }
                    } else {
                        Log::error('❌ Regex extraction failed');
                        return 'ERROR: Could not extract result from response';
                    }
                }
                
                if (!$xml) {
                    Log::error('❌ All XML parsing methods failed');
                    return 'ERROR: Invalid XML response - all parsing methods failed';
                }
                
                // Extract result from parsed XML
                $result = null;
                try {
                    $result = $xml->children('soap', true)->Body->children()->SendByBaseNumberResponse->SendByBaseNumberResult;
                    Log::info('✅ XML result extraction successful: ' . $result);
                } catch (\Exception $e) {
                    Log::error('XML result extraction failed: ' . $e->getMessage());
                    
                    // Try alternative extraction
                    try {
                        $result = $xml->xpath('//SendByBaseNumberResult')[0];
                        Log::info('✅ XPath extraction successful: ' . $result);
                    } catch (\Exception $e2) {
                        Log::error('XPath extraction failed: ' . $e2->getMessage());
                        return 'ERROR: Could not extract result from XML';
                    }
                }
                
                // Check response according to Meli Payamak documentation
                if (is_numeric($result) && $result > 0 && strlen($result) >= 15) {
                    Log::info('✅ SMS sent successfully, RecId: ' . $result);
                    
                    // Update contract welcome_sent to true if contract is provided
                    if ($contract) {
                        $contract->update(['welcome_sent' => true]);
                        Log::info("Contract {$contract->id} marked as welcome_sent = true");
                    }
                    
                    return $result;
                } else {
                    // Handle error codes according to documentation
                    $errorMessage = $this->getMeliPayamakErrorMessage($result);
                    Log::error('❌ SMS sending failed, Code: ' . $result . ', Message: ' . $errorMessage);
                    return 'ERROR: ' . $errorMessage . ' (کد: ' . $result . ')';
                }
                
            } catch (\Exception $e) {
                Log::error('XML Parse Error: ' . $e->getMessage());
                return 'ERROR: XML Parse Error - ' . $e->getMessage();
            }
            
        } catch (\Exception $e) {
            Log::error('SMS sending error: ' . $e->getMessage());
            return 'ERROR: ' . $e->getMessage();
        }
    }
    
    /**
     * Get error message based on Meli Payamak error codes
     */
    private function getMeliPayamakErrorMessage($code)
    {
        $errorMessages = [
            '110' => 'الزام استفاده از ApiKey به جای رمز عبور',
            '109' => 'الزام تنظیم IP مجاز برای استفاده از API',
            '108' => 'مسدود شدن IP به دلیل تلاش ناموفق استفاده از API',
            '-10' => 'در میان متغیرهای ارسالی، لینک وجود دارد',
            '-7' => 'خطایی در شماره فرستنده رخ داده است با پشتیبانی تماس بگیرید',
            '-6' => 'خطای داخلی رخ داده است با پشتیبانی تماس بگیرید',
            '-5' => 'متن ارسالی باتوجه به متغیرهای مشخص شده در متن پیشفرض همخوانی ندارد',
            '-4' => 'کد متن ارسالی صحیح نمی‌باشد و یا توسط مدیر سامانه تأیید نشده است',
            '-3' => 'خط ارسالی در سیستم تعریف نشده است، با پشتیبانی سامانه تماس بگیرید',
            '-2' => 'محدودیت تعداد شماره، محدودیت هربار ارسال یک شماره موبایل می‌باشد',
            '-1' => 'دسترسی برای استفاده از این وبسرویس غیرفعال است. با پشتیبانی تماس بگیرید',
            '0' => 'نام کاربری یا رمزعبور صحیح نمی‌باشد',
            '2' => 'اعتبار کافی نمی‌باشد',
            '6' => 'سامانه در حال بروزرسانی می‌باشد',
            '7' => 'متن حاوی کلمه فیلتر شده می‌باشد، با واحد اداری تماس بگیرید',
            '10' => 'کاربر مورد نظر فعال نمی‌باشد',
            '11' => 'ارسال نشده',
            '12' => 'مدارک کاربر کامل نمی‌باشد',
            '16' => 'شماره گیرنده ای یافت نشد',
            '17' => 'متن پیامک خالی می باشد',
            '18' => 'شماره گیرنده نامعتبر است',
            '19' => 'از محدودیت ساعتی فراتر رفته اید'
        ];
        
        return isset($errorMessages[$code]) ? $errorMessages[$code] : 'خطای ناشناخته';
    }

    private function generatePreviewText($welcomeMessage, $variableValues)
    {
        try {
            // Get the template with body_status = 1
            $template = MessageTemplate::where('body_id', $welcomeMessage->body_id)
                ->where('body_status', 1)
                ->first();
                
            if (!$template) {
                return "قالب پیام یافت نشد یا غیرفعال است (کد: {$welcomeMessage->body_id})";
            }
            
            // Start with template body
            $text = $template->body;
            
            // Replace variables with actual values
            foreach ($variableValues as $variable) {
                $text = str_replace($variable['code'], $variable['value'], $text);
            }
            
            return $text;
            
        } catch (\Exception $e) {
            return "خطا در تولید پیش‌نمایش: " . $e->getMessage();
        }
    }

    private function getVariableValueForResident($fieldName, $resident)
    {
        try {
            // Handle special field names that require relationships
            switch ($fieldName) {
                case 'residents.full_name':
                    return $resident->full_name ?? 'نامشخص';
                    
                case 'contracts.payment_date':
                    // Get the contract for this resident
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->first();
                    if ($contract && $contract->payment_date) {
                        try {
                            // Convert to Jalali (Shamsi) date
                            return \Morilog\Jalali\Jalalian::fromDateTime($contract->payment_date)->format('Y/m/d');
                        } catch (\Exception $e) {
                            Log::warning('Date conversion error: ' . $e->getMessage());
                            return $contract->payment_date;
                        }
                    }
                    return 'نامشخص';
                    
                case 'contracts.start_date':
                    // Get the contract for this resident
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->first();
                    if ($contract && $contract->start_date) {
                        try {
                            // Convert to Jalali (Shamsi) date
                            return \Morilog\Jalali\Jalalian::fromDateTime($contract->start_date)->format('Y/m/d');
                        } catch (\Exception $e) {
                            Log::warning('Date conversion error: ' . $e->getMessage());
                            return $contract->start_date;
                        }
                    }
                    return 'نامشخص';
                    
                case 'contracts.end_date':
                    // Get the contract for this resident
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->first();
                    if ($contract && $contract->end_date) {
                        try {
                            // Convert to Jalali (Shamsi) date
                            return \Morilog\Jalali\Jalalian::fromDateTime($contract->end_date)->format('Y/m/d');
                        } catch (\Exception $e) {
                            Log::warning('Date conversion error: ' . $e->getMessage());
                            return $contract->end_date;
                        }
                    }
                    return 'نامشخص';
                    
                case 'residents.birth_date':
                    // Get birth date from resident
                    if ($resident && $resident->birth_date) {
                        try {
                            // Convert to Jalali (Shamsi) date
                            return \Morilog\Jalali\Jalalian::fromDateTime($resident->birth_date)->format('Y/m/d');
                        } catch (\Exception $e) {
                            Log::warning('Date conversion error: ' . $e->getMessage());
                            return $resident->birth_date;
                        }
                    }
                    return 'نامشخص';
                    
                case 'rooms.name':
                    // Get room name through contract and bed relationships
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->with(['bed.room'])
                        ->first();
                    return $contract && $contract->bed && $contract->bed->room 
                        ? $contract->bed->room->name 
                        : 'نامشخص';
                        
                case 'beds.name':
                    // Get bed name through contract relationship
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->with('bed')
                        ->first();
                    return $contract && $contract->bed 
                        ? $contract->bed->name 
                        : 'نامشخص';
                    
                default:
                    // Try to get direct field from resident model
                    if ($resident && isset($resident->{$fieldName})) {
                        return $resident->{$fieldName};
                    }
            }
        } catch (\Exception $e) {
            Log::warning('Variable not found: ' . $fieldName . ' Error: ' . $e->getMessage());
        }
        
        return '{' . $fieldName . '}';
    }

    private function getErrorMessage($code)
    {
        $errorMessages = [
            '-1' => 'دسترسی برای استفاده از این وبسرویس غیرفعال است',
            '0' => 'نام کاربری یا رمزعبور صحیح نمی‌باشد',
            '2' => 'اعتبار کافی نمی‌باشد',
            '6' => 'سامانه درحال بروزرسانی می‌باشد',
            '7' => 'متن حاوی کلمه فیلتر شده می‌باشد',
            '10' => 'کاربر موردنظر فعال نمی‌باشد',
            '11' => 'ارسال نشده',
            '16' => 'شماره گیرنده ای یافت نشد',
            '17' => 'متن پیامک خالی می باشد',
            '18' => 'شماره گیرنده نامعتبر است',
            '19' => 'از محدودیت ساعتی فراتر رفته اید',
            '-10' => 'در میان متغییر های ارسالی ، لینک وجود دارد',
            '-7' => 'خطایی در شماره فرستنده رخ داده است',
            '-6' => 'خطای داخلی رخ داده است',
            '-5' => 'متن ارسالی با متغیرهای مشخص شده همخوانی ندارد',
            '-4' => 'کد متن ارسالی صحیح نمی‌باشد',
            '-3' => 'خط ارسالی در سیستم تعریف نشده است',
            '-2' => 'محدودیت تعداد شماره، تنها یک شماره موبایل می‌توانید وارد کنید',
            '109' => 'الزام تنظیم IP مجاز برای استفاده از API',
            '108' => 'مسدود شدن IP به دلیل تلاش ناموفق استفاده ازAPI',
            '110' => 'الزام استفاده از ApiKey به جای رمز عبور'
        ];
        
        return $errorMessages[$code] ?? "خطای ناشناخته: کد {$code}";
    }

    public function closePreview()
    {
        $this->showPreview = false;
        $this->previewResidents = [];
        $this->totalResidentsCount = 0;
    }

    /**
     * Test sync execution of welcome message
     */
    public function testSyncWelcomeMessage()
    {
        try {
            // Get first contract for testing
            $contract = \App\Models\Contract::with(['resident', 'bed.room'])
                ->where('welcome_sent', false)
                ->whereHas('resident', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->first();

            if (!$contract) {
                session()->flash('error', 'هیچ قراردادی برای تست پیدا نشد.');
                return;
            }

            // Get first active welcome message
            $welcomeMessage = WelcomeMessage::where('is_active', true)->first();
            if (!$welcomeMessage) {
                session()->flash('error', 'هیچ پیام خوشامدگویی فعالی برای تست پیدا نشد.');
                return;
            }

            Log::info('Testing sync execution for contract: ' . $contract->id);
            
            // Execute job synchronously
            $job = new SendWelcomeMessageJob($contract, $welcomeMessage);
            $job->handle();

            session()->flash('message', 'تست همزمان با موفقیت انجام شد. لاگ‌ها را در فایل لاگ بررسی کنید.');

        } catch (\Exception $e) {
            Log::error('Error in testSyncWelcomeMessage: ' . $e->getMessage());
            session()->flash('error', 'خطا در تست همزمان: ' . $e->getMessage());
        }
    }

    public function testData()
    {
        try {
            $welcomeMessages = WelcomeMessage::where('is_active', true)->get();
            $contracts = \App\Models\Contract::with(['resident', 'bed.room'])
                ->where('welcome_sent', false)
                ->whereHas('resident', function ($query) {
                    $query->whereNull('deleted_at');
                })
                ->get();

            $message = "تست داده‌ها:\n";
            $message .= "پیام‌های خوشامدگویی فعال: {$welcomeMessages->count()}\n";
            $message .= "قراردادهای بدون پیام خوشامدگویی: {$contracts->count()}\n\n";

            foreach ($welcomeMessages as $wm) {
                $message .= "پیام: {$wm->body_id}, تاریخ: {$wm->send_date}\n";
            }

            $message .= "\nقراردادها:\n";
            foreach ($contracts as $contract) {
                $message .= "قرارداد {$contract->id}: {$contract->resident->full_name}, ایجاد: {$contract->resident->created_at}\n";
            }

            session()->flash('message', $message);
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در تست داده‌ها: ' . $e->getMessage());
        }
    }
}
