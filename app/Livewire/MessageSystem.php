<?php

namespace App\Livewire;

use App\Models\ApiKey;
use App\Models\MessageVariable;
use App\Models\MessageTemplate;
use Livewire\Component;
use Livewire\WithPagination;

class MessageSystem extends Component
{
    use WithPagination;

    public $activeTab = 'api-keys';
    
    // API Key properties
    public $apiKeyId = null;
    public $username = '';
    public $api_key = '';
    public $is_active = true;
    public $showApiKeyModal = false;

    // Message Variable properties
    public $variableId = null;
    public $code = '';
    public $description = '';
    public $field_name = '';
    public $custom_field_name = '';
    public $variable_is_active = true;
    public $showVariableModal = false;

    // Message Template properties
    public $selectedApiKey = null;
    public $showLogs = false;
    public $logs = [];

    public function render()
    {
        return view('livewire.message-system', [
            'apiKeys' => ApiKey::latest()->paginate(10),
            'variables' => MessageVariable::latest()->paginate(10),
            'templates' => MessageTemplate::when($this->selectedApiKey, function($query) {
                return $query->where('is_active', true);
            })->where('body_status', 1)->latest()->paginate(10),
        ]);
    }

    // API Key Methods
    public function createApiKey()
    {
        $this->resetApiKeyForm();
        $this->showApiKeyModal = true;
    }

    public function editApiKey($id)
    {
        $apiKey = ApiKey::findOrFail($id);
        $this->apiKeyId = $apiKey->id;
        $this->username = $apiKey->username;
        $this->api_key = $apiKey->api_key;
        $this->is_active = $apiKey->is_active;
        $this->showApiKeyModal = true;
    }

    public function saveApiKey()
    {
        $this->validate([
            'username' => 'required|string|max:255|unique:api_keys,username,' . $this->apiKeyId,
            'api_key' => 'required|string|max:255|unique:api_keys,api_key,' . $this->apiKeyId,
        ]);

        ApiKey::updateOrCreate(
            ['id' => $this->apiKeyId],
            [
                'username' => $this->username,
                'api_key' => $this->api_key,
                'is_active' => $this->is_active,
            ]
        );

        $this->showApiKeyModal = false;
        session()->flash('message', 'API key با موفقیت ذخیره شد.');
        $this->resetApiKeyForm();
    }

    public function deleteApiKey($id)
    {
        ApiKey::findOrFail($id)->delete();
        session()->flash('message', 'API key با موفقیت حذف شد.');
    }

