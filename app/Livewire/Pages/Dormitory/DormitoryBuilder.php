<?php

namespace App\Livewire\Pages\Dormitory;

use App\Repositories\RoomRepository;
use App\Repositories\UnitRepository;
use Livewire\Component;

class DormitoryBuilder extends Component
{
    public $units = [];
    public $selectedUnit = null;
    public $rooms = [];
    
    // Unit form properties
    public $unitName = '';
    public $unitCode = '';
    public $unitDesc = '';
    public $editingUnitId = null;
    public $showUnitModal = false;
    
    // Password protection for units with residents
    public $showPasswordModal = false;
    public $passwordInput = '';
    public $pendingAction = null; // 'edit' or 'delete'
    public $pendingUnitId = null;
    private const UNIT_PASSWORD = '1';
    
    // Room form properties
    public $roomName = '';
    public $roomCode = '';
    public $bedCount = 1;
    public $roomDesc = '';
    public $editingRoomId = null;
    public $showRoomModal = false;
    
    protected $listeners = [
        'delete-unit-confirmed' => 'deleteUnit',
        'delete-room-confirmed' => 'deleteRoom'
    ];
    
    public function deleteUnit($unitId = null)
    {
        // Handle both direct parameter and object parameter
        if (is_array($unitId) && isset($unitId['unitId'])) {
            $unitId = $unitId['unitId'];
        }
        
        try {
            $this->getUnitRepository()->delete($unitId);
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'حذف شد!',
                'description' => 'واحد با موفقیت حذف شد',
                'timer' => 3000
            ]);
            
            if ($this->selectedUnit && $this->selectedUnit->id == $unitId) {
                $this->selectedUnit = null;
                $this->rooms = [];
            }
            
