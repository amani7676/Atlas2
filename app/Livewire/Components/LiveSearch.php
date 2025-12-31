<?php

namespace App\Livewire\Components;

use App\Services\Report\AllReportService;
use Livewire\Component;
use App\Models\Resident;
use Illuminate\Database\Eloquent\Collection;

class LiveSearch extends Component
{
    public string $search = '';
    public Collection $searchResults;
    public bool $showResults = false;
    public bool $isLoading = false;
    public int $selectedIndex = -1; // برای navigation با کیبورد

    protected $listeners = [
        'hideSearchResults' => 'hideResults'
    ];

    public function mount()
    {
        $this->searchResults = new Collection();

    }

    public function updatedSearch()
    {
        $this->selectedIndex = -1; // reset selection

        if (strlen($this->search) >= 2) {
            $this->isLoading = true;

            $this->searchResults = Resident::with(['contract.bed.room.unit'])
                ->where(function($query) {
                    $query->where('full_name', 'like', '%' . $this->search . '%')
                          ->orWhere('phone', 'like', '%' . $this->search . '%');
                })
                ->limit(10)
                ->get();

            $this->showResults = true;
            $this->isLoading = false;
        } else {
            $this->searchResults = new Collection();
            $this->showResults = false;
            $this->isLoading = false;
        }
    }

    public function selectResult($residentId)
    {
        $resident = Resident::find($residentId);
        if ($resident) {
            $this->search = $resident->full_name;
            $this->hideResults();

            // انتشار event برای استفاده در سایر کامپوننت‌ها
            $this->dispatch('resident-selected', [
                'id' => $residentId,
                'name' => $resident->full_name,
                'phone' => $resident->phone
            ]);

            // اختیاری: هدایت به صفحه جزئیات
            // return redirect()->route('resident.show', $residentId);
        }
    }

    public function clearSearch()
    {
        $this->search = '';
        $this->searchResults = new Collection();
        $this->showResults = false;
        $this->isLoading = false;
        $this->selectedIndex = -1;
    }

    public function hideResults()
    {
        $this->showResults = false;
        $this->selectedIndex = -1;
    }



    public function highlightSearch($text)
    {
        if (strlen($this->search) >= 2) {
            $highlighted = str_ireplace(
                $this->search,
                '<mark style="background: #fff3cd; padding: 1px 2px; border-radius: 2px;">' . $this->search . '</mark>',
                $text
            );
            return $highlighted;
        }

        return $text;
    }

    public function render()
    {
        return view('livewire.components.live-search');
    }
}
