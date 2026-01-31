<?php

namespace App\Livewire\Modals;

use App\Models\Bed;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\Unit;
use App\Models\Room;
use App\Repositories\BedRepository;
use App\Rules\PersianDate;
use App\Traits\HasDateConversion;
use App\Traits\livewire\RepositoryResolver;
use App\Traits\livewire\ServiceResolver;
use Carbon\Carbon;
use Livewire\Component;
use Morilog\Jalali\Jalalian;

class ResidentModal extends Component
{
    use HasDateConversion;
    use RepositoryResolver;
    use ServiceResolver;

    // Modal state
    public $showModal = false;
    public $selectedBed = null;
    public $modalMode = 'add';
    public $editingResidentId = null;

    // Unit and Room selection
    public $selectedUnitId = null;
    public $selectedRoomId = null;
    public $units = [];
    public $rooms = [];
    public $beds = [];

    // Form properties for resident
    public $full_name_modal = '';
    public $phone_modal = '';
    public $age_modal = 0;
    public $birth_date_jalali_modal = '';
    public $birth_date_modal = '';
    public $job_modal = '';
    public $referral_source_modal = '';
    public $form_modal = false;
    public $document_modal = false;
    public $rent_modal = false;
    public $trust_modal = false;

    // Form properties for contract
    public $payment_date_modal = '';
    public $state_modal = '';
    public $start_date = '';
    public $end_date = '';

    protected $bedRepository;

    public function __construct()
    {
        $this->bedRepository = $this->repository(BedRepository::class);
    }

    protected $listeners = [
        'openAddResidentModal' => 'openAddModal',
        'openEditResidentModal' => 'openEditModal',
        'closeModal' => 'closeModal',
        'phoneModalUpdated' => 'updatePhoneModal'
    ];

    protected function rules()
    {
        $rules = [
            'full_name_modal' => 'required|string|max:255',
            'phone_modal' => 'nullable|regex:/^09[0-9]{9}$/|min:11|max:11',
            'payment_date_modal' => ['required', new PersianDate],
            'state_modal' => 'required|in:rezerve,nightly,active,leaving,exit',
        ];

        // اگر تخت از قبل انتخاب نشده باشد، باید واحد و اتاق و تخت انتخاب شود
        if (!$this->selectedBed || !is_array($this->selectedBed) || !isset($this->selectedBed['id'])) {
            $rules['selectedUnitId'] = 'required|exists:units,id';
            $rules['selectedRoomId'] = 'required|exists:rooms,id';
            if (!$this->selectedBed || !is_array($this->selectedBed) || !isset($this->selectedBed['id'])) {
                $rules['selectedBed'] = 'required';
            }
        }

        return $rules;
    }

    protected $messages = [
        'full_name_modal.required' => 'نام و نام خانوادگی الزامی است',
        'phone_modal.regex' => 'شماره تلفن باید با 09 شروع شده و 11 رقم باشد',
        'phone_modal.min' => 'شماره تلفن باید 11 رقم باشد',
        'phone_modal.max' => 'شماره تلفن باید 11 رقم باشد',
        'payment_date_modal.required' => 'تاریخ پرداخت الزامی است',
        'state_modal.required' => 'وضعیت رو مشخص کنید',
    ];

    public function openAddModal($bedName = null, $roomName = null)
    {
        $this->modalMode = 'add';
        $this->editingResidentId = null;

        // اگر تخت و اتاق از قبل مشخص شده باشد (از tablelists)
        if ($bedName && $roomName) {
            $bed = Bed::with('room')
                ->where('name', $bedName)
                ->whereHas('room', function ($query) use ($roomName) {
                    $query->where('name', $roomName);
                })
                ->first();

            if ($bed) {
                $this->selectedBed = [
                    'id' => $bed->id,
                    'name' => $bed->name,
                    'room' => $bed->room->name,
                ];
                $this->selectedUnitId = $bed->room->unit_id;
                $this->selectedRoomId = $bed->room_id;
                $this->loadRooms();
                $this->loadBeds();
            }
        } else {
            // حالت جدید: انتخاب واحد و اتاق
            $this->selectedBed = null;
            $this->loadUnits();
        }

        $this->resetForm();
        $this->showModal = true;
        $this->resetValidation();
        $this->dispatch('show-modal');
    }

    public function loadUnits()
    {
        $this->units = Unit::all()->toArray();
    }

