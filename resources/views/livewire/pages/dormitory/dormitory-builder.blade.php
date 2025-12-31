<div>
    <div class="container-fluid py-4" dir="rtl">
        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="text-white">
                                <h3 class="mb-2 fw-bold">
                                    <i class="fas fa-building me-2"></i>
                                    ساخت و مدیریت خوابگاه
                                </h3>
                                <p class="mb-0 opacity-75">تعریف واحدها و اتاق‌های خوابگاه</p>
                            </div>
                            <button class="btn btn-light btn-lg shadow-sm mt-3 mt-md-0" wire:click="openUnitModal" style="border-radius: 15px;">
                                <i class="fas fa-plus me-2"></i>
                                افزودن واحد جدید
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Units Section --}}
            <div class="col-lg-4 mb-4">
                <div class="card border-0 shadow-lg h-100" style="border-radius: 20px; overflow: hidden;">
                    <div class="card-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 20px;">
                        <h5 class="mb-0 fw-bold">
                            <i class="fas fa-building me-2"></i>
                            واحدها
                        </h5>
                        <small class="opacity-75">لیست تمام واحدها</small>
                    </div>
                    <div class="card-body p-3" style="max-height: 600px; overflow-y: auto;">
                        @forelse($units as $unit)
                            <div class="card mb-3 border-0 shadow-sm unit-card" 
                                 wire:click="loadRooms({{ $unit->id }})"
                                 style="cursor: pointer; border-radius: 15px; transition: all 0.3s ease; border-left: 4px solid #667eea;"
                                 onmouseover="this.style.transform='translateX(-5px)'; this.style.boxShadow='0 8px 20px rgba(102, 126, 234, 0.3)';"
                                 onmouseout="this.style.transform='translateX(0)'; this.style.boxShadow='0 2px 10px rgba(0,0,0,0.1)';">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-2 text-primary">{{ $unit->name }}</h6>
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-hashtag me-1"></i>
                                                    کد: {{ $unit->code }}
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <span class="badge bg-info" style="border-radius: 10px;">
                                                    <i class="fas fa-door-open me-1"></i>
                                                    {{ $unit->rooms_count ?? 0 }} اتاق
                                                </span>
                                            </div>
                                        </div>
                                        <div class="btn-group-vertical gap-1">
                                            <button class="btn btn-sm btn-outline-primary" 
                                                    wire:click.stop="openUnitModal({{ $unit->id }})"
                                                    style="border-radius: 8px;">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger" 
                                                    wire:click.stop="confirmDeleteUnit({{ $unit->id }})"
                                                    style="border-radius: 8px;">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                <p class="text-muted">هنوز واحدی اضافه نشده است</p>
                                <button class="btn btn-primary" wire:click="openUnitModal">
                                    <i class="fas fa-plus me-2"></i>
                                    افزودن واحد اول
                                </button>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Rooms Section --}}
            <div class="col-lg-8 mb-4">
                @if($selectedUnit)
                    <div class="card border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                        <div class="card-header text-white d-flex justify-content-between align-items-center" 
                             style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 20px;">
                            <div>
                                <h5 class="mb-1 fw-bold">
                                    <i class="fas fa-door-open me-2"></i>
                                    اتاق‌های واحد: {{ $selectedUnit->name }}
                                </h5>
                                <small class="opacity-75">کد واحد: {{ $selectedUnit->code }}</small>
                            </div>
                            <button class="btn btn-light btn-lg shadow-sm" wire:click="openRoomModal" style="border-radius: 15px;">
                                <i class="fas fa-plus me-2"></i>
                                افزودن اتاق
                            </button>
                        </div>
                        <div class="card-body p-4">
                            <div class="row">
                                @forelse($rooms as $room)
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border-0 shadow-sm h-100 room-card" 
                                             style="border-radius: 15px; transition: all 0.3s ease; border-top: 4px solid #f5576c;">
                                            <div class="card-body p-3">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h6 class="fw-bold mb-0 text-dark">{{ $room->name }}</h6>
                                                    <div class="btn-group-vertical gap-1">
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                wire:click="openRoomModal({{ $room->id }})"
                                                                style="border-radius: 8px; padding: 4px 8px;">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" 
                                                                wire:click="confirmDeleteRoom({{ $room->id }})"
                                                                style="border-radius: 8px; padding: 4px 8px;">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                @if($room->code)
                                                    <small class="text-muted d-block mb-2">
                                                        <i class="fas fa-hashtag me-1"></i>
                                                        کد: {{ $room->code }}
                                                    </small>
                                                @endif
                                                <div class="mt-3">
                                                    <span class="badge bg-primary" style="border-radius: 10px; padding: 8px 12px; font-size: 0.9rem;">
                                                        <i class="fas fa-bed me-1"></i>
                                                        {{ $room->bed_count }} تخت
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="fas fa-door-open fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">هنوز اتاقی در این واحد اضافه نشده است</p>
                                            <button class="btn btn-primary btn-lg" wire:click="openRoomModal" style="border-radius: 15px;">
                                                <i class="fas fa-plus me-2"></i>
                                                افزودن اتاق اول
                                            </button>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-lg h-100" style="border-radius: 20px;">
                        <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 400px;">
                            <div class="text-center">
                                <i class="fas fa-mouse-pointer fa-4x text-muted mb-4"></i>
                                <h5 class="text-muted mb-3">لطفاً یک واحد انتخاب کنید</h5>
                                <p class="text-muted">برای مشاهده و مدیریت اتاق‌ها، روی یکی از واحدها کلیک کنید</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Unit Modal --}}
    @if($showUnitModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:click.self="closeUnitModal">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; padding: 20px;">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-building me-2"></i>
                            {{ $editingUnitId ? 'ویرایش واحد' : 'افزودن واحد جدید' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeUnitModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveUnit">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    نام واحد <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('unitName') is-invalid @enderror" 
                                       wire:model="unitName"
                                       placeholder="مثال: واحد 1"
                                       style="border-radius: 10px;">
                                @error('unitName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-hashtag me-2 text-primary"></i>
                                    کد واحد <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg @error('unitCode') is-invalid @enderror" 
                                       wire:model="unitCode"
                                       placeholder="مثال: 101"
                                       style="border-radius: 10px;">
                                @error('unitCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-align-right me-2 text-primary"></i>
                                    توضیحات
                                </label>
                                <textarea class="form-control @error('unitDesc') is-invalid @enderror" 
                                          wire:model="unitDesc"
                                          rows="3"
                                          placeholder="توضیحات اختیاری..."
                                          style="border-radius: 10px;"></textarea>
                                @error('unitDesc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2 justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary btn-lg" wire:click="closeUnitModal" style="border-radius: 10px;">
                                    <i class="fas fa-times me-2"></i>
                                    انصراف
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 10px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none;">
                                    <i class="fas fa-save me-2"></i>
                                    ذخیره
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Password Modal --}}
    @if($showPasswordModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:click.self="closePasswordModal">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); border: none; padding: 20px;">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-lock me-2"></i>
                            تایید رمز
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closePasswordModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="fas fa-key me-2 text-primary"></i>
                                رمز <span class="text-danger">*</span>
                            </label>
                            <input type="password" 
                                   class="form-control form-control-lg @error('passwordInput') is-invalid @enderror" 
                                   wire:model="passwordInput"
                                   wire:keydown.enter="verifyPassword"
                                   placeholder="رمز را وارد کنید"
                                   autofocus
                                   style="border-radius: 10px;">
                            @error('passwordInput')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4">
                            <button type="button" class="btn btn-secondary btn-lg" wire:click="closePasswordModal" style="border-radius: 10px;">
                                <i class="fas fa-times me-2"></i>
                                انصراف
                            </button>
                            <button type="button" class="btn btn-primary btn-lg" wire:click="verifyPassword" style="border-radius: 10px; background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); border: none;">
                                <i class="fas fa-check me-2"></i>
                                تایید
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Room Modal --}}
    @if($showRoomModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:click.self="closeRoomModal">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                    <div class="modal-header text-white" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; padding: 20px;">
                        <h5 class="modal-title fw-bold">
                            <i class="fas fa-door-open me-2"></i>
                            {{ $editingRoomId ? 'ویرایش اتاق' : 'افزودن اتاق جدید' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeRoomModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form wire:submit.prevent="saveRoom">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tag me-2 text-primary"></i>
                                    نام اتاق <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control form-control-lg @error('roomName') is-invalid @enderror" 
                                       wire:model="roomName"
                                       placeholder="مثال: اتاق 101"
                                       style="border-radius: 10px;">
                                @error('roomName')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-hashtag me-2 text-primary"></i>
                                    کد اتاق
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg @error('roomCode') is-invalid @enderror" 
                                       wire:model="roomCode"
                                       placeholder="مثال: 101 (اختیاری)"
                                       style="border-radius: 10px;">
                                @error('roomCode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-bed me-2 text-primary"></i>
                                    تعداد تخت <span class="text-danger">*</span>
                                </label>
                                <input type="number" 
                                       class="form-control form-control-lg @error('bedCount') is-invalid @enderror" 
                                       wire:model="bedCount"
                                       min="1"
                                       max="100"
                                       placeholder="مثال: 2"
                                       style="border-radius: 10px;">
                                @error('bedCount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-align-right me-2 text-primary"></i>
                                    توضیحات
                                </label>
                                <textarea class="form-control @error('roomDesc') is-invalid @enderror" 
                                          wire:model="roomDesc"
                                          rows="3"
                                          placeholder="توضیحات اختیاری..."
                                          style="border-radius: 10px;"></textarea>
                                @error('roomDesc')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex gap-2 justify-content-end mt-4">
                                <button type="button" class="btn btn-secondary btn-lg" wire:click="closeRoomModal" style="border-radius: 10px;">
                                    <i class="fas fa-times me-2"></i>
                                    انصراف
                                </button>
                                <button type="submit" class="btn btn-primary btn-lg" style="border-radius: 10px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none;">
                                    <i class="fas fa-save me-2"></i>
                                    ذخیره
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @script
    <script>
        // Handle delete confirmation for units
        Livewire.on('confirmDelete', (data) => {
            const { id, type } = data[0];
            const itemName = type === 'unit' ? 'واحد' : 'اتاق';
            
            cuteAlert({
                type: 'warning',
                title: 'حذف ' + itemName,
                description: `آیا از حذف این ${itemName} مطمئن هستید؟ این عمل قابل بازگشت نیست.`,
                primaryButtonText: 'بله، حذف کن',
                secondaryButtonText: 'انصراف'
            }).then((result) => {
                if (result === 'primaryButtonClicked') {
                    if (type === 'unit') {
                        Livewire.dispatch('delete-unit-confirmed', { unitId: id });
                    } else {
                        Livewire.dispatch('delete-room-confirmed', { roomId: id });
                    }
                }
            });
        });

        // Handle show-toast event
        window.addEventListener('show-toast', (event) => {
            const params = event.detail[0];

            if (typeof window.cuteToast === 'function') {
                cuteToast({
                    type: params.type,
                    title: params.title,
                    description: params.description,
                    timer: params.timer
                });
            } else {
                console.error('cuteToast function is not available on window object.');
            }
        });
    </script>
    @endscript
</div>

