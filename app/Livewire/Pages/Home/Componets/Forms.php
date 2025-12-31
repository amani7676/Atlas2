<?php

namespace App\Livewire\Pages\Home\Componets;

use App\Models\Resident;
use App\Services\Report\AllReportService;
use Livewire\Component;
use Livewire\Attributes\On;

class Forms extends Component
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
    // public function getAllReportServiceInstance(): AllReportService
    // {
    //     // از Null Coalescing استفاده می‌کنیم تا مطمئن شویم که سرویس همیشه مقداردهی شده است.
    //     // این حالت فقط برای رندر اولیه کاربرد دارد، در Livewire mount همیشه اجرا می‌شود.
    //     return $this->allReportService ??= app(AllReportService::class);
    // }
    public function giveForm(Resident $resident)
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

        $resident->form = true;
        $resident->save();

        // !!! تغییر 'message' به 'description' برای هماهنگی با cute-alert
        $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
            'type' => 'success',
            'title' => 'موفقیت!',
            'description' => 'فرم گرفته شد',
            'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
        ]);
    }

    public function render()
    {
        $this->allReportService ??= app(AllReportService::class);

        return view('livewire.pages.home.componets.forms');
    }
}