    public function updatedSelectedUnitId($value)
    {
        $this->selectedRoomId = null;
        $this->selectedBed = null;
        $this->beds = [];
        if ($value) {
            $this->loadRooms();
        } else {
            $this->rooms = [];
        }
    }

    public function loadRooms()
    {
        if ($this->selectedUnitId) {
            $this->rooms = Room::where('unit_id', $this->selectedUnitId)->get()->toArray();
        } else {
            $this->rooms = [];
        }
    }

    public function updatedSelectedRoomId($value)
    {
        $this->selectedBed = null;
        if ($value) {
            $this->loadBeds();
        } else {
            $this->beds = [];
        }
    }

    public function updatedSelectedBed($value)
    {
        if ($value && is_string($value)) {
            try {
                $bedData = json_decode($value, true);
                if ($bedData && isset($bedData['id']) && isset($bedData['name'])) {
                    $room = Room::with('unit')->find($this->selectedRoomId);
                    $this->selectedBed = [
                        'id' => (int)$bedData['id'],
                        'name' => $bedData['name'],
                        'room' => $room ? $room->name : '',
                    ];
                } else {
                    $this->selectedBed = null;
                }
            } catch (\Exception $e) {
                $this->selectedBed = null;
            }
        } elseif (!$value) {
            $this->selectedBed = null;
        }
    }

    public function loadBeds()
    {
        if ($this->selectedRoomId) {
            $this->beds = Bed::where('room_id', $this->selectedRoomId)
                ->orderBy('name')
                ->get()
                ->map(function ($bed) {
                    return [
                        'id' => $bed->id,
                        'name' => $bed->name,
                        'state_ratio_resident' => $bed->state_ratio_resident,
                    ];
                })
                ->toArray();
        } else {
            $this->beds = [];
        }
    }