            $this->loadUnits();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی در حذف رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }
    
    public function deleteRoom($roomId = null)
    {
        // Handle both direct parameter and object parameter
        if (is_array($roomId) && isset($roomId['roomId'])) {
            $roomId = $roomId['roomId'];
        }
        
        try {
            $room = $this->getRoomRepository()->getWithBeds($roomId);
            if ($room) {
                $room->delete();
                
                $this->dispatch('show-toast', [
                    'type' => 'error',
                    'title' => 'حذف شد!',
                    'description' => 'اتاق با موفقیت حذف شد',
                    'timer' => 3000
                ]);
                
                if ($this->selectedUnit) {
                    $this->loadRooms($this->selectedUnit->id);
                    $this->loadUnits();
                }
            }
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی در حذف رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }
    
    protected function getUnitRepository(): UnitRepository
    {
        return app(UnitRepository::class);
    }
    
    protected function getRoomRepository(): RoomRepository
    {
        return app(RoomRepository::class);
    }
    
    public function mount(): void
    {
        $this->loadUnits();
    }
    
    public function loadUnits()
    {
        $this->units = $this->getUnitRepository()->getAllWithRoomsCount();
    }
    
    public function loadRooms($unitId)
    {
        $this->selectedUnit = $this->getUnitRepository()->findById($unitId);
        if ($this->selectedUnit) {
            $this->rooms = $this->getRoomRepository()->getByUnit($unitId);
        }
    }
    
    public function openUnitModal($unitId = null)
    {
        $this->resetUnitForm();
        if ($unitId) {
            // Check if unit has residents
            if ($this->unitHasResidents($unitId)) {
                $this->pendingAction = 'edit';
                $this->pendingUnitId = $unitId;
                $this->showPasswordModal = true;
                return;
            }
            
            $unit = $this->getUnitRepository()->findById($unitId);
            if ($unit) {
                $this->editingUnitId = $unitId;
                $this->unitName = $unit->name;
                $this->unitCode = $unit->code;
                $this->unitDesc = $unit->desc ?? '';
            }
        }
        $this->showUnitModal = true;
    }
    
    public function unitHasResidents($unitId): bool
    {
        $unit = $this->getUnitRepository()->findById($unitId);
        if (!$unit) {
            return false;
        }
        
        // Check if unit has any contracts (which means it has residents)
        return \App\Models\Contract::whereHas('bed.room', function($query) use ($unitId) {
            $query->where('unit_id', $unitId);
        })->exists();
    }
    
    public function verifyPassword()
    {
        if ($this->passwordInput === self::UNIT_PASSWORD) {
            $this->showPasswordModal = false;
            $this->passwordInput = '';
            
            if ($this->pendingAction === 'edit') {
                $this->proceedWithEdit();
            } elseif ($this->pendingAction === 'delete') {
                $this->proceedWithDelete();
            }
            
            $this->pendingAction = null;
            $this->pendingUnitId = null;
        } else {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'رمز وارد شده صحیح نیست',
                'timer' => 3000
            ]);
            $this->passwordInput = '';
        }
    }
    
    public function proceedWithEdit()
    {
        if (!$this->pendingUnitId) {
            return;
        }
        
        $unit = $this->getUnitRepository()->findById($this->pendingUnitId);
        if ($unit) {
            $this->editingUnitId = $this->pendingUnitId;
            $this->unitName = $unit->name;
            $this->unitCode = $unit->code;
            $this->unitDesc = $unit->desc ?? '';
            $this->showUnitModal = true;
        }
    }
    
    public function proceedWithDelete()
    {
        if (!$this->pendingUnitId) {
            return;
        }
        
        $this->deleteUnit($this->pendingUnitId);
    }
    
    public function closePasswordModal()
    {
        $this->showPasswordModal = false;
        $this->passwordInput = '';
        $this->pendingAction = null;
        $this->pendingUnitId = null;
    }
    
    public function saveUnit()
    {
        $this->validate([
            'unitName' => 'required|string|max:255',
            'unitCode' => 'required|numeric|unique:units,code,' . ($this->editingUnitId ?? ''),
            'unitDesc' => 'nullable|string',
        ], [
            'unitName.required' => 'نام واحد الزامی است',
            'unitCode.required' => 'کد واحد الزامی است',
            'unitCode.unique' => 'این کد واحد قبلاً استفاده شده است',
        ]);
        
        try {
            if ($this->editingUnitId) {
                $this->getUnitRepository()->update($this->editingUnitId, [
                    'name' => $this->unitName,
                    'code' => $this->unitCode,
                    'desc' => $this->unitDesc,
                ]);
                
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'واحد با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                $this->getUnitRepository()->create([
                    'name' => $this->unitName,
                    'code' => $this->unitCode,
                    'desc' => $this->unitDesc,
                ]);
                
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => 'واحد جدید با موفقیت ایجاد شد',
                    'timer' => 3000
                ]);
            }
            
            $this->closeUnitModal();
            $this->loadUnits();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }
    
    public function confirmDeleteUnit($unitId)
    {
        // Check if unit has residents
        if ($this->unitHasResidents($unitId)) {
            $this->pendingAction = 'delete';
            $this->pendingUnitId = $unitId;
            $this->showPasswordModal = true;
            return;
        }
        
        $unit = $this->getUnitRepository()->findById($unitId);
        if ($unit) {
            $this->dispatch('confirmDelete', ['id' => $unitId, 'type' => 'unit']);
        }
    }
    
    public function closeUnitModal()
    {
        $this->showUnitModal = false;
        $this->resetUnitForm();
    }
    
    public function resetUnitForm()
    {
        $this->unitName = '';
        $this->unitCode = '';
        $this->unitDesc = '';
        $this->editingUnitId = null;
    }
    
    public function openRoomModal($roomId = null)
    {
        if (!$this->selectedUnit) {
            $this->dispatch('show-toast', [
                'type' => 'warning',
                'title' => 'هشدار!',
                'description' => 'لطفاً ابتدا یک واحد انتخاب کنید',
                'timer' => 3000
            ]);
            return;
        }
        
        $this->resetRoomForm();
        if ($roomId) {
            $room = $this->getRoomRepository()->getWithBeds($roomId);
            if ($room) {
                $this->editingRoomId = $roomId;
                $this->roomName = $room->name;
                $this->roomCode = $room->code ?? '';
                $this->bedCount = $room->bed_count;
                $this->roomDesc = $room->desc ?? '';
            }
        }
        $this->showRoomModal = true;
    }
    
    public function saveRoom()
    {
        if (!$this->selectedUnit) {
            return;
        }
        
        $this->validate([
            'roomName' => 'required|string|max:255',
            'roomCode' => 'nullable|integer',
            'bedCount' => 'required|integer|min:1|max:100',
            'roomDesc' => 'nullable|string',
        ], [
            'roomName.required' => 'نام اتاق الزامی است',
            'bedCount.required' => 'تعداد تخت الزامی است',
            'bedCount.min' => 'تعداد تخت باید حداقل 1 باشد',
            'bedCount.max' => 'تعداد تخت نمی‌تواند بیشتر از 100 باشد',
        ]);
        
        try {
            if ($this->editingRoomId) {
                $room = $this->getRoomRepository()->getWithBeds($this->editingRoomId);
                if ($room) {
                    $room->update([
                        'name' => $this->roomName,
                        'code' => $this->roomCode ?: null,
                        'bed_count' => $this->bedCount,
                        'desc' => $this->roomDesc,
                    ]);
                }
                
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'اتاق با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                $this->getRoomRepository()->create([
                    'name' => $this->roomName,
                    'code' => $this->roomCode ?: null,
                    'unit_id' => $this->selectedUnit->id,
                    'bed_count' => $this->bedCount,
                    'desc' => $this->roomDesc,
                ]);
                
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => 'اتاق جدید با موفقیت ایجاد شد',
                    'timer' => 3000
                ]);
            }
            
            $this->closeRoomModal();
            $this->loadRooms($this->selectedUnit->id);
            $this->loadUnits();
        } catch (\Exception $e) {
            $this->dispatch('show-toast', [
                'type' => 'error',
                'title' => 'خطا!',
                'description' => 'خطایی رخ داد: ' . $e->getMessage(),
                'timer' => 3000
            ]);
        }
    }
    
    public function confirmDeleteRoom($roomId)
    {
        $this->dispatch('confirmDelete', ['id' => $roomId, 'type' => 'room']);
    }
    
    public function closeRoomModal()
    {
        $this->showRoomModal = false;
        $this->resetRoomForm();
    }
    
    public function resetRoomForm()
    {
        $this->roomName = '';
        $this->roomCode = '';
        $this->bedCount = 1;
        $this->roomDesc = '';
        $this->editingRoomId = null;
    }
    
    public function render()
    {
        return view('livewire.pages.dormitory.dormitory-builder')
            ->title("ساخت خوابگاه");
    }
}

