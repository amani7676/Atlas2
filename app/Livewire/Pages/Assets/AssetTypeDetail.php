<?php

namespace App\Livewire\Pages\Assets;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Room;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AssetTypeDetail extends Component
{
    public $assetType;
    public $assets;
    public $rooms;
    public $units;
    public $connections;

    // Form properties for Connection
    public $selectedAsset = null;
    public $selectedRoom = null;
    public $connectionType = 'direct';
    public $connectedAt = '';
    public $notes = '';

    // Form properties for Asset
    public $assetName = '';
    public $assetNumber = '';
    public $assetDescription = '';
    public $assetStatus = 'active';
    public $assetModel = '';
    public $assetSerialNumber = '';
    public $assetInstallationDate = '';
    public $assetNotes = '';

    // Modal states
    public $showAssetModal = false;
    public $showConnectionModal = false;
    public $editingAssetId = null;
    public $editingConnection = null;

    // Search and filter
    public $searchAsset = '';
    public $searchRoom = '';
    public $filterUnit = '';
    public $filterStatus = '';

    protected $listeners = ['refreshAssets' => 'loadData'];

    public function mount($assetTypeId)
    {
        $this->assetType = AssetType::with('assets')->findOrFail($assetTypeId);
        $this->loadData();
    }

    public function loadData()
    {
        $query = Asset::where('asset_type_id', $this->assetType->id)
            ->with(['assetType', 'rooms']);

        if ($this->searchAsset) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchAsset . '%')
                  ->orWhere('number', 'like', '%' . $this->searchAsset . '%')
                  ->orWhere('model', 'like', '%' . $this->searchAsset . '%');
            });
        }

        if ($this->searchRoom) {
            $query->whereHas('rooms', function ($roomQuery) {
                $roomQuery->where('name', 'like', '%' . $this->searchRoom . '%')
                          ->orWhere('code', 'like', '%' . $this->searchRoom . '%');
            })->with(['rooms' => function ($roomQuery) {
                $roomQuery->where('name', 'like', '%' . $this->searchRoom . '%')
                          ->orWhere('code', 'like', '%' . $this->searchRoom . '%');
            }]);
        }

        if ($this->filterUnit) {
            $query->whereHas('rooms', function ($roomQuery) {
                $roomQuery->where('unit_id', $this->filterUnit);
            })->with(['rooms' => function ($roomQuery) {
                $roomQuery->where('unit_id', $this->filterUnit);
            }]);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $this->assets = $query->get();
        $this->rooms = Room::with(['unit'])->get();
        $this->units = Unit::all();
        $this->loadConnections();
    }

    public function updatedSearchAsset()
    {
        $this->loadData();
        $this->loadConnections();
    }

    public function updatedSearchRoom()
    {
        $this->loadData();
        $this->loadConnections();
    }

    public function updatedFilterUnit()
    {
        $this->loadData();
        $this->loadConnections();
    }

    public function updatedFilterStatus()
    {
        $this->loadData();
        $this->loadConnections();
    }

    public function loadConnections()
    {
        $query = Asset::where('asset_type_id', $this->assetType->id)
            ->with('rooms');

        if ($this->searchAsset) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchAsset . '%')
                  ->orWhere('number', 'like', '%' . $this->searchAsset . '%')
                  ->orWhere('model', 'like', '%' . $this->searchAsset . '%');
            });
        }

        if ($this->searchRoom) {
            $query->whereHas('rooms', function ($roomQuery) {
                $roomQuery->where('name', 'like', '%' . $this->searchRoom . '%')
                          ->orWhere('code', 'like', '%' . $this->searchRoom . '%');
            })->with(['rooms' => function ($roomQuery) {
                $roomQuery->where('name', 'like', '%' . $this->searchRoom . '%')
                          ->orWhere('code', 'like', '%' . $this->searchRoom . '%');
            }]);
        }

        if ($this->filterUnit) {
            $query->whereHas('rooms', function ($roomQuery) {
                $roomQuery->where('unit_id', $this->filterUnit);
            })->with(['rooms' => function ($roomQuery) {
                $roomQuery->where('unit_id', $this->filterUnit);
            }]);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $this->connections = $query->get();
    }

    // Asset CRUD Methods
    public function openAssetModal($assetId = null)
    {
        $this->resetAssetForm();
        if ($assetId) {
            $asset = Asset::findOrFail($assetId);
            $this->editingAssetId = $assetId;
            $this->assetName = $asset->name;
            $this->assetNumber = $asset->number;
            $this->assetDescription = $asset->description;
            $this->assetStatus = $asset->status;
            $this->assetModel = $asset->model;
            $this->assetSerialNumber = $asset->serial_number;
            $this->assetInstallationDate = $asset->installation_date ? $asset->installation_date->format('Y-m-d') : '';
            $this->assetNotes = $asset->notes;
        } else {
            $this->editingAssetId = null;
        }
        $this->showAssetModal = true;
    }

    public function saveAsset()
    {
        $rules = [
            'assetName' => 'required|string|max:255',
            'assetStatus' => 'required|in:active,inactive,maintenance',
            'assetModel' => 'nullable|string|max:255',
            'assetSerialNumber' => 'nullable|string|max:255|unique:assets,serial_number,' . $this->editingAssetId,
            'assetInstallationDate' => 'nullable|date',
            'assetDescription' => 'nullable|string',
            'assetNotes' => 'nullable|string',
        ];

        $this->validate($rules);

        try {
            $data = [
                'asset_type_id' => $this->assetType->id,
                'name' => $this->assetName,
                'number' => $this->assetNumber,
                'description' => $this->assetDescription,
                'status' => $this->assetStatus,
                'model' => $this->assetModel ?: null,
                'serial_number' => $this->assetSerialNumber ?: null,
                'installation_date' => $this->assetInstallationDate ?: null,
                'notes' => $this->assetNotes,
            ];

            if ($this->editingAssetId) {
                Asset::findOrFail($this->editingAssetId)->update($data);
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'دارایی با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                Asset::create($data);
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => 'دارایی جدید با موفقیت ایجاد شد',
                    'timer' => 3000
                ]);
            }

            $this->closeAssetModal();
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

    public function closeAssetModal()
    {
        $this->showAssetModal = false;
        $this->resetAssetForm();
    }

    public function resetAssetForm()
    {
        $this->editingAssetId = null;
        $this->assetName = '';
        $this->assetNumber = '';
        $this->assetDescription = '';
        $this->assetStatus = 'active';
        $this->assetModel = '';
        $this->assetSerialNumber = '';
        $this->assetInstallationDate = '';
        $this->assetNotes = '';
    }

    public function deleteAsset($assetId)
    {
        try {
            $asset = Asset::findOrFail($assetId);
            $asset->rooms()->detach();
            $asset->delete();

            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'حذف شد!',
                'description' => 'دارایی با موفقیت حذف شد',
                'timer' => 3000
            ]);

            $this->loadData();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف دارایی: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    // Connection Methods
    public function openConnectionModal($assetId = null, $roomId = null)
    {
        $this->resetConnectionForm();
        $this->selectedAsset = $assetId;
        $this->selectedRoom = $roomId;
        $this->connectedAt = now()->format('Y-m-d');
        $this->showConnectionModal = true;
    }

    public function editConnection($connectionId)
    {
        $connection = DB::table('asset_room')->find($connectionId);
        if ($connection) {
            $this->editingConnection = $connectionId;
            $this->selectedAsset = $connection->asset_id;
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
            'selectedAsset' => 'required|exists:assets,id',
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
                DB::table('asset_room')
                    ->where('id', $this->editingConnection)
                    ->update([
                        'asset_id' => $this->selectedAsset,
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
                    $exists = DB::table('asset_room')
                        ->where('asset_id', $this->selectedAsset)
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

                    DB::table('asset_room')->insert([
                        'asset_id' => $this->selectedAsset,
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
        DB::table('asset_room')->where('id', $connectionId)->delete();

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
        $this->selectedAsset = null;
        $this->selectedRoom = null;
        $this->connectionType = 'direct';
        $this->connectedAt = '';
        $this->notes = '';
        $this->editingConnection = null;
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
        return view('livewire.pages.assets.asset-type-detail')
            ->title('مدیریت ' . $this->assetType->name);
    }
}
