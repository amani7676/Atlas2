<?php

namespace App\Livewire\Pages\Dormitory;

use App\Models\Bed;
use App\Repositories\RoomRepository;
use App\Repositories\UnitRepository;
use Livewire\Attributes\On;
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
    public $unitColor = '#667eea'; // رنگ پیش‌فرض
    public $roomColorForUnit = '#f093fb'; // رنگ پیش‌فرض برای اتاق‌های این واحد
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
    public $roomColor = '#f093fb'; // رنگ پیش‌فرض
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
            
            // بستن modal پسورد در صورت باز بودن
            if ($this->showPasswordModal) {
                $this->closePasswordModal();
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
            // برای واحدهای موجود (ویرایش)، پسورد لازم است
            $this->pendingAction = 'edit';
            $this->pendingUnitId = $unitId;
            $this->showPasswordModal = true;
            return;
        }
        // برای واحدهای جدید، پسورد لازم نیست - مستقیماً modal را باز می‌کنیم
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
        
        $unit = $this->getUnitRepository()->getWithRooms($this->pendingUnitId);
        if ($unit) {
            $this->editingUnitId = $this->pendingUnitId;
            $this->unitName = $unit->name;
            $this->unitCode = $unit->code;
            $this->unitDesc = $unit->desc ?? '';
            // لود رنگ واحد - اگر null یا خالی باشد از پیش‌فرض استفاده می‌کنیم
            $this->unitColor = !empty($unit->color) ? $unit->color : '#667eea';
            // دریافت رنگ اتاق‌ها از اولین اتاق واحد (اگر وجود داشته باشد)
            $firstRoom = $unit->rooms->first();
            $this->roomColorForUnit = !empty($firstRoom->color) ? $firstRoom->color : '#f093fb';
            $this->showUnitModal = true;
        }
    }
    
    public function proceedWithDelete()
    {
        if (!$this->pendingUnitId) {
            return;
        }
        
        // نمایش تایید نهایی با cuteAlert
        $unit = $this->getUnitRepository()->findById($this->pendingUnitId);
        $unitName = $unit ? $unit->name : 'این واحد';
        
        $this->dispatch('confirm-delete-unit-after-password', [
            'unitId' => $this->pendingUnitId,
            'unitName' => $unitName
        ]);
    }
    
    #[On('proceed-to-password-after-initial-confirm')]
    public function proceedToPasswordAfterInitialConfirm($unitId = null)
    {
        // بعد از تایید اولیه، modal پسورد را باز می‌کنیم
        // Handle both direct parameter and object parameter
        if (is_array($unitId) && isset($unitId['unitId'])) {
            $unitId = $unitId['unitId'];
        }
        
        if (!$unitId) {
            return;
        }
        
        $this->pendingAction = 'delete';
        $this->pendingUnitId = $unitId;
        $this->showPasswordModal = true;
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
        // Normalize color values (uppercase to lowercase, ensure # prefix)
        if (!empty($this->unitColor)) {
            $this->unitColor = strtolower(trim($this->unitColor));
            if (!str_starts_with($this->unitColor, '#')) {
                $this->unitColor = '#' . $this->unitColor;
            }
        }
        
        if (!empty($this->roomColorForUnit)) {
            $this->roomColorForUnit = strtolower(trim($this->roomColorForUnit));
            if (!str_starts_with($this->roomColorForUnit, '#')) {
                $this->roomColorForUnit = '#' . $this->roomColorForUnit;
            }
        }
        
        $this->validate([
            'unitName' => 'required|string|max:255',
            'unitCode' => 'required|numeric|unique:units,code,' . ($this->editingUnitId ?? ''),
            'unitDesc' => 'nullable|string',
            'unitColor' => 'nullable|string|regex:/^#[0-9a-f]{6}$/i',
            'roomColorForUnit' => 'nullable|string|regex:/^#[0-9a-f]{6}$/i',
        ], [
            'unitName.required' => 'نام واحد الزامی است',
            'unitCode.required' => 'کد واحد الزامی است',
            'unitCode.unique' => 'این کد واحد قبلاً استفاده شده است',
            'unitColor.regex' => 'فرمت رنگ نامعتبر است (مثال: #667eea)',
            'roomColorForUnit.regex' => 'فرمت رنگ اتاق نامعتبر است (مثال: #f093fb)',
        ]);
        
        try {
            $unit = null;
            $unitColorValue = !empty($this->unitColor) ? $this->unitColor : '#667eea';
            $roomColorValue = !empty($this->roomColorForUnit) ? $this->roomColorForUnit : '#f093fb';
            
            if ($this->editingUnitId) {
                $unit = $this->getUnitRepository()->update($this->editingUnitId, [
                    'name' => $this->unitName,
                    'code' => $this->unitCode,
                    'desc' => $this->unitDesc,
                    'color' => $unitColorValue,
                ]);
                
                // به‌روزرسانی رنگ همه اتاق‌های این واحد
                if ($unit->rooms()->count() > 0) {
                    $unit->rooms()->update([
                        'color' => $roomColorValue
                    ]);
                }
                
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'واحد و اتاق‌هایش با موفقیت به‌روزرسانی شدند',
                    'timer' => 3000
                ]);
            } else {
                $unit = $this->getUnitRepository()->create([
                    'name' => $this->unitName,
                    'code' => $this->unitCode,
                    'desc' => $this->unitDesc,
                    'color' => $unitColorValue,
                ]);
                
                // برای واحد جدید، رنگ اتاق‌ها در زمان ایجاد اتاق اعمال می‌شود
                // (در saveRoom)
                
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
        // نمایش alert تایید اولیه
        $unit = $this->getUnitRepository()->findById($unitId);
        $unitName = $unit ? $unit->name : 'این واحد';
        
        $this->dispatch('confirm-delete-unit-initial', [
            'unitId' => $unitId,
            'unitName' => $unitName
        ]);
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
        $this->unitColor = '#667eea';
        $this->roomColorForUnit = '#f093fb';
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
                // حالت ویرایش
                $room = $this->getRoomRepository()->getWithBeds($this->editingRoomId);
                if ($room) {
                    $oldBedCount = $room->bed_count;
                    // استفاده از رنگ واحد برای اتاق (از اولین اتاق واحد)
                    $roomColor = $this->selectedUnit->rooms()->first()?->color ?? '#f093fb';
                    
                    $room->update([
                        'name' => $this->roomName,
                        'code' => $this->roomCode ?: null,
                        'bed_count' => $this->bedCount,
                        'desc' => $this->roomDesc,
                        'color' => $roomColor,
                    ]);
                    
                    // اگر تعداد تخت‌ها تغییر کرد
                    if ($this->bedCount != $oldBedCount) {
                        $existingBedsCount = $room->beds()->count();
                        
                        if ($this->bedCount > $existingBedsCount) {
                            // تخت‌های اضافی ایجاد کن
                            $startNumber = $existingBedsCount + 1;
                            for ($bedNumber = $startNumber; $bedNumber <= $this->bedCount; $bedNumber++) {
                                Bed::create([
                                    'room_id' => $room->id,
                                    'name' => $bedNumber,
                                    'state' => 'active',
                                    'state_ratio_resident' => 'empty',
                                    'desc' => "تخت شماره {$bedNumber} در اتاق {$this->roomName}",
                                ]);
                            }
                        } elseif ($this->bedCount < $existingBedsCount) {
                            // تخت‌های اضافی را حذف کن (فقط تخت‌های خالی)
                            $bedsToDelete = $room->beds()
                                ->where('state_ratio_resident', 'empty')
                                ->orderBy('name', 'desc')
                                ->limit($existingBedsCount - $this->bedCount)
                                ->get();
                            
                            foreach ($bedsToDelete as $bed) {
                                // فقط تخت‌های خالی را حذف کن
                                if ($bed->state_ratio_resident === 'empty') {
                                    $bed->delete();
                                }
                            }
                        }
                    }
                }
                
                $this->dispatch('show-toast', [
                    'type' => 'info',
                    'title' => 'به‌روزرسانی شد!',
                    'description' => 'اتاق با موفقیت به‌روزرسانی شد',
                    'timer' => 3000
                ]);
            } else {
                // حالت ایجاد جدید
                // استفاده از رنگ واحد برای اتاق (از اولین اتاق واحد یا رنگ پیش‌فرض)
                // اگر واحد تازه ایجاد شده و اتاقی ندارد، از roomColorForUnit استفاده می‌کنیم
                $roomColor = $this->selectedUnit->rooms()->first()?->color ?? '#f093fb';
                
                $room = $this->getRoomRepository()->create([
                    'name' => $this->roomName,
                    'code' => $this->roomCode ?: null,
                    'unit_id' => $this->selectedUnit->id,
                    'bed_count' => $this->bedCount,
                    'desc' => $this->roomDesc,
                    'color' => $roomColor,
                ]);
                
                // ایجاد تخت‌ها به تعداد مشخص شده
                for ($bedNumber = 1; $bedNumber <= $this->bedCount; $bedNumber++) {
                    Bed::create([
                        'room_id' => $room->id,
                        'name' => $bedNumber,
                        'state' => 'active',
                        'state_ratio_resident' => 'empty', // حالت پیش‌فرض: خالی
                        'desc' => "تخت شماره {$bedNumber} در اتاق {$this->roomName}",
                    ]);
                }
                
                $this->dispatch('show-toast', [
                    'type' => 'success',
                    'title' => 'ایجاد شد!',
                    'description' => "اتاق جدید با {$this->bedCount} تخت با موفقیت ایجاد شد",
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

