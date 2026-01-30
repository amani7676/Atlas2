<?php

namespace App\Livewire\Pages\Coolers;

use App\Models\Cooler;
use App\Models\Room;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CoolerRoomManager extends Component
{
    public $coolers;
    public $rooms;
    public $units;
    public $connections = [];

    // Form properties for Connection
    public $selectedCooler = null;
    public $selectedRoom = null;
    public $connectionType = 'direct';
    public $connectedAt = '';
    public $notes = '';

    // Form properties for Cooler
    public $coolerName = '';
    public $coolerNumber = '';
    public $coolerDesc = '';
    public $coolerStatus = 'active';
    public $coolerModel = '';
    public $coolerSerialNumber = '';
    public $coolerInstallationDate = '';

    // Modal states
    public $showCoolerModal = false;
    public $showConnectionModal = false;
    public $editingCoolerId = null;
    public $editingConnection = null;

    // Search and filter
    public $searchCooler = '';
    public $searchRoom = '';
    public $filterUnit = '';
    public $filterStatus = '';

    protected $listeners = ['delete-confirmed' => 'deleteConnection'];

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->coolers = Cooler::with('rooms')->get();
        $this->rooms = Room::with(['unit', 'coolers'])->get();
        $this->units = Unit::all();
        $this->loadConnections();
    }

    public function loadConnections()
    {
        $this->connections = Cooler::with('rooms')->get();
    }

    // Cooler CRUD Methods
    public function openCoolerModal($coolerId = null)
    {
        $this->resetCoolerForm();
        if ($coolerId) {
            $cooler = Cooler::findOrFail($coolerId);
            $this->editingCoolerId = $coolerId;
            $this->coolerName = $cooler->name;
            $this->coolerNumber = $cooler->number;
            $this->coolerDesc = $cooler->desc;
            $this->coolerStatus = $cooler->status;
            $this->coolerModel = $cooler->model;
            $this->coolerSerialNumber = $cooler->serial_number;
            $this->coolerInstallationDate = $cooler->installation_date ? $cooler->installation_date->format('Y-m-d') : '';
        } else {
            $this->editingCoolerId = null;
        }
        $this->showCoolerModal = true;
    }

    public function saveCooler()
    {
        $rules = [
            'coolerName' => 'required|string|max:255',
            'coolerNumber' => 'required|numeric',
            'coolerStatus' => 'required|in:active,inactive,maintenance',
            'coolerModel' => 'nullable|string|max:255',
            'coolerSerialNumber' => 'nullable|string|max:255|unique:coolers,serial_number,' . $this->editingCoolerId,
            'coolerInstallationDate' => 'nullable|date',
            'coolerDesc' => 'nullable|string',
        ];

        $this->validate($rules);

        try {
            $data = [
                'name' => $this->coolerName,
                'number' => $this->coolerNumber,
                'desc' => $this->coolerDesc,
                'status' => $this->coolerStatus,
                'model' => $this->coolerModel ?: null,
                'serial_number' => $this->coolerSerialNumber ?: null,
                'installation_date' => $this->coolerInstallationDate ?: null,
            ];

            if ($this->editingCoolerId) {
                Cooler::findOrFail($this->editingCoolerId)->update($data);
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'کولر با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                Cooler::create($data);
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => 'کولر جدید با موفقیت ایجاد شد',
                    'timer' => 3000
                ]);
            }

            $this->closeCoolerModal();
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    public function closeCoolerModal()
    {
        $this->showCoolerModal = false;
        $this->resetCoolerForm();
    }

    public function resetCoolerForm()
    {
        $this->editingCoolerId = null;
        $this->coolerName = '';
        $this->coolerNumber = '';
        $this->coolerDesc = '';
        $this->coolerStatus = 'active';
        $this->coolerModel = '';
        $this->coolerSerialNumber = '';
        $this->coolerInstallationDate = '';
    }

    public function confirmDeleteCooler($coolerId)
    {
        $cooler = Cooler::findOrFail($coolerId);
        $coolerName = $cooler->name;
        
        $this->dispatch('confirm-delete-cooler', coolerId: $coolerId, coolerName: $coolerName);
    }

    public function deleteCooler($coolerId)
    {
        try {
            $cooler = Cooler::findOrFail($coolerId);
            $cooler->rooms()->detach();
            $cooler->delete();

            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'حذف شد!',
                'description' => 'کولر با موفقیت حذف شد',
                'timer' => 3000
            ]);

            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف کولر: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    // Connection Methods
    public function openConnectionModal($coolerId = null, $roomId = null)
    {
        $this->resetConnectionForm();
        $this->selectedCooler = $coolerId;
        $this->selectedRoom = $roomId;
        $this->connectedAt = now()->format('Y-m-d');
        $this->showConnectionModal = true;
    }

    public function editConnection($connectionId)
    {
        $connection = DB::table('cooler_room')->find($connectionId);
        if ($connection) {
            $this->editingConnection = $connectionId;
            $this->selectedCooler = $connection->cooler_id;
            $this->selectedRoom = $connection->room_id;
            $this->connectionType = $connection->connection_type;
            $this->connectedAt = $connection->connected_at;
            $this->notes = $connection->notes;
            $this->showConnectionModal = true;
        }
    }

    public function saveConnection()
    {
        $roomIds = $this->editingConnection ? [$this->selectedRoom] : (is_array($this->selectedRoom) ? $this->selectedRoom : [$this->selectedRoom]);

        $rules = [
            'selectedCooler' => 'required|exists:coolers,id',
            'notes' => 'nullable|string|max:500',
            'connectedAt' => 'nullable|date',
        ];

        if ($this->editingConnection) {
            $rules['selectedRoom'] = 'required|exists:rooms,id';
        } else {
            $rules['selectedRoom'] = 'required|array';
            $rules['selectedRoom.*'] = 'exists:rooms,id';
        }

        $this->validate($rules);

        try {
            if ($this->editingConnection) {
                DB::table('cooler_room')
                    ->where('id', $this->editingConnection)
                    ->update([
                        'cooler_id' => $this->selectedCooler,
                        'room_id' => $this->selectedRoom,
                        'connection_type' => $this->connectionType,
                        'connected_at' => $this->connectedAt,
                        'notes' => $this->notes,
                        'updated_at' => now()
                    ]);

                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'اتصال با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                foreach ($roomIds as $roomId) {
                    $exists = DB::table('cooler_room')
                        ->where('cooler_id', $this->selectedCooler)
                        ->where('room_id', $roomId)
                        ->exists();

                    if ($exists) {
                        $this->dispatch('show-toast', [
                            'type' => 'error',
                            'title' => 'خطا!',
                            'description' => "اتصال برای این اتاق قبلاً وجود دارد!",
                            'timer' => 3000
                        ]);
                        continue;
                    }

                    DB::table('cooler_room')->insert([
                        'cooler_id' => $this->selectedCooler,
                        'room_id' => $roomId,
                        'connection_type' => $this->connectionType,
                        'connected_at' => count($roomIds) === 1 ? $this->connectedAt : null,
                        'notes' => count($roomIds) === 1 ? $this->notes : null,
                        'created_at' => now(),
                        'updated_at' => now()
                    ]);
                }

                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => 'اتصال(های) جدید با موفقیت ایجاد شد',
                    'timer' => 3000
                ]);
            }

            $this->closeConnectionModal();
            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    public function confirmDelete($connectionId)
    {
        $this->dispatch('confirmDelete', ['connectionId' => $connectionId]);
    }

    public function deleteConnection($connectionId)
    {
        DB::table('cooler_room')->where('id', $connectionId)->delete();

        $this->dispatch('show-toast', [
            'type' => 'error',
            'title' => 'حذف شد!',
            'description' => 'اتصال با موفقیت حذف شد',
            'timer' => 3000
        ]);
        $this->loadData();
    }

    public function closeConnectionModal()
    {
        $this->showConnectionModal = false;
        $this->resetConnectionForm();
    }

    public function resetConnectionForm()
    {
        $this->selectedCooler = null;
        $this->selectedRoom = null;
        $this->connectionType = 'direct';
        $this->connectedAt = '';
        $this->notes = '';
        $this->editingConnection = null;
    }

    public function getFilteredCoolersProperty()
    {
        return $this->coolers->when($this->searchCooler, function ($query) {
            return $query->filter(function ($cooler) {
                return str_contains(strtolower($cooler->name), strtolower($this->searchCooler)) ||
                    str_contains($cooler->number ?? '', $this->searchCooler) ||
                    str_contains(strtolower($cooler->model ?? ''), strtolower($this->searchCooler));
            });
        })->when($this->filterStatus, function ($query) {
            return $query->where('status', $this->filterStatus);
        });
    }

    public function getFilteredRoomsProperty()
    {
        return $this->rooms->when($this->searchRoom, function ($query) {
            return $query->filter(function ($room) {
                return str_contains(strtolower($room->name), strtolower($this->searchRoom)) ||
                    str_contains($room->code ?? '', $this->searchRoom);
            });
        })->when($this->filterUnit, function ($query) {
            return $query->where('unit_id', $this->filterUnit);
        });
    }

    public function render()
    {
        return view('livewire.pages.coolers.cooler-room-manager')
            ->title('کولرها');
    }
}
