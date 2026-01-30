<?php

namespace App\Livewire\Pages\MessageSystem;

use App\Models\ApiKey;
use App\Models\MessageVariable;
use App\Models\MessageTemplate;
use App\Services\PayamakService;
use Livewire\Component;
use Livewire\WithPagination;

class MessageSystemManager extends Component
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
    public $variable_is_active = true;
    public $showVariableModal = false;

    // Message Template properties
    public $selectedApiKey = null;

    protected $rules = [
        'username' => 'required|string|max:255',
        'api_key' => 'required|string|max:255',
        'code' => 'required|string|max:10|unique:message_variables,code',
        'description' => 'required|string|max:255',
        'field_name' => 'required|string|max:255',
    ];

    public function render()
    {
        return view('livewire.pages.message-system.message-system-manager', [
            'apiKeys' => ApiKey::latest()->paginate(10),
            'variables' => MessageVariable::latest()->paginate(10),
            'templates' => MessageTemplate::when($this->selectedApiKey, function($query) {
                return $query->where('is_active', true);
            })->latest()->paginate(10),
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

        MessageVariable::updateOrCreate(
            ['id' => $this->variableId],
            [
                'code' => $this->code,
                'description' => $this->description,
                'field_name' => $this->field_name,
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
        $payamakService = new PayamakService();

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
}
