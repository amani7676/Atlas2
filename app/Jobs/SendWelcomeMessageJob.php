<?php

namespace App\Jobs;

use App\Models\Contract;
use App\Models\Resident;
use App\Models\WelcomeMessage;
use App\Models\MessageTemplate;
use App\Models\MessageVariable;
use App\Models\ApiKey;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use SoapClient;

class SendWelcomeMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [60, 300, 900]; // 1 minute, 5 minutes, 15 minutes

    /**
     * Create a new job instance.
     */
    public function __construct(
        private Contract $contract,
        private WelcomeMessage $welcomeMessage
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Get the resident
            $resident = $this->contract->resident;
            if (!$resident) {
                return;
            }

            // Get the welcome message template
            $template = $this->welcomeMessage->template;
            if (!$template) {
                return;
            }

            // Get active API key
            $apiKey = ApiKey::where('is_active', true)->first();
            if (!$apiKey) {
                return;
            }

            // Get variables for the template
            $variables = $this->getTemplateVariables($template->id);
            
            // Prepare text array with actual values
            $textArray = $this->prepareTextArray($variables, $resident, $this->contract);
            
            // Store template variables debug info in cache
            $debugKey = 'template_debug_' . time() . '_' . $this->contract->id;
            $debugData = [];
            
            foreach ($variables as $variable) {
                $value = $this->getVariableValue($variable['field_name'], $resident, $this->contract);
                $debugData[] = [
                    'variable_code' => $variable['code'] ?? 'N/A',
                    'field_name' => $variable['field_name'],
                    'value' => $value,
                    'description' => $variable['description'] ?? 'N/A'
                ];
            }
            
            \Cache::put($debugKey, [
                'resident_name' => $resident->full_name,
                'template_body_id' => $template->body_id,
                'variables' => $debugData,
                'text_array' => $textArray,
                'created_at' => now()
            ], 300);
            
            // Add debug key to the list
            $debugKeys = \Cache::get('template_debug_keys', []);
            $debugKeys[] = $debugKey;
            \Cache::put('template_debug_keys', $debugKeys, 300);

            // Send SMS via Payamak API
            $result = $this->sendSms(
                $apiKey->username,
                $apiKey->api_key,
                $textArray,
                $resident->phone,
                $template->body_id
            );

            // Check if SMS was sent successfully
            if ($this->isSmsSuccessful($result)) {
                // Mark contract as welcome_sent = true
                $this->contract->update(['welcome_sent' => true]);
            }

        } catch (\Exception $e) {
            // Just fail silently
            $this->fail($e);
        }
    }

    /**
     * Get variables for the template
     */
    private function getTemplateVariables($templateId): array
    {
        return MessageVariable::where('template_id', $templateId)
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get()
            ->toArray();
    }

    /**
     * Prepare text array with actual values
     */
    private function prepareTextArray($variables, Resident $resident, Contract $contract): array
    {
        $textArray = [];
        
        foreach ($variables as $variable) {
            $value = $this->getVariableValue($variable['field_name'], $resident, $contract);
            $textArray[] = $value;
        }

        return $textArray;
    }

    /**
     * Get variable value from resident or contract data
     */
    private function getVariableValue($fieldName, Resident $resident, Contract $contract): string
    {
        // Check resident fields
        $residentFields = ['id', 'full_name', 'phone', 'age', 'birth_date', 'job', 'referral_source', 'form', 'document', 'rent', 'trust'];
        if (str_starts_with($fieldName, 'residents.')) {
            $field = str_replace('residents.', '', $fieldName);
            if (in_array($field, $residentFields)) {
                return $resident->$field ?? '';
            }
        }

        // Check contract fields
        $contractFields = ['resident_id', 'payment_date', 'bed_id', 'state', 'start_date', 'end_date', 'welcome_sent', 'welcome_message_response'];
        if (str_starts_with($fieldName, 'contracts.')) {
            $field = str_replace('contracts.', '', $fieldName);
            if (in_array($field, $contractFields)) {
                return $contract->$field ?? '';
            }
        }

        // Check related models
        if ($fieldName === 'rooms.name' && $contract->bed && $contract->bed->room) {
            return $contract->bed->room->name ?? '';
        }

        if ($fieldName === 'beds.name' && $contract->bed) {
            return $contract->bed->name ?? '';
        }

        // Legacy field names for backward compatibility
        if ($fieldName === 'room_name') {
            return $contract->bed && $contract->bed->room ? $contract->bed->room->name : '';
        }

        if ($fieldName === 'bed_name') {
            return $contract->bed ? $contract->bed->name : '';
        }

        return '';
    }

    /**
     * Send SMS via Payamak API
     */
    private function sendSms($username, $password, $textArray, $to, $bodyId): string
    {
        try {
            ini_set("soap.wsdl_cache_enabled", "0");
            $sms = new SoapClient("http://api.payamak-panel.com/post/send.asmx?wsdl", array("encoding" => "UTF-8"));
            
            $data = array(
                "username" => $username,
                "password" => $password,
                "text" => $textArray,
                "to" => $to,
                "bodyId" => $bodyId
            );
            
            $result = $sms->SendByBaseNumber($data);
            $response = $result->SendByBaseNumberResult;
            
            // Store response in cache for temporary display
            $cacheKey = 'sms_response_' . time() . '_' . $this->contract->id;
            \Cache::put($cacheKey, [
                'resident_name' => $this->contract->resident->full_name,
                'resident_phone' => $this->contract->resident->phone,
                'rec_id' => $response,
                'status' => $this->isSmsSuccessful($response),
                'message' => $this->getSmsMessage($response),
                'created_at' => now()
            ], 300); // 5 minutes
            
            // Add key to the list of SMS response keys
            $keys = \Cache::get('sms_response_keys', []);
            $keys[] = $cacheKey;
            \Cache::put('sms_response_keys', $keys, 300);
            
            return $response;
            
        } catch (\Exception $e) {
            return 'API_ERROR';
        }
    }

    /**
     * Get SMS message based on response code
     */
    private function getSmsMessage($result): string
    {
        $messages = [
            '110-' => 'الزام استفاده از ApiKey به جای رمز عبور',
            '109-' => 'الزام تنظیم IP مجاز برای استفاده از API',
            '108-' => 'مسدود شدن IP به دلیل تلاش ناموفق استفاده ازAPI',
            '-10' => 'در میان متغیرهای ارسالی، لینک وجود دارد.',
            '-7' => 'خطایی در شماره فرستنده رخ داده است با پشتیبانی تماس بگیرید',
            '-6' => 'خطای داخلی رخ داده است با پشتیبانی تماس بگیرید',
            '-5' => 'متن ارسالی با متغیرهای مشخص شده در متن پیشفرض همخوانی ندارد',
            '-4' => 'کد متن ارسالی صحیح نمی‌باشد و یا توسط مدیر سامانه تأیید نشده است',
            '-3' => 'خط ارسالی در سیستم تعریف نشده است، با پشتیبانی سامانه تماس بگیرید',
            '-2' => 'محدودیت تعداد شماره، محدودیت هربار ارسال یک شماره موبایل می‌باشد',
            '-1' => 'دسترسی برای استفاده از این وبسودیس غیرفعال است. با پشتیبانی تماس بگیرید',
            '0' => 'نام کاربری یا رمزعبور صحیح نمی‌باشد',
            '2' => 'اعتبار کافی نمی‌باشد',
            '6' => 'سامانه در حال بروزرسانی می‌باشد',
            '7' => 'متن حاوی کلمه فیلتر شده می‌باشد، با واحد اداری تماس بگیرید',
            '10' => 'کاربر موردنظر فعال نمی‌باشد',
            '11' => 'ارسال نشده',
            '12' => 'مدارک کاربر کامل نمی‌باشد',
            '16' => 'شماره گیرنده یافت نشد',
            '17' => 'متن پیامک خالی می‌باشد',
            '18' => 'شماره گیرنده نامعتبر است',
            '19' => 'از محدودیت ساعتی فراتر رفته اید.',
        ];
        
        return $messages[$result] ?? 'خطای ناشناخته';
    }

    /**
     * Check if SMS was sent successfully
     */
    private function isSmsSuccessful($result): bool
    {
        // Success if result is a number (recId) with more than 15 digits
        // According to documentation: "در صورت دریافت (recId) یک عدد بیش از 15 رقم به معنای ارسال موفق"
        return is_numeric($result) && strlen($result) > 15;
    }
}
