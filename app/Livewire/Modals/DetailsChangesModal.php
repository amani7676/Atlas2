<?php

namespace App\Livewire\Modals;

use App\Models\Bed;
use App\Models\Note;
use App\Models\Resident;
use App\Models\Contract;
use App\Repositories\BedRepository;
use App\Traits\livewire\RepositoryResolver;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\On;

class DetailsChangesModal extends Component
{
    use RepositoryResolver;

    public bool $showModal = false;
    public $residentId = null;
    public $resident = null;
    public $contract = null;

    // Expanded sections state
    public $expandedSections = [
        'EndedOrDelete' => false,
        'moving' => false,
        'notesInfo' => false
    ];

    // Form data - Personal Info
    public $fullName = '';
    public $nationalId = '';
    public $phone = '';
    public $birthDate = '';

    // Form data - Contract Info
    public $contractStart = '';
    public $contractEnd = '';
    public $rentAmount = '';
    public $contractType = 'فعال';

    // Form data - Payment Info
    public $lastPayment = '';
    public $paidAmount = '';
    public $paymentStatus = '';

    // Form data - Notes
    public $newNote = '';
    public $previousNotes = [];
    public array $noteTypes =
        [
            'payment' => "پرداختی",
            'end_date' => "سررسید",
            'exit' => "خروج",
            'demand' => "طلب",
            'other' => "سایر"
        ];
    public $selectedNoteType = null;
    public $beds = null;
    public $selectBed = null;
    protected $bedRepository;

    public function onNoteTypeChanged()
    {
        // Reset note when type changes
        if ($this->selectedNoteType !== 'end_date') {
            $this->newNote = '';
        }
        $this->dispatch('note-type-changed');
    }

    public function __construct()
    {
        $this->bedRepository = $this->repository(BedRepository::class);
    }

    public function mount(): void
    {
        $this->beds = $this->bedRepository->getBeds();
    }

