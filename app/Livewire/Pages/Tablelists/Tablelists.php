<?php

namespace App\Livewire\Pages\Tablelists;

use App\Repositories\BedRepository;
use App\Services\Core\StatusService;
use App\Services\Report\AllReportService;
use App\Traits\HasDateConversion;
use Livewire\Attributes\Title;
use Livewire\Attributes\On;
use Livewire\Component;

#[Title('مدیریت ساکنین - لیست جداول')]
class Tablelists extends Component
{

    use HasDateConversion;

    // خصوصیات برای ذخیره داده های فرم
    public array $editingResidents = [];
    public array $full_name = [];
    public array $phone = [];
    public array $payment_date = [];
    protected $listeners = [
        'residentAdded' => 'refreshResidentData',
        'residentDataUpdated' => 'refreshResidentData'  // اضافه شده
    ];

    public function mount()
    {
        $this->loadResidentData();
    }

    // متد جداگانه برای لود کردن داده‌های residents
    private function loadResidentData(): void
    {
        // ابتدا آرایه‌ها را خالی کنید
        $this->full_name = [];
        $this->phone = [];
        $this->payment_date = [];

        // تمام واحدها را با وابستگی‌هایشان (ساکنین و قراردادها) دریافت می‌کنیم
        $allUnitsData = $this->allReportService()->getUnitWithDependence();

        foreach ($allUnitsData as $unitData) {
            foreach ($unitData['rooms'] as $roomData) {
                foreach ($roomData['beds'] as $bed) {
                    // فقط برای تخت‌هایی که قرارداد فعال دارند
                    if ($bed['contracts']->first()) {
                        $contractData = $bed['contracts']->first();
                        $resident = $contractData['resident'];
                        $contract = $contractData['contract'];

                        // خصوصیات Livewire را با داده‌های موجود مقداردهی اولیه می‌کنیم
                        $this->full_name[$resident['id']] = $resident['full_name'] ?? '';
                        // اینجا شماره تلفن را برای نمایش با خط فاصله فرمت می‌کنیم
                        $this->phone[$resident['id']] = $this->formatPhoneNumberForDisplay($resident['phone'] ?? '');
                        $this->payment_date[$resident['id']] = $contract['payment_date'] ?? '';
                    }
                }
            }
        }
    }

    // متد جدید برای فرمت کردن شماره تلفن برای نمایش (اضافه کردن خط فاصله)
    private function formatPhoneNumberForDisplay($phoneNumber): string
    {
        // ابتدا شماره را پاکسازی می‌کنیم (حذف تمام کاراکترهای غیر عددی)
        $cleanPhone = preg_replace('/\D/', '', $phoneNumber);

        // اگر شماره 11 رقمی باشد و با 0 شروع شود
        if (strlen($cleanPhone) == 11 && substr($cleanPhone, 0, 1) == '0') {
            return substr($cleanPhone, 0, 4) . '-' . substr($cleanPhone, 4, 3) . '-' . substr($cleanPhone, 7, 4);
        }

        // اگر فرمت استاندارد نباشد، همان شماره اصلی را برگردان
        return $phoneNumber;
    }

    // متد برای پاکسازی شماره تلفن قبل از ذخیره در دیتابیس (حذف خط فاصله)
    private function sanitizePhoneNumberForDatabase($phoneNumber): array|string|null
    {
        return preg_replace('/\D/', '', $phoneNumber); // حذف تمام کاراکترهای غیر عددی
    }

    // متد جدید برای هندل کردن تغییرات شماره تلفن در real-time
    public function updatedPhone($value, $key): void
    {
        // فرمت کردن شماره تلفن هنگام تایپ
        $this->phone[$key] = $this->formatPhoneNumberForDisplay($value);
        // ولیدیشن شماره تلفن
        $this->validatePhoneNumber($key);
    }

    // متد ولیدیشن شماره تلفن
    private function validatePhoneNumber($residentId): bool
    {
        $phoneNumber = $this->phone[$residentId] ?? '';
        $cleanPhone = preg_replace('/\D/', '', $phoneNumber);

        // پاک کردن خطاهای قبلی
        $this->resetErrorBag("phone.{$residentId}");

        // ولیدیشن: شماره باید دقیقا 11 رقم باشد
        if (strlen($cleanPhone) != 11) {
            $this->addError("phone.{$residentId}", 'شماره تلفن باید دقیقا 11 رقم باشد');
            return false;
        }

        // ولیدیشن: شماره باید با 0 شروع شود
        if (substr($cleanPhone, 0, 1) != '0') {
            $this->addError("phone.{$residentId}", 'شماره تلفن باید با 0 شروع شود');
            return false;
        }

        // ولیدیشن: رقم دوم باید 9 باشد (شماره موبایل)
        if (substr($cleanPhone, 1, 1) != '9') {
            $this->addError("phone.{$residentId}", 'شماره تلفن وارد شده معتبر نمی‌باشد');
            return false;
        }

        return true;
    }

