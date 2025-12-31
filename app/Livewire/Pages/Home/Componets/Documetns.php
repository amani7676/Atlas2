<?php

namespace App\Livewire\Pages\Home\Componets;

use App\Models\Resident;
use App\Services\Report\AllReportService;
use Livewire\Component;

class Documetns extends Component
{
    protected AllReportService $allReportService;
    public $isExpanded = false;

    public function mount(AllReportService $allReportService)
    {
        $this->allReportService = $allReportService;
    }

    public function toggle()
    {
        $this->isExpanded = !$this->isExpanded;
    }
    // public function getAllReportService(): AllReportService
    // {
    //     // اگر allReportService هنوز مقداردهی نشده، آن را از سرویس کانتینر لاراول دریافت کن
    //     return $this->allReportService ??= app(AllReportService::class);
    // }

    public function giveDocumented(Resident $resident)
    {
        if (!$resident) {
            // پیام خطا را مستقیماً با dispatch ارسال کنید
             // !!! تغییر 'message' به 'description' برای هماهنگی با cute-alert
             $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'اقامتگری وجود ندارد!',
                'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
            ]);
            return;
        }

        $resident->document = true;
        $resident->save();

        // !!! تغییر 'message' به 'description' برای هماهنگی با cute-alert
         $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
            'type' => 'success',
            'title' => 'موفقیت!',
            'description' => 'مدرک گرفته شد',
            'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
        ]);
    }


    public function render()
    {
        $this->allReportService ??= app(AllReportService::class);

        return view('livewire.pages.home.componets.documetns');
    }
}
