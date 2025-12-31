<?php

namespace App\Livewire\Pages\Home;

use App\Repositories\NoteRepository;
use App\Services\Core\StatusService;
use App\Models\Rezerve;
use App\Repositories\BedRepository;
use App\Repositories\RezerveRepository;
use App\Services\Report\AllReportService;
use Livewire\Component;
use Livewire\Attributes\On;

class Home extends Component
{

    // ویژگی‌ها را به صورت nullable تعریف کنید
    protected ?AllReportService $allReportService = null;
    protected ?RezerveRepository $rezerveRepository = null;
    protected ?BedRepository $bedRepository = null;
    protected ?StatusService $statusService = null;
    protected ?NoteRepository $noteRepository = null;
    
    protected function getAllReportService(): AllReportService
    {
        return $this->allReportService ??= app(AllReportService::class);
    }
    
    protected function getRezerveRepository(): RezerveRepository
    {
        return $this->rezerveRepository ??= app(RezerveRepository::class);
    }
    
    protected function getBedRepository(): BedRepository
    {
        return $this->bedRepository ??= app(BedRepository::class);
    }
    
    protected function getStatusService(): StatusService
    {
        return $this->statusService ??= app(StatusService::class);
    }
    
    protected function getNoteRepository(): NoteRepository
    {
        return $this->noteRepository ??= app(NoteRepository::class);
    }


    #[On('delete-note')]
    public function handleDeleteNote($noteId): void
    {
        // Handle both direct parameter and array parameter
        if (is_array($noteId)) {
            $noteId = $noteId['noteId'] ?? $noteId[0] ?? null;
        }
        
        if (!$noteId) {
            return;
        }
        
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
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف یادداشت: ' . $e->getMessage(),
                'timer' => 4000
            ]);
        }
    }

    public function mount(
        AllReportService $occupancyReportService,
        RezerveRepository $rezerveRepository,
        BedRepository $bed_repository,
        StatusService $statusService ,
        NoteRepository $noteRepository,
    ): void {
        $this->allReportService = $occupancyReportService;
        $this->rezerveRepository = $rezerveRepository;
        $this->bedRepository = $bed_repository;
        $this->statusService = $statusService; // اضافه شد
        $this->noteRepository = $noteRepository;

    }

    // متدی که هنگام دریافت رویداد show-alert فراخوانی می‌شود

    public function render()
    {
        return view('livewire.pages.home.home', [
            'allReportService' => $this->getAllReportService(),
            'rezerves' => $this->getRezerveRepository(),
            'beds' => $this->getBedRepository(),
            'statusService' => $this->getStatusService(), // اضافه شد برای استفاده در view
            'noteRepository' => $this->getNoteRepository()
        ]);
    }
}