    public function openEditModal($residentId)
    {
        $this->modalMode = 'edit';
        $this->editingResidentId = $residentId;

        // Optimize with eager loading to prevent N+1 queries
        $resident = Resident::with([
            'contract' => function ($query) {
                $query->latest()->with('bed.room');
            }
        ])->find($residentId);

        if ($resident) {
            $contract = $resident->contract->first();

            if ($contract && $contract->bed) {
                $this->selectedBed = [
                    'id' => $contract->bed->id,
                    'name' => $contract->bed->name,
                    'room' => $contract->bed->room->name,
                ];
            } else {
                $this->selectedBed = null;
            }

            $this->full_name_modal = $resident->full_name ?? '';
            $this->phone_modal = $resident->phone ?? '';
            $this->age_modal = $resident->age ?? '';

            // اصلاح بخش تبدیل تاریخ تولد
            if ($resident->birth_date) {
                $this->birth_date_modal = $resident->birth_date;
                $this->birth_date_jalali_modal = Jalalian::forge($resident->birth_date)->format('Y/m/d');
            } else {
                $this->birth_date_modal = '';
                $this->birth_date_jalali_modal = '';
            }

            $this->job_modal = $resident->job ?? '';
            $this->referral_source_modal = $resident->referral_source ?? '';
            $this->form_modal = $resident->form ?? false;
            $this->document_modal = $resident->document ?? false;
            $this->rent_modal = $resident->rent ?? false;
            $this->trust_modal = $resident->trust ?? false;

            if ($contract) {
                $this->payment_date_modal = $this->toJalali($contract->payment_date) ?? '';
                $this->state_modal = $contract->state ?? '';
            }

            $this->showModal = true;
            $this->resetValidation();
            $this->dispatch('show-modal');

        } else {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'اقامتگر مورد نظر یافت نشد',
                'timer' => 3000
            ]);
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedBed = null;
        $this->selectedUnitId = null;
        $this->selectedRoomId = null;
        $this->units = [];
        $this->rooms = [];
        $this->beds = [];
        $this->modalMode = 'add';
        $this->editingResidentId = null;
        $this->resetForm();
        $this->dispatch('hide-modal');
    }

    public function updatePhoneModal($value)
    {
        $this->phone_modal = $value;
        $this->validateOnly('phone_modal');
    }

    private function resetForm()
    {
        $this->full_name_modal = '';
        $this->phone_modal = '';
        $this->age_modal = '';
        $this->job_modal = '';
        $this->birth_date_modal = '';
        $this->birth_date_jalali_modal = '';
        $this->referral_source_modal = '';
        $this->form_modal = false;
        $this->document_modal = false;
        $this->rent_modal = false;
        $this->trust_modal = false;
        $this->state_modal = '';
        $this->payment_date_modal = '';
        
        // Reset unit/room selection only if bed was not pre-selected
        if (!$this->selectedBed) {
            $this->selectedUnitId = null;
            $this->selectedRoomId = null;
            $this->rooms = [];
            $this->beds = [];
        }
        
        $this->resetValidation();
    }

    public function saveResident()
    {
        $this->phone_modal = str_replace('-', '', $this->phone_modal);
        $this->validate();

        if ($this->modalMode == 'add') {
            $this->createNewResident();
        } else {
            $this->updateExistingResident();
        }

        $this->closeModal();
    }

    private function createNewResident()
    {
        // اگر تخت انتخاب نشده باشد، از فیلدهای انتخاب شده استفاده کن
        if (!$this->selectedBed && $this->selectedRoomId) {
            // این حالت نباید رخ دهد چون validation آن را چک می‌کند
            // اما برای اطمینان چک می‌کنیم
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'لطفاً یک تخت انتخاب کنید',
                'timer' => 3000
            ]);
            return;
        }

        $this->start_date = Carbon::now()->format('Y-m-d');
        $this->payment_date_modal = $this->toMiladi($this->payment_date_modal);

        // تبدیل تاریخ تولد به میلادی قبل از ذخیره
        $birthDateMiladi = null;
        if ($this->birth_date_modal) {
            $birthDateMiladi = $this->birth_date_modal; // از آپدیت متد استفاده می‌کنیم
        }

        $resident = Resident::create([
            'full_name' => $this->full_name_modal,
            'phone' => $this->phone_modal,
            'age' => $this->age_modal ?: null,
            'birth_date' => $birthDateMiladi,
            'job' => $this->job_modal ?: null,
            'referral_source' => $this->referral_source_modal ?: null,
            'form' => $this->form_modal,
            'document' => $this->document_modal ?: false,
            'rent' => $this->rent_modal,
            'trust' => $this->trust_modal,
        ]);

        $bedId = is_array($this->selectedBed) ? $this->selectedBed['id'] : $this->selectedBed;

        $contract = Contract::create([
            'resident_id' => $resident->id,
            'bed_id' => $bedId,
            'payment_date' => $this->payment_date_modal,
            'state' => $this->state_modal,
            'start_date' => $this->start_date,
        ]);

        \App\Models\Bed::where('id', $bedId)
            ->update([
                'state' => 'active',
                'state_ratio_resident' => in_array($this->state_modal, ['nightly', 'active', 'leaving'])
                    ? 'full'
                    : ($this->state_modal === 'rezerve' ? 'rezerve' : null)
            ]);

        // ارسال خودکار پیام خوشامدگویی برای اقامتگر جدید
        $this->sendWelcomeMessageToNewResident($resident, $contract);

        $this->dispatch('show-toast', [
            'type' => 'success',
            'title' => 'موفقیت!',
            'description' => "اقامتگر {$this->full_name_modal} با موفقیت اضافه شد",
            'timer' => 3000
        ]);

        $this->dispatch('residentAdded');
        
        // Clear cache to ensure fresh data
        \App\Services\Report\AllReportService::clearAllCache();
    }

    private function updateExistingResident(): void
    {

        $resident = Resident::find($this->editingResidentId);
        if ($resident) {
            // تبدیل تاریخ تولد به میلادی برای ذخیره در دیتابیس
            $birthDateMiladi = null;
            if ($this->birth_date_modal) {
                $birthDateMiladi = $this->birth_date_modal; // از آپدیت متد استفاده می‌کنیم
            }
            $resident->update([
                'full_name' => $this->full_name_modal,
                'phone' => $this->phone_modal,
                'age' => $this->age_modal ?: null,
                'job' => $this->job_modal ?: null,
                'referral_source' => $this->referral_source_modal ?: null,
                'form' => $this->form_modal,
                'document' => $this->document_modal ?: false,
                'rent' => $this->rent_modal,
                'trust' => $this->trust_modal,
                'birth_date' => $birthDateMiladi, // اصلاح شده
            ]);
            $contract = $resident->contract()->latest()->first();
            if ($contract) {
                $contract->update([
                    'payment_date' => $this->toMiladi($this->payment_date_modal),
                    'state' => $this->state_modal,
                ]);
            }

            \App\Models\Bed::where('id', $contract->bed_id)
                ->update([
                    'state' => 'active',
                    'state_ratio_resident' => in_array($this->state_modal, ['nightly', 'active', 'leaving'])
                        ? 'full'
                        : ($this->state_modal === 'rezerve' ? 'rezerve' : null)
                ]);

            // بررسی ارسال پیام خوشامدگویی در حالت ویرایش (فقط اگر فعال شده و قبلاً ارسال نشده)
            if ($contract->state === 'active' && !$contract->welcome_sent) {
                $this->sendWelcomeMessageToNewResident($resident, $contract);
            }

            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'موفقیت!',
                'description' => "اطلاعات {$this->full_name_modal} با موفقیت بروزرسانی شد",
                'timer' => 4000
            ]);

            $this->dispatch('residentAdded');
        
        // Clear cache to ensure fresh data
        \App\Services\Report\AllReportService::clearAllCache();
        }
    }

    public function updatedBirthDateJalaliModal($value)
    {
        if ($value) {
            try {
                if (preg_match('/^\d{4}\/\d{1,2}\/\d{1,2}$/', $value)) {
                    $jalaliDate = Jalalian::fromFormat('Y/m/d', $value);
                    $miladiDate = $jalaliDate->toCarbon();

                    if ($miladiDate->isValid()) {
                        // ذخیره تاریخ میلادی در متغیر داخلی
                        $this->birth_date_modal = $miladiDate->format('Y-m-d');
                        $this->calculateAge();
                    } else {
                        throw new \Exception('تاریخ نامعتبر است');
                    }
                } else {
                    throw new \Exception('فرمت تاریخ نامعتبر است');
                }
            } catch (\Exception $e) {
                $this->birth_date_modal = '';
                $this->age_modal = 0;
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'خطا!',
                    'description' => 'لطفاً تاریخ تولد را به فرمت صحیح (مثال: 1400/01/01) وارد کنید',
                    'timer' => 3000
                ]);
            }
        } else {
            $this->birth_date_modal = '';
            $this->age_modal = 0;
        }
    }

    public function calculateAge()
    {
        if ($this->birth_date_modal) {
            $birthDate = Carbon::parse($this->birth_date_modal);
            $this->age_modal = $birthDate->age;
        } else {
            $this->age_modal = 0;
        }
    }

    public function mount()
    {
        $this->loadUnits();
    }

    public function render()
    {
        return view('livewire.modals.resident-modal');
    }

    /**
     * ارسال خودکار پیام خوشامدگویی برای اقامتگر جدید
     */
    private function sendWelcomeMessageToNewResident($resident, $contract)
    {
        try {
            // فقط برای قراردادهای فعال ارسال شود
            if ($contract->state !== 'active') {
                \Illuminate\Support\Facades\Log::info("Contract {$contract->id} is not active (state: {$contract->state}), skipping welcome message");
                return;
            }

            // بررسی اینکه قبلاً پیام ارسال نشده باشد
            if ($contract->welcome_sent) {
                \Illuminate\Support\Facades\Log::info("Welcome message already sent for contract {$contract->id}");
                return;
            }

            // بررسی وجود پیام خوشامدگویی فعال
            $welcomeMessage = \App\Models\WelcomeMessage::where('is_active', true)->first();
            if (!$welcomeMessage) {
                \Illuminate\Support\Facades\Log::info("No active welcome message found");
                return;
            }

            // بررسی تاریخ اقامتگر نسبت به تاریخ پیام
            if (!$resident->created_at->gte($welcomeMessage->send_date)) {
                \Illuminate\Support\Facades\Log::info("Resident created before welcome message date, skipping");
                return;
            }

            // بررسی وجود قالب پیام فعال
            $template = \App\Models\MessageTemplate::where('body_id', $welcomeMessage->body_id)
                ->where('body_status', 1)
                ->first();
                
            if (!$template) {
                \Illuminate\Support\Facades\Log::info("No active template found for body_id: {$welcomeMessage->body_id}");
                return;
            }

            // بررسی وجود API key فعال
            $apiKey = \App\Models\ApiKey::where('is_active', true)->first();
            if (!$apiKey) {
                \Illuminate\Support\Facades\Log::info("No active API key found");
                return;
            }

            \Illuminate\Support\Facades\Log::info("=== SENDING WELCOME MESSAGE TO NEW RESIDENT ===");
            \Illuminate\Support\Facades\Log::info("Resident: {$resident->full_name}");
            \Illuminate\Support\Facades\Log::info("Phone: {$resident->phone}");
            \Illuminate\Support\Facades\Log::info("Contract ID: {$contract->id}");
            \Illuminate\Support\Facades\Log::info("Body ID: {$welcomeMessage->body_id}");

            // دریافت متغیرها و ساخت آرایه متن
            $variables = \App\Models\MessageVariable::where('is_active', true)->orderBy('id')->get();
            $textArray = [];
            
            foreach ($variables as $variable) {
                $value = $this->getVariableValueForResident($variable->field_name, $resident);
                $textArray[] = $value;
            }

            \Illuminate\Support\Facades\Log::info("Text Array: " . json_encode($textArray, JSON_UNESCAPED_UNICODE));

            // ارسال پیام با استفاده از همان متد MessageSender
            $messageSender = new \App\Livewire\MessageSender();
            $result = $messageSender->sendSmsViaPayamak($apiKey, $textArray, $resident->phone, $welcomeMessage->body_id, $contract);

            // بررسی نتیجه
            if (is_numeric($result) && $result > 0) {
                \Illuminate\Support\Facades\Log::info("✅ Welcome message sent successfully to {$resident->full_name}, RecId: {$result}");
                
                // نمایش پیام موفقیت به کاربر
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'پیام خوشامدگویی!',
                    'description' => "پیام خوشامدگویی برای {$resident->full_name} با موفقیت ارسال شد (کد پیگیری: {$result})",
                    'timer' => 5000
                ]);
                
            } else {
                \Illuminate\Support\Facades\Log::error("❌ Welcome message sending failed for {$resident->full_name}: {$result}");
                
                // نمایش پیام خطا به کاربر
                $this->dispatch('show-toast', [
                    'type' => 'warning',
                    'title' => 'خطا در ارسال پیام!',
                    'description' => "خطا در ارسال پیام خوشامدگویی برای {$resident->full_name}: {$result}",
                    'timer' => 5000
                ]);
            }

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in sendWelcomeMessageToNewResident: ' . $e->getMessage());
            
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا در ارسال پیام!',
                'description' => "خطا در ارسال پیام خوشامدگویی: " . $e->getMessage(),
                'timer' => 5000
            ]);
        }
    }

    /**
     * دریافت مقدار متغیر برای اقامتگر (با تبدیل تاریخ به شمسی)
     */
    private function getVariableValueForResident($fieldName, $resident)
    {
        try {
            switch ($fieldName) {
                case 'residents.full_name':
                    return $resident->full_name ?? 'نامشخص';
                    
                case 'contracts.payment_date':
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->first();
                    if ($contract && $contract->payment_date) {
                        try {
                            return \Morilog\Jalali\Jalalian::fromDateTime($contract->payment_date)->format('Y/m/d');
                        } catch (\Exception $e) {
                            return $contract->payment_date;
                        }
                    }
                    return 'نامشخص';
                    
                case 'contracts.start_date':
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->first();
                    if ($contract && $contract->start_date) {
                        try {
                            return \Morilog\Jalali\Jalalian::fromDateTime($contract->start_date)->format('Y/m/d');
                        } catch (\Exception $e) {
                            return $contract->start_date;
                        }
                    }
                    return 'نامشخص';
                    
                case 'contracts.end_date':
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->first();
                    if ($contract && $contract->end_date) {
                        try {
                            return \Morilog\Jalali\Jalalian::fromDateTime($contract->end_date)->format('Y/m/d');
                        } catch (\Exception $e) {
                            return $contract->end_date;
                        }
                    }
                    return 'نامشخص';
                    
                case 'rooms.name':
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->with(['bed.room'])
                        ->first();
                    return $contract && $contract->bed && $contract->bed->room 
                        ? $contract->bed->room->name 
                        : 'نامشخص';
                        
                case 'beds.name':
                    $contract = \App\Models\Contract::where('resident_id', $resident->id)
                        ->where('state', 'active')
                        ->with('bed')
                        ->first();
                    return $contract && $contract->bed 
                        ? $contract->bed->name 
                        : 'نامشخص';
                    
                default:
                    if ($resident && isset($resident->{$fieldName})) {
                        return $resident->{$fieldName};
                    }
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Variable not found: ' . $fieldName . ' Error: ' . $e->getMessage());
        }
        
        return '{' . $fieldName . '}';
    }
}
