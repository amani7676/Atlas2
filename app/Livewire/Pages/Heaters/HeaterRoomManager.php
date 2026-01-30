<?php

namespace App\Livewire\Pages\Heaters;

use App\Models\Heater;
use App\Models\Room;
use App\Models\Unit;
use Livewire\Component;
use Livewire\WithPagination;

class HeaterRoomManager extends Component
{
    use WithPagination;

    // Form properties for Heater
    public $heaterName = '';
    public $heaterNumber = '';
    public $heaterDesc = '';
    public $heaterStatus = 'active';
    public $heaterModel = '';
    public $heaterSerialNumber = '';
    public $heaterInstallationDate = '';
    public $heaterRoomId = null;

    // Modal states
    public $showHeaterModal = false;
    public $editingHeaterId = null;

    // Search and filter
    public $searchHeater = '';
    public $filterUnit = '';
    public $filterStatus = '';

    protected $queryString = [
        'searchHeater' => ['except' => ''],
        'filterUnit' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    // Heater CRUD Methods
    public function openHeaterModal($heaterId = null)
    {
        $this->resetHeaterForm();
        if ($heaterId) {
            $heater = Heater::with('room')->findOrFail($heaterId);
            $this->editingHeaterId = $heaterId;
            $this->heaterName = $heater->name;
            $this->heaterNumber = $heater->number;
            $this->heaterDesc = $heater->desc;
            $this->heaterStatus = $heater->status;
            $this->heaterModel = $heater->model;
            $this->heaterSerialNumber = $heater->serial_number;
            $this->heaterInstallationDate = $heater->installation_date ? $heater->installation_date->format('Y-m-d') : '';
            $this->heaterRoomId = $heater->room_id;
        } else {
            $this->editingHeaterId = null;
        }
        $this->showHeaterModal = true;
    }

    public function saveHeater()
    {
        $rules = [
            'heaterName' => 'required|string|max:255',
            'heaterNumber' => 'nullable|numeric',
            'heaterStatus' => 'required|in:active,inactive,maintenance',
            'heaterModel' => 'nullable|string|max:255',
            'heaterSerialNumber' => 'nullable|string|max:255|unique:heaters,serial_number,' . $this->editingHeaterId,
            'heaterInstallationDate' => 'nullable|date',
            'heaterDesc' => 'nullable|string',
            'heaterRoomId' => 'nullable|exists:rooms,id',
        ];

        $this->validate($rules);

        try {
            // Check if room is already assigned to another heater
            if ($this->heaterRoomId) {
                $existingHeater = Heater::where('room_id', $this->heaterRoomId)
                    ->where('id', '!=', $this->editingHeaterId)
                    ->first();
                
                if ($existingHeater) {
                    $this->addError('heaterRoomId', 'این اتاق قبلاً به هیتر دیگری اختصاص داده شده است.');
                    return;
                }
            }

            $data = [
                'name' => $this->heaterName,
                'number' => $this->heaterNumber ?: null,
                'desc' => $this->heaterDesc,
                'status' => $this->heaterStatus,
                'model' => $this->heaterModel ?: null,
                'serial_number' => $this->heaterSerialNumber ?: null,
                'installation_date' => $this->heaterInstallationDate ?: null,
                'room_id' => $this->heaterRoomId ?: null,
            ];

            if ($this->editingHeaterId) {
                Heater::findOrFail($this->editingHeaterId)->update($data);
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'هیتر با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                Heater::create($data);
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => 'هیتر جدید با موفقیت ایجاد شد',
                    'timer' => 3000
                ]);
            }

            $this->closeHeaterModal();
            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    public function closeHeaterModal()
    {
        $this->showHeaterModal = false;
        $this->resetHeaterForm();
    }

    public function resetHeaterForm()
    {
        $this->editingHeaterId = null;
        $this->heaterName = '';
        $this->heaterNumber = '';
        $this->heaterDesc = '';
        $this->heaterStatus = 'active';
        $this->heaterModel = '';
        $this->heaterSerialNumber = '';
        $this->heaterInstallationDate = '';
        $this->heaterRoomId = null;
        $this->resetErrorBag();
    }

    public function confirmDeleteHeater($heaterId)
    {
        $heater = Heater::findOrFail($heaterId);
        $heaterName = $heater->name;
        
        $this->dispatch('confirm-delete-heater', heaterId: $heaterId, heaterName: $heaterName);
    }

    public function deleteHeater($heaterId)
    {
        try {
            $heater = Heater::findOrFail($heaterId);
            $heater->delete();

            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'حذف شد!',
                'description' => 'هیتر با موفقیت حذف شد',
                'timer' => 3000
            ]);

            $this->resetPage();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف هیتر: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    public function updatingSearchHeater()
    {
        $this->resetPage();
    }

    public function updatingFilterUnit()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function getFilteredHeatersProperty()
    {
        $query = Heater::with(['room' => function($q) {
            $q->select('id', 'name', 'unit_id')->with('unit:id,name');
        }]);

        if ($this->searchHeater) {
            $search = $this->searchHeater;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('number', 'like', '%' . $search . '%')
                  ->orWhere('model', 'like', '%' . $search . '%')
                  ->orWhere('serial_number', 'like', '%' . $search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterUnit) {
            $query->whereHas('room', function($q) {
                $q->where('unit_id', $this->filterUnit);
            });
        }

        return $query->orderBy('created_at', 'desc');
    }

    public function getRoomsProperty()
    {
        return Room::with('unit:id,name')
            ->select('id', 'name', 'unit_id')
            ->orderBy('name')
            ->get();
    }

    public function getUnitsProperty()
    {
        return Unit::select('id', 'name')->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.pages.heaters.heater-room-manager', [
            'filteredHeaters' => $this->filteredHeaters->paginate(15),
            'units' => $this->units,
            'rooms' => $this->rooms,
        ])->title('هیترها');
    }
}