    #[On('openDetailsChangeModal')]
    public function openModal($residentId)
    {

        $this->residentId = $residentId;
        $this->loadResidentData();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['residentId', 'resident', 'contract']);
        $this->resetForm();
        $this->dispatch('residentDataUpdated');
    }

    private function loadResidentData()
    {
        if (!$this->residentId) return;

        $this->resident = Resident::find($this->residentId);
        if (!$this->resident) return;

        // Load contract data
        $this->contract = $this->resident->contract()->latest()->first();

        // Fill form with existing data
        $this->fillFormWithExistingData();
    }

    private function fillFormWithExistingData()
    {
        if ($this->resident) {
            $this->fullName = $this->resident->full_name ?? '';
            $this->nationalId = $this->resident->national_id ?? '';
            $this->phone = $this->formatPhoneNumberForDisplay($this->resident->phone ?? '');
            $this->birthDate = $this->resident->birth_date ?? '';
        }

        if ($this->contract) {
            $this->contractStart = $this->contract->start_date ?? '';
            $this->contractEnd = $this->contract->end_date ?? '';
            $this->rentAmount = $this->contract->rent_amount ?? '';
            $this->contractType = $this->getContractTypeText($this->contract->state ?? 'active');
            $this->lastPayment = $this->contract->payment_date ?? '';
            $this->paidAmount = $this->contract->paid_amount ?? '';
            $this->paymentStatus = $this->contract->payment_status ?? '';
        }

        // Load notes (assuming you have a notes relationship)
        $this->loadNotes();
    }

    private function loadNotes()
    {
        if (!$this->resident) return;

        $this->previousNotes = $this->resident->notes
            ->mapWithKeys(function ($note) {
                return [
                    $note->id => [
                        'resident_id' => $note->resident_id,
                        'note' => $note->note,
                        'type' => $note->type,
                        // اضافه کردن نوع متناظر برای نمایش بهتر (اختیاری)
                        'type_text' => $this->noteTypes[$note->type] ?? 'سایر'
                    ]
                ];
            })
            ->toArray();

    }

    private function getContractTypeText($state)
    {
        return match ($state) {
            'active' => 'فعال',
            'reserve' => 'رزرو',
            'nightly' => 'شبانه',
            'leaving' => 'خروج',
            default => 'فعال'
        };
    }

    private function getContractStateFromText($text)
    {
        return match ($text) {
            'فعال' => 'active',
            'رزرو' => 'reserve',
            'شبانه' => 'nightly',
            'خروج' => 'leaving',
            default => 'active'
        };
    }

    private function formatPhoneNumberForDisplay($phoneNumber): string
    {
        $cleanPhone = preg_replace('/\D/', '', $phoneNumber);

        if (strlen($cleanPhone) == 11 && substr($cleanPhone, 0, 1) == '0') {
            return substr($cleanPhone, 0, 4) . '-' . substr($cleanPhone, 4, 3) . '-' . substr($cleanPhone, 7, 4);
        }

        return $phoneNumber;
    }

    private function sanitizePhoneNumberForDatabase($phoneNumber): string
    {
        return preg_replace('/\D/', '', $phoneNumber);
    }

    public function updatedPhone($value)
    {
        $this->phone = $this->formatPhoneNumberForDisplay($value);
        $this->validatePhoneNumber();
    }

    private function validatePhoneNumber(): bool
    {
        $cleanPhone = preg_replace('/\D/', '', $this->phone);

        $this->resetErrorBag('phone');

        if (strlen($cleanPhone) != 11) {
            $this->addError('phone', 'شماره تلفن باید دقیقا 11 رقم باشد');
            return false;
        }

        if (substr($cleanPhone, 0, 1) != '0') {
            $this->addError('phone', 'شماره تلفن باید با 0 شروع شود');
            return false;
        }

        if (substr($cleanPhone, 1, 1) != '9') {
            $this->addError('phone', 'شماره تلفن وارد شده معتبر نمی‌باشد');
            return false;
        }

        return true;
    }

    public function toggleSection($section)
    {
        // ابتدا همه بخش‌ها را ببندی // اگر بخش مورد نظر در حال باز شدن است
        if (!$this->expandedSections[$section]) {
            // ابتدا همه بخش‌ها را ببندید
            foreach ($this->expandedSections as $key => $value) {
                $this->expandedSections[$key] = false;
            }
        }

        //    // وضعیت بخش مورد نظر را تغییر دهید
        $this->expandedSections[$section] = !$this->expandedSections[$section];
    }

    public function addNote(): void
    {
        $this->validate([
            'newNote' => 'required|string|max:500',
            'selectedNoteType' => 'required|in:payment,end_date,exit,demand,other'
        ], [
            'newNote.required' => 'متن یادداشت الزامی است',
            'selectedNoteType.required' => 'نوع یادداشت را انتخاب کنید'
        ]);

        try {


            // در اینجا می‌توانید یادداشت را به دیتابیس هم ذخیره کنید
            // به عنوان مثال:
            $note = Note::create([
                'resident_id' => $this->residentId,
                'type' => $this->selectedNoteType,
                'note' => $this->newNote,
            ]);

            // ریست کردن فیلدهای یادداشت
            $this->reset(['newNote', 'selectedNoteType']);

            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'موفقیت!',
                'description' => 'یادداشت با موفقیت اضافه شد',
                'timer' => 3000
            ]);

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در اضافه کردن یادداشت: ' . $e->getMessage(),
                'timer' => 4000
            ]);
        }
        $this->loadNotes();
        $this->dispatch('update_notes');
    }

    public function removeNote($index): void
    {
        try {
            \App\Models\Note::where('id', $index)->delete();
            $this->loadNotes();
            // در اینجا می‌توانید یادداشت را از دیتابیس هم حذف کنید


            //اطلاع دادن به پدر که نوت اپدیت بشه
            $this->dispatch('update_notes');

            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'موفقیت!',
                'description' => 'یادداشت با موفقیت حذف شد',
                'timer' => 3000
            ]);


        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف یادداشت: ' . $e->getMessage(),
                'timer' => 4000
            ]);
        }
    }

    public function saveChanges(): void
    {
        try {
            // Validation
            $this->validate([
                'fullName' => 'required|string|max:255',
                'nationalId' => 'nullable|string|max:10',
                'phone' => 'required|string',
                'birthDate' => 'nullable|string',
                'contractStart' => 'nullable|string',
                'contractEnd' => 'nullable|string',
                'rentAmount' => 'nullable|numeric',
                'contractType' => 'required|in:فعال,رزرو,شبانه,خروج',
                'lastPayment' => 'nullable|string',
                'paidAmount' => 'nullable|numeric',
                'paymentStatus' => 'nullable|in:paid,pending,overdue'
            ], [
                'fullName.required' => 'نام کامل الزامی است',
                'phone.required' => 'شماره تماس الزامی است',
                'contractType.required' => 'نوع قرارداد الزامی است'
            ]);

            // Additional phone validation
            if (!$this->validatePhoneNumber()) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'خطا!',
                    'description' => 'لطفا شماره تلفن را به درستی وارد کنید',
                    'timer' => 4000
                ]);
                return;
            }

            // Update resident data
            if ($this->resident) {
                $this->resident->update([
                    'full_name' => $this->fullName,
                    'national_id' => $this->nationalId,
                    'phone' => $this->sanitizePhoneNumberForDatabase($this->phone),
                    'birth_date' => $this->birthDate,
                ]);
            }

            // Update contract data
            if ($this->contract) {
                $this->contract->update([
                    'start_date' => $this->contractStart,
                    'end_date' => $this->contractEnd,
                    'rent_amount' => $this->rentAmount,
                    'state' => $this->getContractStateFromText($this->contractType),
                    'payment_date' => $this->lastPayment,
                    'paid_amount' => $this->paidAmount,
                    'payment_status' => $this->paymentStatus,
                ]);
            }

            // Dispatch success message
            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'موفقیت!',
                'description' => 'تغییرات با موفقیت ذخیره شد',
                'timer' => 3000
            ]);

            // Dispatch event to refresh parent component
            $this->dispatch('residentDataUpdated');

            $this->closeModal();

        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در ذخیره تغییرات: ' . $e->getMessage(),
                'timer' => 4000
            ]);
        }
    }

    private function resetForm(): void
    {
        $this->fullName = '';
        $this->nationalId = '';
        $this->phone = '';
        $this->birthDate = '';
        $this->contractStart = '';
        $this->contractEnd = '';
        $this->rentAmount = '';
        $this->contractType = 'فعال';
        $this->lastPayment = '';
        $this->paidAmount = '';
        $this->paymentStatus = '';
        $this->newNote = '';
        $this->previousNotes = [];

        $this->expandedSections = [
            'EndedOrDelete' => false,
            'moving' => false,
            'notesInfo' => false
        ];
    }

    public function changeBedForResident($residentId): void
    {
        // پیدا کردن قرارداد فعلی resident
        $currentContract = Contract::with(['resident', 'bed.room'])
            ->where('resident_id', $residentId)
            ->first();

        if (!$currentContract) {
            throw new \Exception('هیچ قرارداد فعالی برای این resident پیدا نشد');
        }

        $currentBedId = $currentContract->bed_id;

        // اگر تخت مقصد همان تخت فعلی باشد، کاری انجام نمی‌دهیم
        if ($currentBedId == $this->selectBed) {
            return;
        }

        // پیدا کردن قرارداد فعال تخت مقصد (اگر وجود داشته باشد)
        $targetContract = Contract::with(['resident', 'bed.room'])
            ->where('bed_id', $this->selectBed)->first();


        // شروع transaction برای اطمینان از یکپارچگی داده‌ها
        $selectBed = $this->selectBed;
        DB::transaction(function () use ($currentContract, $targetContract, $currentBedId, $selectBed) {


            // اگر تخت مقصد resident دارد، جابجایی انجام می‌دهیم
            if ($targetContract) {
                // جابجایی ID تخت‌ها در قراردادها
                $currentContract->update(['bed_id' => $this->selectBed]);
                $targetContract->update(['bed_id' => $currentBedId]);
            } else {
                // اگر تخت مقصد خالی است، فقط تخت resident اول را تغییر می‌دهیم
                $currentContract->update(['bed_id' => $this->selectBed]);
            }

            // به‌روزرسانی state_ratio_resident در تخت‌ها (اگر نیاز باشد)
            $currentBed = Bed::find($currentBedId);
            $targetBed = Bed::find($this->selectBed);

            if ($targetContract) {
                // در صورت جابجایی، state_ratio_resident را نیز جابجا می‌کنیم
                $tempStateRatio = $currentBed->state_ratio_resident;
                $currentBed->update(['state_ratio_resident' => $targetBed->state_ratio_resident]);
                $targetBed->update(['state_ratio_resident' => $tempStateRatio]);

            } else {
                // اگر تخت مقصد خالی بود، state_ratio_resident تخت قبلی را خالی می‌کنیم
                $targetBed->update(['state_ratio_resident' => $currentBed->state_ratio_resident]);
                $currentBed->update(['state_ratio_resident' => 'empty']);
            }

            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'موفق!',
                'description' => $currentContract->resident->full_name . "به اتاق " . $targetBed->name . "جا به جا شد",
                'timer' => 3000
            ]);
        });


    }

    public function endedContract($idResident): void
    {
        $name = $this->resident->full_name;
        $this->js("
            Swal.fire({
                title: 'اتمام قرارداد',
                text: 'آیا از اتمام قرارداد {$name}  مطمئن هستید؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله، قرارداد را تمام کن',
                cancelButtonText: 'انصراف',
                customClass: {
                    popup: 'swal-z-index'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                \$wire.call('endedContractConfirmed', " . $idResident . ");
                }
            });
        ");
    }

    public function endedContractConfirmed($idResident): void
    {
        DB::transaction(function () use ($idResident) {
            try {
                // 1. پیدا کردن اقامتگر
                $resident = Resident::findOrFail($idResident);

                // 2. حذف نرم یادداشت‌های مرتبط
                $resident->notes()->delete();

                // 3. غیرفعال کردن قرارداد مرتبط
                if ($resident->contract) {
                    $contract = $resident->contract;
                    //تغییر وضعیت قرار داد به خروج
                    $contract->update(['state' => 'exit']);

                    // 4. آزاد کردن تخت
                    if ($contract->bed) {
                        $contract->bed()->update([
                            'state_ratio_resident' => 'empty',
                        ]);
                    }

                    // 5. حذف نرم قرارداد
                    $contract->delete();
                }

                // 6. حذف نرم اقامتگر
                $resident->delete();


                $this->closeModal();

                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'موفقیت',
                    'description' => $resident->full_name . ' و اطلاعات مرتبط با موفقیت غیرفعال شدند',
                    'timer' => 3000
                ]);
                $this->dispatch('residentDataUpdated');


            } catch (\Exception $e) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'خطا',
                    'description' => 'خطا در غیرفعال کردن: ' . $e->getMessage(),
                    'timer' => 4000
                ]);
            }
        });
    }


    public function deleteResident($idResident): void
    {
        $name = $this->resident->full_name;
        $this->js("
            Swal.fire({
                title: 'حذف اقامتگر',
                text: 'آیا از حذف {$name}  مطمئن هستید؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'بله، اقامتگر رو حذف کن',
                cancelButtonText: 'انصراف',
                customClass: {
                    popup: 'swal-z-index'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                \$wire.call('deleteResidentConfirmed', " . $idResident . ");
                }
            });
        ");
    }

    public function deleteResidentConfirmed($idResident): void
    {
        DB::transaction(function () use ($idResident) {
            try {
                // 1. پیدا کردن اقامتگر
                $resident = Resident::findOrFail($idResident);
                $residentName = $resident->full_name;

                // 2. حذف فیزیکی یادداشت‌های مرتبط
                $resident->notes()->forceDelete();

                // 3. حذف قرارداد مرتبط
                if ($resident->contract) {
                    $contract = $resident->contract;

                    // 4. آزاد کردن تخت
                    if ($contract->bed) {
                        $contract->bed()->update([
                            'state_ratio_resident' => 'empty',
                        ]);
                    }

                    // 5. حذف فیزیکی قرارداد
                    $contract->forceDelete();
                }

                // 6. حذف فیزیکی اقامتگر
                $resident->forceDelete();

                $this->closeModal();

                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'موفقیت',
                    'description' => $residentName . ' و اطلاعات مرتبط با موفقیت حذف شدند',
                    'timer' => 3000
                ]);
                $this->dispatch('residentDataUpdated');

            } catch (\Exception $e) {
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'خطا',
                    'description' => 'خطا در حذف: ' . $e->getMessage(),
                    'timer' => 4000
                ]);
            }
        });
    }

    public function render()
    {
        return view('livewire.modals.details-changes-modal');
    }


}
