<?php

namespace App\Livewire\Pages\Reports;

use App\Models\Bed;
use App\Models\Contract;
use App\Models\Resident;
use App\Models\Unit;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ListCurrentResident extends Component
{
    public $units = [];
    public $rooms = [];
    public $beds = [];
    public $contracts = [];

    #[Rule('required|string|max:255')]
    public $resident_name = '';

    #[Rule('required|string|max:11')]
    public $resident_phone = '';

    #[Rule('required|date')]
    public $contract_date = '';

    public $selected_bed_id = null;
    public $editing_bed_id = null;

    public function mount()
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        // بارگذاری واحدها با اتاق‌هایی که type آنها room است
        $this->units = Unit::with([
            'rooms' => function ($query) {
                $query->where('type', 'room');
            },
            'rooms.beds.contracts.resident'
        ])->get();

        // تنظیم اتاق‌ها برای نمایش در جداول
        $this->rooms = collect();
        foreach ($this->units as $unit) {
            foreach ($unit->rooms as $room) {
                $this->rooms->push($room);
            }
        }

        // گروه‌بندی اتاق‌ها بر اساس طبقه
        $this->rooms = $this->rooms->groupBy(function ($room) {
            return substr($room->name, 0, 1); // اولین رقم شماره اتاق (طبقه)
        });
    }





    public function resetForm()
    {
        $this->resident_name = '';
        $this->resident_phone = '';
        $this->contract_date = '';
        $this->selected_bed_id = null;
        $this->editing_bed_id = null;
    }

    public function getBedInfo($bed): array
    {
        $Contract = $bed->contracts()->first();

        if ($Contract && $Contract->resident) {
            return [
                'name' => $Contract->resident->full_name,
                'date' => $Contract->getPaymentDateJalaliAttribute(),
                'occupied' => true
            ];
        }

        return [
            'name' => '',
            'date' => '',
            'occupied' => false
        ];
    }

    public function render()
    {
        return view('livewire.pages.reports.list-current-resident')
            ->title('اقای عنایتی');
    }
}
