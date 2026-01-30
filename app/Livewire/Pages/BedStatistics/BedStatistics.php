<?php

namespace App\Livewire\Pages\BedStatistics;

use App\Services\Report\StatisticsService;
use Livewire\Component;

class BedStatistics extends Component
{
    public array $statistics = [];
    public bool $loading = true;

    protected $statisticsService;

    public function boot(StatisticsService $statisticsService): void
    {
        $this->statisticsService = $statisticsService;
    }

    public function mount(): void
    {
        $this->loadStatistics();
    }

    public function loadStatistics(): void
    {
        $this->loading = true;

        try {
            $this->statistics = $this->statisticsService->getAllStatistics();
        } catch (\Exception $e) {
            session()->flash('error', 'خطا در بارگیری آمار: ' . $e->getMessage());
            $this->statistics = [];
        } finally {
            $this->loading = false;
        }
    }

    public function refresh(): void
    {
        $this->loadStatistics();
        $this->dispatch('show-toast', [ // !!! تغییر به 'show-toast'
            'type' => 'success',
            'title' => 'به روز رسانی!',
            'description' => "اطلاعات به روز رسانی شد",
            'timer' => 3000 // Toast پس از 3 ثانیه ناپدید می‌شود
        ]);
    }

    public function getColorClass($index): string
    {
        $colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary'];
        return $colors[$index % count($colors)];
    }

    public function render()
    {
        return view('livewire.pages.bed-statistics.bed-statistics')
            ->title('آمار تخت‌ها');
    }
}
