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
    public ?string $highlightBed = null;
    public ?string $highlightRoom = null;
    protected $listeners = [
        'residentAdded' => 'refreshResidentData',
        'residentDataUpdated' => 'refreshResidentData'  // اضافه شده
    ];

    public function mount()
    {
        // Get hash fragment from URL for room highlighting
        $urlHash = request()->server('HTTP_REFERER') ?? '';
        if (strpos($urlHash, '#') !== false) {
            $hashPart = substr($urlHash, strpos($urlHash, '#') + 1);
            $this->highlightRoom = $hashPart;
        }
        
        // Also check current URL hash (for direct access)
        $currentUrl = request()->fullUrl();
        if (strpos($currentUrl, '#') !== false) {
            $hashPart = substr($currentUrl, strpos($currentUrl, '#') + 1);
            $this->highlightRoom = $hashPart;
        }
        
        // Don't load data on mount - use lazy loading instead
    }

    // متد جداگانه برای لود کردن داده‌های residents
    public function loadResidentData(): void
    {
        // تمام واحدها را با وابستگی‌هایشان (ساکنین و قراردادها) دریافت می‌کنیم
        $allUnitsData = $this->allReportService()->getUnitWithDependence();

        // ابتدا آرایه‌ها را خالی کنید
        $this->full_name = [];
        $this->phone = [];
        $this->payment_date = [];

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
                        $this->phone[$resident['id']] = $resident['phone'] ?? '';
                        $this->payment_date[$resident['id']] = $contract['payment_date'] ?? '';
                    }
                }
            }
        }
    }

    // متد برای اطمینان از وجود کلید در آرایه‌های Livewire
    public function ensureResidentDataExists($residentId): void
    {
        if (!isset($this->full_name[$residentId])) {
            $this->full_name[$residentId] = '';
        }
        if (!isset($this->phone[$residentId])) {
            $this->phone[$residentId] = '';
        }
        if (!isset($this->payment_date[$residentId])) {
            $this->payment_date[$residentId] = '';
        }
    }

    // متد برای گرفتن مقدار امن از آرایه‌ها
    private function getSafeArrayValue(array $array, string $key, $default = '')
    {
        return $array[$key] ?? $default;
    }

    // متد برای فرمت کردن شماره تلفن برای نمایش (اضافه کردن خط فاصله)
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

    // متد جدید برای هندل کردن تغییرات شماره تلفن در real-time با debouncing
    public function updatedPhone($value, $key): void
    {
        // فرمت کردن شماره تلفن هنگام تایپ
        $this->phone[$key] = $this->formatPhoneNumberForDisplay($value);
        // حذف ولیدیشن real-time برای بهبود performance
        // $this->validatePhoneNumber($key);
    }
    
    // متد برای ذخیره سریع با debouncing
    public function debouncedSave($residentId): void
    {
        // فقط در پروداکشن از debouncing استفاده کن
        if (app()->environment('production')) {
            // ذخیره با تاخیر برای کاهش بار سرور
            sleep(0.1); // 100ms delay
        }
        
        $this->editResidentInline($residentId);
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
        // Clear cache to ensure fresh data
        \App\Services\Report\AllReportService::clearAllCache();
        
        // Force reload of all data
        $this->loadResidentData();
        
        // Dispatch a refresh event to update the UI
        $this->dispatch('dataRefreshed');
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
            // بروزرسانی اطلاعات ساکن
            $resident = \App\Models\Resident::find($residentId);
            if ($resident) {
                // استفاده از transaction برای سرعت بیشتر در پروداکشن
                if (app()->environment('production')) {
                    \DB::transaction(function () use ($resident, $residentId) {
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
                    });
                } else {
                    // در محیط توسعه بدون transaction
                    $resident->update([
                        'full_name' => $this->full_name[$residentId] ?? $resident->full_name,
                        'phone' => $this->sanitizePhoneNumberForDatabase($this->phone[$residentId] ?? $resident->phone),
                    ]);

                    $contract = $resident->contract()->latest()->first();

                    if ($contract && isset($this->payment_date[$residentId])) {
                        $contract->update([
                            'payment_date' => $this->toMiladi($this->payment_date[$residentId])
                        ]);
                    }
                }

                // بعد از آپدیت، شماره تلفن را دوباره فرمت می‌کنیم
                $this->phone[$residentId] = $this->formatPhoneNumberForDisplay($this->phone[$residentId]);

                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'موفقیت!',
                    'description' => "مشخصات " . ($resident->full_name ?? 'کاربر') . " به روز شد",
                    'timer' => 2000 // کاهش تایمر برای سرعت بیشتر
                ]);
                
                // Only clear specific cache instead of all cache
                $this->clearSpecificResidentCache($residentId);
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'مشکل!',
                'description' => 'خطا در انجام آپدیت خطی: ' . $e->getMessage(),
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
        // بدون نیاز به لود مجدد داده‌ها
        $this->dispatch('openEditResidentModal', $residentId);
    }

    public function detailsChange($residentId): void
    {
        // ارسال رویداد به کامپوننت مودال برای تغییر جزئیات
        // بدون نیاز به لود مجدد داده‌ها
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
            
            // پاک کردن cache
            \App\Services\Report\AllReportService::clearResidentsCache();
            
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

    // متد جدید برای پاک کردن cache خاص یک resident به جای کل cache
    private function clearSpecificResidentCache($residentId): void
    {
        try {
            // فقط cache مربوط به این resident را پاک می‌کنیم
            \Cache::forget('resident_' . $residentId);
            // به جای پاک کردن کل cache، فقط cache units را آپدیت می‌کنیم
            \Cache::forget('units_with_dependence');
            \Cache::forget('units_with_dependence_v2');
            \Cache::forget('units_with_dependence_v3');
            \Cache::forget('units_with_dependence_v4');
            
            // Clear user-specific caches
            $userId = auth()->id() ?? 'anonymous';
            \Cache::forget('units_with_dependence_user_' . $userId);
            \Cache::forget('units_with_dependence_v2_user_' . $userId);
            \Cache::forget('units_with_dependence_v3_user_' . $userId);
            \Cache::forget('units_with_dependence_v4_user_' . $userId);
        } catch (\Exception $e) {
            // در صورت خطا، فقط cache مربوط به units را پاک می‌کنیم
            \Cache::forget('units_with_dependence');
            \Cache::forget('units_with_dependence_v2');
            \Cache::forget('units_with_dependence_v3');
            \Cache::forget('units_with_dependence_v4');
        }
    }

    public function render()
    {
        // Load data only when needed for better performance
        // Use lazy loading - don't load all data at once
        $this->loadResidentData();
        
        return view('livewire.pages.tablelists.tablelists', [
            'allReportService' => $this->service(AllReportService::class),
            'statusService' => $this->service(StatusService::class),
            'bedRepository' => $this->repository(BedRepository::class),
        ])->title('لیست اقامتگران');
    }
    
    // Add lazy loading method for better performance
    public function getUnitsProperty()
    {
        return $this->cache(function () {
            return $this->allReportService()->getUnitWithDependence();
        }, 'units_data_' . auth()->id());
    }
}