    // متد جدید که بعد از اضافه شدن resident فراخوانی می‌شود
    #[On('residentDataUpdated')]  // اضافه شده
    public function refreshResidentData(): void
    {
        // داده‌های residents را مجدداً لود کنید
        $this->loadResidentData();
    }

    // 🔧 متد عمومی برای سرویس‌ها
    protected function service(string $class)
    {
        return app($class);
    }

    // 🔧 متد عمومی برای ریپازیتوری‌ها
    protected function repository(string $class)
    {
        return app(BedRepository::class); // اطمینان حاصل کنید که BedRepository استفاده شود
    }

    protected function allReportService(): AllReportService
    {
        return app(AllReportService::class);
    }

    protected function statusService(): StatusService
    {
        return app(StatusService::class);
    }

    public function getColorClass($vahedId): string
    {
        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        return $colors[$vahedId % count($colors)]; // برای جلوگیری از خطای "Offset out of bounds"
    }

    public function editResidentInline($residentId): void
    {
        try {
            // ولیدیشن قبل از ذخیره
//            if (!$this->validatePhoneNumber($residentId)) {
//                $this->dispatch('show-toast', [
//                    'type' => 'error',
//                    'title' => 'خطا!',
//                    'description' => 'لطفا شماره تلفن را به درستی وارد کنید',
//                    'timer' => 4000
//                ]);
//                return;
//            }
            // بروزرسانی اطلاعات ساکن
            $resident = \App\Models\Resident::find($residentId);
            if ($resident) {
                $resident->update([
                    'full_name' => $this->full_name[$residentId] ?? $resident->full_name,
                    // اینجا شماره تلفن را قبل از ذخیره در دیتابیس پاکسازی می‌کنیم
                    'phone' => $this->sanitizePhoneNumberForDatabase($this->phone[$residentId] ?? $resident->phone),
                ]);

                // بروزرسانی تاریخ پرداخت در قرارداد
                $contract = $resident->contract()->latest()->first();

                if ($contract && isset($this->payment_date[$residentId])) {
                    $contract->update([
                        'payment_date' => $this->toMiladi($this->payment_date[$residentId])
                    ]);
                }

                // بعد از آپدیت، شماره تلفن را دوباره فرمت می‌کنیم
                $this->phone[$residentId] = $this->formatPhoneNumberForDisplay($this->phone[$residentId]);

                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'موفقیت!',
                    'description' => "مشخصات " . ($resident->full_name ?? 'کاربر') . " به روز شد",
                    'timer' => 3000
                ]);
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'مشکل!',
                'description' => 'خطا در انجام آپدیت خطی: ' . $e->getMessage(), // نمایش پیام خطا برای دیباگ
                'timer' => 3000
            ]);
        }
    }

    public function openAddModal($bedName, $roomName): void
    {
        // ارسال رویداد به کامپوننت مودال
        $this->dispatch('openAddResidentModal', $bedName, $roomName);
    }

    public function editResident($residentId): void
    {
        // ارسال رویداد به کامپوننت مودال برای ویرایش
        $this->dispatch('openEditResidentModal', $residentId);
    }

    public function detailsChange($residentId): void
    {
        // ارسال رویداد به کامپوننت مودال برای تغییر جزئیات
        $this->dispatch('openDetailsChangeModal', $residentId);
    }
    #[On('update_notes')]
    public function updateNotes()
    {
        $this->loadResidentData();
    }

    public function deleteNote($noteId): void
    {
        try {
            \App\Models\Note::where('id', $noteId)->delete();
            
            $this->dispatch('show-toast', [
                'type' => 'success',
                'title' => 'موفقیت!',
                'description' => 'یادداشت با موفقیت حذف شد',
                'timer' => 3000
            ]);
            
            $this->loadResidentData();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف یادداشت: ' . $e->getMessage(),
                'timer' => 4000
            ]);
        }
    }

    public function render()
    {
        return view('livewire.pages.tablelists.tablelists', [
            'allReportService' => $this->service(AllReportService::class),
            'statusService' => $this->service(StatusService::class),
            'bedRepository' => $this->repository(BedRepository::class),
        ])->title('لیست اقامتگران');
    }
}
