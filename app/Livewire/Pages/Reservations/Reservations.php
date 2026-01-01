<?php

namespace App\Livewire\Pages\Reservations;

use App\Models\Rezerve;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Reservations extends Component
{
    // Form Properties
    #[Validate('required|string|max:255')]
    public string $full_name = '';

    #[Validate('required|string|min:11|max:13')]
    public string $phone = '';

    public $bed_id = null;

    #[Validate('nullable|string|max:1000')]
    public string $note = '';

    #[Validate('required|in:low,medium,high')]
    public string $priority = 'medium';

    // Component State
    public $editingId = null;
    public $reserves = [];
    public $showForm = false;

    // Search and Filter
    public $search = '';
    public $filterPriority = '';

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->loadReserves();
    }

    public function loadReserves(): void
    {
        $query = Rezerve::with('bed.room');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterPriority) {
            $query->where('priority', $this->filterPriority);
        }

// مرتب‌سازی بر اساس اولویت دلخواه
        $query->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('created_at', 'asc');

        $this->reserves = $query->get();

    }

    public function updatedSearch(): void
    {
        $this->loadReserves();
    }

    public function updatedFilterPriority(): void
    {
        $this->loadReserves();
    }

    public function showCreateForm(): void
    {
        $this->showForm = true;
        $this->resetForm();
    }

    public function hideForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->reset([
            'full_name',
            'phone',
            'note',
            'priority',
            'editingId'
        ]);
        $this->priority = 'medium';
        $this->bed_id = null;
    }

    public function updatedPhone($value): void
    {
        // پاکسازی شماره تلفن (حذف کاراکترهای غیر عددی)
        $cleanPhone = preg_replace('/\D/', '', $value);
        
        // اگر شماره با 09 شروع نشود، اضافه کردن 0
        if (strlen($cleanPhone) > 0 && !str_starts_with($cleanPhone, '0')) {
            $cleanPhone = '0' . $cleanPhone;
        }
        
        // محدود کردن به 11 رقم
        $cleanPhone = substr($cleanPhone, 0, 11);
        
        // فرمت کردن شماره تلفن برای نمایش
        if (strlen($cleanPhone) == 11) {
            $this->phone = substr($cleanPhone, 0, 4) . '-' . substr($cleanPhone, 4, 3) . '-' . substr($cleanPhone, 7, 4);
        } else {
            $this->phone = $cleanPhone;
        }
        
        // اعتبارسنجی
        $this->validatePhoneNumber();
    }

    public function validatePhoneNumber(): bool
    {
        $cleanPhone = preg_replace('/\D/', '', $this->phone);
        
        $this->resetErrorBag('phone');
        
        if (strlen($cleanPhone) != 11) {
            $this->addError('phone', 'شماره تلفن باید دقیقاً 11 رقم باشد (مثال: 09123456789)');
            return false;
        }
        
        if (!str_starts_with($cleanPhone, '0')) {
            $this->addError('phone', 'شماره تلفن باید با 0 شروع شود');
            return false;
        }
        
        if (substr($cleanPhone, 1, 1) != '9') {
            $this->addError('phone', 'شماره تلفن باید با 09 شروع شود');
            return false;
        }
        
        return true;
    }

    private function sanitizePhoneNumberForDatabase($phoneNumber): string
    {
        return preg_replace('/\D/', '', $phoneNumber);
    }

    public function save(): void
    {
        // اعتبارسنجی شماره تلفن
        if (!$this->validatePhoneNumber()) {
            return;
        }

        $this->validate();

        try {
            if ($this->editingId) {
                // Update existing reserve
                $reserve = Rezerve::findOrFail($this->editingId);
                $reserve->update([
                    'full_name' => $this->full_name,
                    'phone' => $this->sanitizePhoneNumberForDatabase($this->phone),
                    'note' => $this->note,
                    'priority' => $this->priority,
                ]);

                $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
                    'type' => 'info',
                    'title' => 'Updated Reservation!',
                    'description' => 'رزرو اپدیت شد',
                    'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
                ]);
            } else {
                // Create new reserve
                Rezerve::create([
                    'full_name' => $this->full_name,
                    'phone' => $this->sanitizePhoneNumberForDatabase($this->phone),
                    'note' => $this->note,
                    'priority' => $this->priority,
                ]);

                $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
                    'type' => 'sucess',
                    'title' => 'Created Reservation!',
                    'description' => 'رزرو ثبت شدخطا در ثبت اطلاعات: ',
                    'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
                ]);
            }

            $this->hideForm();
            $this->loadReserves();

        } catch (\Exception $e) {
            session()->flash('error', 'خطا در ذخیره اطلاعات: ' . $e->getMessage());
        }
    }

    public function edit($reserveId): void
    {
        $reserve = Rezerve::with('bed')->findOrFail($reserveId);

        $this->editingId = $reserve->id;
        $this->full_name = $reserve->full_name;
        // فرمت کردن شماره تلفن برای نمایش
        $this->phone = $this->formatPhoneNumberForDisplay($reserve->phone);
        $this->note = $reserve->note;
        $this->priority = $reserve->priority;

        $this->showForm = true;
    }

    private function formatPhoneNumberForDisplay($phoneNumber): string
    {
        $cleanPhone = preg_replace('/\D/', '', $phoneNumber);
        
        if (strlen($cleanPhone) == 11 && substr($cleanPhone, 0, 1) == '0') {
            return substr($cleanPhone, 0, 4) . '-' . substr($cleanPhone, 4, 3) . '-' . substr($cleanPhone, 7, 4);
        }
        
        return $phoneNumber;
    }

    public function delete($reserveId): void
    {
        try {
            Rezerve::findOrFail($reserveId)->delete();
            $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
                'type' => 'error',
                'title' => 'Deleted Reservation!',
                'description' => 'رزرو شده حذف شد',
                'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
            ]);
            $this->loadReserves();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
                'type' => 'error',
                'title' => 'ناموفق!',
                'description' => 'خطا در ثبت اطلاعات: ',
                'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
            ]);
        }
    }

    public function getPriorityLabel($priority): string
    {
        return match ($priority) {
            'low' => 'کم',
            'medium' => 'متوسط',
            'high' => 'بالا',
            default => 'نامشخص'
        };
    }

    public function getPriorityClass($priority): string
    {
        return match ($priority) {
            'low' => 'badge bg-secondary',
            'medium' => 'badge bg-warning',
            'high' => 'badge bg-danger',
            default => 'badge bg-light'
        };
    }

    public function render()
    {
        return view('livewire.pages.reservations.reservations')
            ->title('رزروی ها');
    }
}