    public function resetApiKeyForm()
    {
        $this->apiKeyId = null;
        $this->username = '';
        $this->api_key = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    // Message Variable Methods
    public function createVariable()
    {
        $this->resetVariableForm();
        $this->showVariableModal = true;
    }

    public function editVariable($id)
    {
        $variable = MessageVariable::findOrFail($id);
        $this->variableId = $variable->id;
        $this->code = $variable->code;
        $this->description = $variable->description;
        
        // Set field_name to the database field name for display
        $this->field_name = $variable->field_name;
        $this->variable_is_active = $variable->is_active;
        $this->showVariableModal = true;
    }

    public function saveVariable()
    {
        $this->validate([
            'code' => 'required|string|max:10|unique:message_variables,code,' . $this->variableId,
            'description' => 'required|string|max:255',
            'field_name' => 'required|string|max:255',
        ]);

        // Handle custom field name
        $finalFieldName = $this->field_name;
        if ($this->field_name === 'custom') {
            $this->validate([
                'custom_field_name' => 'required|string|max:255',
            ]);
            $finalFieldName = $this->custom_field_name;
        }

        // Get the database field name (table.column format)
        $finalFieldName = $this->getFieldInfo($finalFieldName);

        MessageVariable::updateOrCreate(
            ['id' => $this->variableId],
            [
                'code' => $this->code,
                'description' => $this->description,
                'field_name' => $finalFieldName,
                'is_active' => $this->variable_is_active,
            ]
        );

        $this->showVariableModal = false;
        session()->flash('message', 'متغیر با موفقیت ذخیره شد.');
        $this->resetVariableForm();
    }

    public function deleteVariable($id)
    {
        MessageVariable::findOrFail($id)->delete();
        session()->flash('message', 'متغیر با موفقیت حذف شد.');
    }

    public function resetVariableForm()
    {
        $this->variableId = null;
        $this->code = '';
        $this->description = '';
        $this->field_name = '';
        $this->custom_field_name = '';
        $this->variable_is_active = true;
        $this->resetErrorBag();
    }

    // Message Template Methods
    public function syncTemplates()
    {
        if (!$this->selectedApiKey) {
            session()->flash('error', 'لطفاً ابتدا یک API key انتخاب کنید.');
            return;
        }

        $apiKey = ApiKey::findOrFail($this->selectedApiKey);
        $payamakService = new \App\Services\PayamakService();

        if ($payamakService->syncTemplates($apiKey)) {
            session()->flash('message', 'قالب‌ها با موفقیت همگام‌سازی شد.');
        } else {
            session()->flash('error', 'خطا در همگام‌سازی قالب‌ها. لطفاً اطلاعات API key را بررسی کنید.');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function closeApiKeyModal()
    {
        $this->showApiKeyModal = false;
        $this->resetApiKeyForm();
    }

    public function closeVariableModal()
    {
        $this->showVariableModal = false;
        $this->resetVariableForm();
    }

    public function showLogs()
    {
        try {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile)) {
                $lines = file($logFile);
                // Get last 50 lines
                $this->logs = array_slice(array_reverse($lines), 0, 50);
                $this->showLogs = true;
            } else {
                $this->logs = ['No log file found at: ' . $logFile];
                $this->showLogs = true;
            }
        } catch (\Exception $e) {
            $this->logs = ['Error reading log file: ' . $e->getMessage()];
            $this->showLogs = true;
        }
    }

    private function convertToEnglish($string)
    {
        // Persian to English mapping for field names
        $persianToEnglish = [
            'نام اقامتگر' => 'resident_name',
            'نام خانوادگی اقامتگر' => 'resident_family_name',
            'کد ملی اقامتگر' => 'resident_national_code',
            'شماره تلفن اقامتگر' => 'resident_phone',
            'شماره موبایل اقامتگر' => 'resident_mobile',
            'ایمیل اقامتگر' => 'resident_email',
            'آدرس اقامتگر' => 'resident_address',
            'تاریخ تولد اقامتگر' => 'resident_birth_date',
            'تحصیلات اقامتگر' => 'resident_education',
            'شغل اقامتگر' => 'resident_job',
            'تماس اضطراری اقامتگر' => 'resident_emergency_contact',
            'تلفن اضطراری اقامتگر' => 'resident_emergency_phone',
            'شماره قرارداد' => 'contract_number',
            'تاریخ شروع قرارداد' => 'contract_start_date',
            'تاریخ پایان قرارداد' => 'contract_end_date',
            'مبلغ قرارداد' => 'contract_amount',
            'نوع پرداخت قرارداد' => 'contract_payment_type',
            'شماره اتاق قرارداد' => 'contract_room_number',
            'شماره تخت قرارداد' => 'contract_bed_number',
            'مدت قرارداد' => 'contract_duration',
            'وضعیت قرارداد' => 'contract_status',
            'یادداشت‌های قرارداد' => 'contract_notes',
        ];

        // Check if the string exists in the mapping
        if (isset($persianToEnglish[$string])) {
            return $persianToEnglish[$string];
        }

        // Return the original string if no mapping found
        return $string;
    }

    public function getFieldInfo($fieldName)
    {
        // Map field names to their database table and column names
        $fieldTableMap = [
            // Residents table fields
            'id' => 'residents.id',
            'full_name' => 'residents.full_name',
            'phone' => 'residents.phone',
            'age' => 'residents.age',
            'birth_date' => 'residents.birth_date',
            'job' => 'residents.job',
            'referral_source' => 'residents.referral_source',
            'form' => 'residents.form',
            'document' => 'residents.document',
            'rent' => 'residents.rent',
            'trust' => 'residents.trust',
            'created_at' => 'residents.created_at',
            'updated_at' => 'residents.updated_at',
            'deleted_at' => 'residents.deleted_at',
            
            // Contracts table fields
            'resident_id' => 'contracts.resident_id',
            'payment_date' => 'contracts.payment_date',
            'bed_id' => 'contracts.bed_id',
            'state' => 'contracts.state',
            'start_date' => 'contracts.start_date',
            'end_date' => 'contracts.end_date',
            'welcome_sent' => 'contracts.welcome_sent',
            'welcome_message_response' => 'contracts.welcome_message_response',
            'created_at' => 'contracts.created_at',
            'updated_at' => 'contracts.updated_at',
            'deleted_at' => 'contracts.deleted_at',
            
            // Manual fields
            'room_name' => 'rooms.name',
            'bed_name' => 'beds.name',
        ];

        // Return the mapped value or the field name if not found
        return $fieldTableMap[$fieldName] ?? $fieldName;
    }
}
