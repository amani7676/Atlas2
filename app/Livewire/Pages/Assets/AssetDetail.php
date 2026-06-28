<?php

namespace App\Livewire\Pages\Assets;

use App\Models\Asset;
use App\Models\AssetType;
use App\Models\Room;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AssetDetail extends Component
{
    public $asset;
    public $assetTypes;
    public $rooms;
    public $units;
    public $connections;

    // Form properties for Connection
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
    public $selectedAssetType = null;

    // Modal states
    public $showConnectionModal = false;
    public $showAssetModal = false;
    public $editingConnection = null;
    public $editingAssetId = null;

    // Search and filter
    public $searchRoom = '';
    public $filterUnit = '';

    public function mount($assetId)
    {
        $this->asset = Asset::with(['assetType', 'rooms.unit'])->findOrFail($assetId);
        $this->loadData();
    }

    public function loadData()
    {
        $this->rooms = Room::with(['unit'])->get();
        $this->units = Unit::all();
        $this->assetTypes = AssetType::active()->get();
        $this->loadConnections();
    }

    // Asset CRUD Methods
    public function openAssetModal()
    {
        $this->resetAssetForm();
        $this->editingAssetId = $this->asset->id;
        $this->assetName = $this->asset->name;
        $this->assetNumber = $this->asset->number;
        $this->assetDescription = $this->asset->description;
        $this->assetStatus = $this->asset->status;
        $this->assetModel = $this->asset->model;
        $this->assetSerialNumber = $this->asset->serial_number;
        $this->assetInstallationDate = $this->asset->installation_date ? $this->asset->installation_date->format('Y-m-d') : '';
        $this->assetNotes = $this->asset->notes;
        $this->selectedAssetType = $this->asset->asset_type_id;
        $this->showAssetModal = true;
    }

    public function saveAsset()
    {
        $rules = [
            'assetName' => 'required|string|max:255',
            'selectedAssetType' => 'required|exists:asset_types,id',
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
                'asset_type_id' => $this->selectedAssetType,
                'name' => $this->assetName,
                'number' => $this->assetNumber,
                'description' => $this->assetDescription,
                'status' => $this->assetStatus,
                'model' => $this->assetModel ?: null,
                'serial_number' => $this->assetSerialNumber ?: null,
                'installation_date' => $this->assetInstallationDate ?: null,
                'notes' => $this->assetNotes,
            ];

            $this->asset->update($data);
            $this->dispatch('show-toast', [
                'type' => 'info',
                'title' => 'به‌روزرسانی شد!',
                'description' => 'دارایی با موفقیت به‌روزرسانی شد',
                'timer' => 3000
            ]);

            $this->closeAssetModal();
            $this->loadData();
            $this->asset = Asset::with(['assetType', 'rooms.unit'])->findOrFail($this->asset->id);
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
        $this->selectedAssetType = null;
    }

    public function confirmDeleteAsset()
    {
        $assetName = $this->asset->name;
        
        $this->dispatch('confirm-delete-asset', assetName: $assetName);
    }

    public function deleteAsset()
    {
        try {
            $this->asset->rooms()->detach();
            $this->asset->delete();

            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'حذف شد!',
                'description' => 'دارایی با موفقیت حذف شد',
                'timer' => 3000
            ]);

            return redirect()->route('amval');
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطا در حذف دارایی: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }

    public function loadConnections()
    {
        $this->connections = $this->asset->rooms()->with('unit')->get();
    }

    // Connection Methods
    public function openConnectionModal($roomId = null)
    {
        $this->resetConnectionForm();
        $this->selectedRoom = $roomId;
        $this->connectedAt = now()->format('Y-m-d');
        $this->showConnectionModal = true;
    }

    public function editConnection($connectionId)
    {
        $connection = DB::table('asset_room')->find($connectionId);
        if ($connection) {
            $this->editingConnection = $connectionId;
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
                        'asset_id' => $this->asset->id,
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
                        ->where('asset_id', $this->asset->id)
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
                        'asset_id' => $this->asset->id,
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
            $this->asset = Asset::with(['assetType', 'rooms.unit'])->findOrFail($this->asset->id);
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
        $this->asset = Asset::with(['assetType', 'rooms.unit'])->findOrFail($this->asset->id);
    }

    public function closeConnectionModal()
    {
        $this->showConnectionModal = false;
        $this->resetConnectionForm();
    }

    public function resetConnectionForm()
    {
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
        return view('livewire.pages.assets.asset-detail')
            ->title('جزئیات دارایی: ' . $this->asset->name);
    }
}
