<div>
    <div class="container-fluid px-4 py-4" dir="rtl">
        {{-- Modern Header --}}
        <div class="cooler-header-modern">
            <div class="cooler-header-content">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="cooler-icon-modern">
                            <i class="fas fa-wind"></i>
                            </div>
                        <div class="ms-3">
                            <h2 class="cooler-title-modern mb-1">مدیریت کولرها</h2>
                            <p class="cooler-subtitle-modern mb-0">سامانه مدیریت کولرها و اتصالات</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button wire:click="openCoolerModal()" class="cooler-btn-primary">
                                <i class="fas fa-plus me-2"></i>
                            افزودن کولر
                        </button>
                        <button wire:click="openConnectionModal()" class="cooler-btn-secondary">
                            <i class="fas fa-link me-2"></i>
                                اتصال جدید
                            </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="cooler-filters-card">
            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <div class="cooler-input-wrapper">
                        <label class="cooler-label">
                            <i class="fas fa-search me-2"></i>جستجوی کولر
                        </label>
                        <div class="cooler-input-group">
                            <i class="fas fa-search input-icon-cooler"></i>
                            <input type="text" wire:model.live="searchCooler" 
                                   class="cooler-input" placeholder="نام، شماره یا مدل...">
                    </div>
                            </div>
                        </div>
                <div class="col-lg-2 col-md-6">
                    <div class="cooler-input-wrapper">
                        <label class="cooler-label">
                            <i class="fas fa-filter me-2"></i>وضعیت
                        </label>
                        <select wire:model.live="filterStatus" class="cooler-select">
                                <option value="">همه وضعیت‌ها</option>
                                <option value="active">فعال</option>
                                <option value="inactive">غیرفعال</option>
                                <option value="maintenance">تعمیرات</option>
                            </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="cooler-input-wrapper">
                        <label class="cooler-label">
                            <i class="fas fa-search me-2"></i>جستجوی اتاق
                        </label>
                        <div class="cooler-input-group">
                            <i class="fas fa-search input-icon-cooler"></i>
                            <input type="text" wire:model.live="searchRoom" 
                                   class="cooler-input" placeholder="نام یا کد اتاق...">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="cooler-input-wrapper">
                        <label class="cooler-label">
                            <i class="fas fa-building me-2"></i>واحد
                        </label>
                        <select wire:model.live="filterUnit" class="cooler-select">
                            <option value="">همه واحدها</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
                        </div>

        {{-- Coolers Grid --}}
        @if($this->filteredCoolers->isEmpty())
            <div class="cooler-empty-state">
                <div class="empty-icon-cooler">
                    <i class="fas fa-wind"></i>
                </div>
                <h4 class="empty-title-cooler">هیچ کولری یافت نشد</h4>
                <p class="empty-desc-cooler">برای شروع، اولین کولر خود را ایجاد کنید</p>
                <button wire:click="openCoolerModal()" class="cooler-btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    افزودن اولین کولر
                </button>
            </div>
        @else
            <div class="row g-4">
                @foreach($this->filteredCoolers as $cooler)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="cooler-card-modern" style="height: auto;">
                            {{-- Card Header --}}
                            <div class="cooler-card-header">
                                <div class="cooler-header-info">
                                    <div class="cooler-avatar-modern">
                                        <i class="fas fa-wind"></i>
                                    </div>
                                    <div class="cooler-info-modern">
                                        <h4 class="cooler-name-modern">{{ $cooler->name }}</h4>
                                                @if($cooler->number)
                                            <div class="cooler-number-tag">
                                                <i class="fas fa-hashtag"></i>
                                                <span>{{ $cooler->number }}</span>
                                            </div>
                                                @endif
                                                </div>
                                            </div>
                                <div class="cooler-dropdown-wrapper" data-dropdown-id="cooler-{{ $cooler->id }}">
                                    <button class="cooler-menu-btn" type="button" onclick="toggleDropdown('cooler-{{ $cooler->id }}')">
                                        <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                    <ul class="cooler-dropdown-menu" id="dropdown-cooler-{{ $cooler->id }}" style="display: none;">
                                        <li>
                                            <a href="#" wire:click="openCoolerModal({{ $cooler->id }})" 
                                               onclick="closeDropdown('cooler-{{ $cooler->id }}')"
                                               class="dropdown-item-cooler">
                                                <i class="fas fa-edit"></i>
                                                <span>ویرایش</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" wire:click="confirmDeleteCooler({{ $cooler->id }})" 
                                               onclick="event.preventDefault(); closeDropdown('cooler-{{ $cooler->id }}');"
                                               class="dropdown-item-cooler danger">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>حذف</span>
                                            </a>
                                        </li>
                                    </ul>
                                        </div>
                                    </div>

                            {{-- Card Body --}}
                            <div class="cooler-card-body">
                                {{-- Status Badge --}}
                                <div class="mb-3">
                                    <span class="status-badge-cooler status-{{ $cooler->status }}">
                                        @if($cooler->status === 'active')
                                            <i class="fas fa-check-circle me-1"></i>فعال
                                        @elseif($cooler->status === 'inactive')
                                            <i class="fas fa-times-circle me-1"></i>غیرفعال
                                        @else
                                            <i class="fas fa-tools me-1"></i>تعمیرات
                                        @endif
                                    </span>
                                </div>

                                {{-- Cooler Details --}}
                                @if($cooler->model)
                                    <div class="cooler-detail-chip">
                                        <div class="chip-icon-cooler blue">
                                            <i class="fas fa-cog"></i>
                                </div>
                                        <div class="chip-content-cooler">
                                            <span class="chip-label-cooler">مدل</span>
                                            <span class="chip-text-cooler">{{ $cooler->model }}</span>
                        </div>
                    </div>
                                @endif

                                @if($cooler->serial_number)
                                    <div class="cooler-detail-chip">
                                        <div class="chip-icon-cooler green">
                                            <i class="fas fa-barcode"></i>
                </div>
                                        <div class="chip-content-cooler">
                                            <span class="chip-label-cooler">شماره سریال</span>
                                            <span class="chip-text-cooler">{{ $cooler->serial_number }}</span>
            </div>
                                    </div>
                                @endif

                                @if($cooler->desc)
                                    <div class="cooler-detail-chip">
                                        <div class="chip-icon-cooler orange">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="chip-content-cooler">
                                            <span class="chip-label-cooler">توضیحات</span>
                                            <p class="chip-text-cooler mb-0">{{ Str::limit($cooler->desc, 80) }}</p>
                                        </div>
                                    </div>
                                @endif

            {{-- Rooms Section --}}
                                <div class="cooler-rooms-section">
                                    <div class="cooler-rooms-header">
                                        <div class="cooler-rooms-title-wrapper">
                                            <div class="cooler-rooms-icon">
                                                <i class="fas fa-door-open"></i>
                    </div>
                                            <div>
                                                <h6 class="cooler-rooms-title">اتاق‌های متصل</h6>
                                                <span class="cooler-rooms-count">{{ $cooler->rooms->count() }} اتاق</span>
                            </div>
                                        </div>
                                        <button class="cooler-btn-icon" wire:click="openConnectionModal({{ $cooler->id }})">
                                            <i class="fas fa-plus"></i>
                                        </button>
                        </div>

                                    @if($cooler->rooms->count() > 0)
                                        <div class="cooler-rooms-list">
                                            @foreach($cooler->rooms as $room)
                                                <div class="cooler-room-chip" 
                                                     wire:click="editConnection({{ $room->pivot->id }})">
                                                    <div class="cooler-room-chip-content">
                                                        <div class="cooler-room-chip-icon">
                                                            <i class="fas fa-door-open"></i>
                                                        </div>
                                                        <div class="cooler-room-chip-info">
                                                            <span class="cooler-room-chip-name">{{ $room->name }}</span>
                                                            <span class="cooler-room-chip-type">
                                                                {{ $room->pivot->connection_type === 'direct' ? 'مستقیم' : 
                                                                   ($room->pivot->connection_type === 'duct' ? 'کانالی' : 'مرکزی') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <button class="cooler-room-chip-delete" 
                                                            wire:click.stop="confirmDelete({{ $room->pivot->id }})"
                                                            onclick="event.preventDefault();">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                @endforeach
                        </div>
                                    @else
                                        <div class="cooler-empty-rooms">
                                            <i class="fas fa-door-open"></i>
                                            <p>هیچ اتاقی متصل نیست</p>
                                            <button class="cooler-btn-text" wire:click="openConnectionModal({{ $cooler->id }})">
                                                افزودن اتاق
                                            </button>
                                        </div>
                                                @endif
                                                </div>
                                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Cooler Modal --}}
    @if($showCoolerModal)
        <div class="cooler-modal-overlay" wire:ignore.self>
            <div class="cooler-modal-container">
                <div class="cooler-modal">
                    <form wire:submit.prevent="saveCooler">
                        <div class="cooler-modal-header">
                            <div class="cooler-modal-header-content">
                                <div class="cooler-modal-icon">
                                    <i class="fas fa-wind"></i>
                                </div>
                                <div>
                                    <h5 class="cooler-modal-title">{{ $editingCoolerId ? 'ویرایش کولر' : 'افزودن کولر جدید' }}</h5>
                                </div>
                            </div>
                            <button type="button" class="cooler-modal-close" wire:click="closeCoolerModal">
                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                        <div class="cooler-modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">
                                            نام کولر <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model.defer="coolerName"
                                               class="cooler-input @error('coolerName') is-invalid @enderror"
                                               placeholder="نام کولر را وارد کنید">
                                        @error('coolerName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">
                                            شماره کولر <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" wire:model.defer="coolerNumber"
                                               class="cooler-input @error('coolerNumber') is-invalid @enderror"
                                               placeholder="شماره کولر را وارد کنید">
                                        @error('coolerNumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                </div>
                        </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">وضعیت</label>
                                        <select wire:model.defer="coolerStatus" class="cooler-select">
                                            <option value="active">فعال</option>
                                            <option value="inactive">غیرفعال</option>
                                            <option value="maintenance">تعمیرات</option>
                                        </select>
                    </div>
                </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">مدل</label>
                                        <input type="text" wire:model.defer="coolerModel"
                                               class="cooler-input" placeholder="مدل کولر (اختیاری)">
            </div>
                        </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">شماره سریال</label>
                                        <input type="text" wire:model.defer="coolerSerialNumber"
                                               class="cooler-input @error('coolerSerialNumber') is-invalid @enderror"
                                               placeholder="شماره سریال (اختیاری)">
                                        @error('coolerSerialNumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                                    </div>
                                                    </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">تاریخ نصب</label>
                                        <input type="date" wire:model.defer="coolerInstallationDate"
                                               class="cooler-input">
                                                </div>
                                                </div>
                                <div class="col-12">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">توضیحات</label>
                                        <textarea wire:model.defer="coolerDesc" class="cooler-input" rows="3"
                                                  placeholder="توضیحات اختیاری..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                        <div class="cooler-modal-footer">
                            <div>
                                <button type="button" class="cooler-btn-secondary me-2" wire:click="closeCoolerModal">
                                    انصراف
                                </button>
                                <button type="submit" class="cooler-btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <span wire:loading.remove wire:target="saveCooler">ذخیره</span>
                                    <span wire:loading wire:target="saveCooler" class="spinner-border spinner-border-sm"></span>
                                </button>
                        </div>
                    </div>
                    </form>
            </div>
        </div>
        </div>
    @endif

        {{-- Connection Modal --}}
        @if($showConnectionModal)
        <div class="cooler-modal-overlay" wire:ignore.self>
            <div class="cooler-modal-container">
                <div class="cooler-modal">
                            <form wire:submit.prevent="saveConnection">
                        <div class="cooler-modal-header">
                            <div class="cooler-modal-header-content">
                                <div class="cooler-modal-icon">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div>
                                    <h5 class="cooler-modal-title">{{ $editingConnection ? 'ویرایش اتصال' : 'اتصال جدید' }}</h5>
                                </div>
                            </div>
                            <button type="button" class="cooler-modal-close" wire:click="closeConnectionModal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="cooler-modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">
                                            کولر <span class="text-danger">*</span>
                                        </label>
                                        <select wire:model="selectedCooler" 
                                                class="cooler-select @error('selectedCooler') is-invalid @enderror">
                                            <option value="">انتخاب کولر...</option>
                                            @foreach($coolers as $cooler)
                                                <option value="{{ $cooler->id }}">
                                                    {{ $cooler->name }}
                                                    @if($cooler->number) ({{ $cooler->number }})
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedCooler')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">
                                            اتاق <span class="text-danger">*</span>
                                        </label>
                                        @if($editingConnection)
                                            <input type="text" class="cooler-input"
                                                   value="{{ \App\Models\Room::find($selectedRoom)->name ?? '' }}" readonly>
                                        @else
                                            <select wire:model="selectedRoom" multiple
                                                    class="cooler-select @error('selectedRoom') is-invalid @enderror"
                                                    size="8">
                                                <option value="">یک یا چند اتاق را انتخاب کنید...</option>
                                                @foreach($this->filteredRooms as $room)
                                                <option value="{{ $room->id }}">
                                                    {{ $room->name }} - {{ $room->unit->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedRoom')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                            <small class="text-muted d-block mt-2">
                                                <i class="fas fa-info-circle me-1"></i>
                                                برای انتخاب چند اتاق، Ctrl (یا Cmd در Mac) را نگه دارید
                                            </small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">نوع اتصال</label>
                                        <select wire:model="connectionType" class="cooler-select">
                                            <option value="direct">مستقیم</option>
                                            <option value="duct">کانالی</option>
                                            <option value="central">مرکزی</option>
                                        </select>
                                    </div>
                                    </div>
                                <div class="col-md-6">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">تاریخ اتصال</label>
                                        <input type="date" wire:model="connectedAt"
                                               class="cooler-input">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="cooler-input-wrapper">
                                        <label class="cooler-label">یادداشت</label>
                                        <textarea wire:model="notes" class="cooler-input" rows="3"
                                                  placeholder="یادداشت اختیاری..."></textarea>
                        </div>
                        </div>
                    </div>
                </div>
                        <div class="cooler-modal-footer">
                            <div>
                                @if($editingConnection)
                                    <button type="button" class="cooler-btn-danger" wire:click="confirmDelete({{ $editingConnection }})"
                                            onclick="event.preventDefault();">
                                        <i class="fas fa-trash me-2"></i>
                                        حذف اتصال
                                    </button>
        @endif
                        </div>
                            <div>
                                <button type="button" class="cooler-btn-secondary me-2" wire:click="closeConnectionModal">
                                انصراف
                            </button>
                                <button type="submit" class="cooler-btn-primary">
                                <i class="fas fa-save me-2"></i>
                                    <span wire:loading.remove wire:target="saveConnection">ذخیره</span>
                                    <span wire:loading wire:target="saveConnection" class="spinner-border spinner-border-sm"></span>
                            </button>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
    </div>
    @endif

    @push('styles')
        <style>
            /* Cooler Modern Material Design */
            :root {
                --cooler-primary: #0ea5e9;
                --cooler-primary-dark: #0284c7;
                --cooler-secondary: #06b6d4;
                --cooler-success: #10b981;
                --cooler-danger: #ef4444;
                --cooler-warning: #f59e0b;
                --cooler-info: #3b82f6;
                --cooler-dark: #1f2937;
                --cooler-light: #f1f5f9;
                --cooler-border: #e2e8f0;
                --cooler-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --cooler-shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --cooler-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            /* Header */
            .cooler-header-modern {
                background: linear-gradient(135deg, var(--cooler-primary) 0%, var(--cooler-secondary) 100%) !important;
                border-radius: 20px !important;
                padding: 32px !important;
                margin-bottom: 24px !important;
                box-shadow: var(--cooler-shadow-lg) !important;
                position: relative !important;
                overflow: hidden !important;
            }

            .cooler-header-modern::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -20%;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
                border-radius: 50%;
            }

            .cooler-header-content {
                position: relative;
                z-index: 1;
            }

            .cooler-icon-modern {
                width: 64px;
                height: 64px;
                background: rgba(255, 255, 255, 0.25);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                color: white;
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            }

            .cooler-title-modern {
                color: white !important;
                font-size: 28px !important;
                font-weight: 700 !important;
                margin: 0 !important;
            }

            .cooler-subtitle-modern {
                color: rgba(255, 255, 255, 0.9) !important;
                font-size: 14px !important;
            }

            .cooler-btn-primary {
                background: white !important;
                color: var(--cooler-primary) !important;
                border: none !important;
                padding: 12px 24px !important;
                border-radius: 12px !important;
                font-weight: 600 !important;
                font-size: 14px !important;
                transition: all 0.3s ease !important;
                box-shadow: var(--cooler-shadow-md) !important;
            }

            .cooler-btn-primary:hover {
                transform: translateY(-2px) !important;
                box-shadow: var(--cooler-shadow-lg) !important;
                color: var(--cooler-primary) !important;
            }

            .cooler-btn-secondary {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border: 2px solid rgba(255, 255, 255, 0.3);
                padding: 12px 24px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .cooler-btn-secondary:hover {
                background: rgba(255, 255, 255, 0.3);
                border-color: rgba(255, 255, 255, 0.5);
                color: white;
            }

            /* Filters */
            .cooler-filters-card {
                background: white;
                border-radius: 16px;
                padding: 24px;
                margin-bottom: 24px;
                box-shadow: var(--cooler-shadow);
                border: 1px solid var(--cooler-border);
            }

            .cooler-input-wrapper {
                margin-bottom: 0;
            }

            .cooler-label {
                display: block;
                color: var(--cooler-dark);
                font-weight: 600;
                font-size: 13px;
                margin-bottom: 8px;
            }

            .cooler-input-group {
                position: relative;
            }

            .input-icon-cooler {
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: #94a3b8;
                z-index: 1;
            }

            .cooler-input, .cooler-select {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid var(--cooler-border);
                border-radius: 12px;
                font-size: 14px;
                transition: all 0.3s ease;
                background: white;
                color: var(--cooler-dark);
            }

            .cooler-input-group .cooler-input {
                padding-right: 48px;
            }

            .cooler-input:focus, .cooler-select:focus {
                outline: none;
                border-color: var(--cooler-primary);
                box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
            }

            /* Cooler Card */
            .cooler-card-modern {
                background: white !important;
                border-radius: 20px !important;
                overflow: hidden !important;
                box-shadow: var(--cooler-shadow) !important;
                border: 1px solid var(--cooler-border) !important;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
                display: flex !important;
                flex-direction: column !important;
            }

            .cooler-card-modern:hover {
                transform: translateY(-8px) !important;
                box-shadow: var(--cooler-shadow-lg) !important;
            }

            .cooler-card-header {
                background: linear-gradient(135deg, var(--cooler-primary) 0%, var(--cooler-secondary) 100%) !important;
                padding: 24px !important;
                display: flex !important;
                justify-content: space-between !important;
                align-items: flex-start !important;
            }

            .cooler-header-info {
                display: flex;
                align-items: center;
                gap: 16px;
                flex: 1;
            }

            .cooler-avatar-modern {
                width: 56px;
                height: 56px;
                background: rgba(255, 255, 255, 0.25);
                border-radius: 14px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 24px;
                color: white;
                backdrop-filter: blur(10px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            }

            .cooler-info-modern {
                flex: 1;
            }

            .cooler-name-modern {
                color: white;
                font-size: 20px;
                font-weight: 700;
                margin: 0 0 8px 0;
            }

            .cooler-number-tag {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                padding: 6px 12px;
                border-radius: 8px;
                font-size: 12px;
                font-weight: 600;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                backdrop-filter: blur(10px);
            }

            .cooler-menu-btn {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
            }

            .cooler-menu-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .cooler-card-body {
                padding: 24px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            /* Status Badge */
            .status-badge-cooler {
                display: inline-flex;
                align-items: center;
                padding: 8px 16px;
                border-radius: 10px;
                font-size: 13px;
                font-weight: 600;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            .status-badge-cooler.status-active {
                background: linear-gradient(135deg, var(--cooler-success) 0%, #059669 100%);
                color: white;
            }

            .status-badge-cooler.status-inactive {
                background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
                color: white;
            }

            .status-badge-cooler.status-maintenance {
                background: linear-gradient(135deg, var(--cooler-warning) 0%, #d97706 100%);
                color: white;
            }

            /* Detail Chips */
            .cooler-detail-chip {
                background: var(--cooler-light);
                border: 2px solid var(--cooler-border);
                border-radius: 12px;
                padding: 12px;
                margin-bottom: 12px;
                display: flex;
                align-items: flex-start;
                gap: 12px;
                transition: all 0.3s ease;
            }

            .cooler-detail-chip:hover {
                border-color: var(--cooler-primary);
                box-shadow: var(--cooler-shadow-sm);
            }

            .chip-icon-cooler {
                width: 36px;
                height: 36px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 16px;
                flex-shrink: 0;
            }

            .chip-icon-cooler.blue {
                background: linear-gradient(135deg, var(--cooler-info) 0%, #2563eb 100%);
            }

            .chip-icon-cooler.green {
                background: linear-gradient(135deg, var(--cooler-success) 0%, #059669 100%);
            }

            .chip-icon-cooler.orange {
                background: linear-gradient(135deg, var(--cooler-warning) 0%, #d97706 100%);
            }

            .chip-content-cooler {
                flex: 1;
            }

            .chip-label-cooler {
                display: block;
                color: #64748b;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px;
            }

            .chip-text-cooler {
                color: var(--cooler-dark);
                font-size: 13px;
                font-weight: 500;
            }

            /* Rooms Section */
            .cooler-rooms-section {
                margin-top: auto;
                padding-top: 20px;
                border-top: 2px solid var(--cooler-border);
            }

            .cooler-rooms-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
            }

            .cooler-rooms-title-wrapper {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .cooler-rooms-icon {
                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, var(--cooler-success) 0%, #059669 100%);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }

            .cooler-rooms-title {
                color: var(--cooler-dark);
                font-size: 14px;
                font-weight: 700;
                margin: 0 0 2px 0;
            }

            .cooler-rooms-count {
                color: #64748b;
                font-size: 12px;
                font-weight: 500;
            }

            .cooler-btn-icon {
                background: linear-gradient(135deg, var(--cooler-success) 0%, #059669 100%);
                border: none;
                color: white;
                width: 36px;
                height: 36px;
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }

            .cooler-btn-icon:hover {
                transform: scale(1.1) rotate(90deg);
                box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
            }

            /* Room Chips */
            .cooler-rooms-list {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .cooler-room-chip {
                background: white;
                border: 2px solid var(--cooler-border);
                border-radius: 12px;
                padding: 12px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .cooler-room-chip:hover {
                border-color: var(--cooler-success);
                background: #f0fdf4;
                transform: translateX(-4px);
                box-shadow: var(--cooler-shadow-sm);
            }

            .cooler-room-chip-content {
                display: flex;
                align-items: center;
                gap: 12px;
                flex: 1;
            }

            .cooler-room-chip-icon {
                width: 32px;
                height: 32px;
                background: linear-gradient(135deg, var(--cooler-success) 0%, #059669 100%);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 14px;
            }

            .cooler-room-chip-info {
                flex: 1;
            }

            .cooler-room-chip-name {
                display: block;
                color: var(--cooler-dark);
                font-size: 14px;
                font-weight: 600;
                margin-bottom: 2px;
            }

            .cooler-room-chip-type {
                display: block;
                color: #64748b;
                font-size: 11px;
                font-weight: 500;
            }

            .cooler-room-chip-delete {
                background: transparent;
                border: none;
                color: var(--cooler-danger);
                width: 28px;
                height: 28px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .cooler-room-chip-delete:hover {
                background: #fee2e2;
                color: #dc2626;
                transform: scale(1.1);
            }

            /* Empty States */
            .cooler-empty-state {
                background: white;
                border-radius: 20px;
                padding: 64px 32px;
                text-align: center;
                box-shadow: var(--cooler-shadow);
            }

            .empty-icon-cooler {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, var(--cooler-primary) 0%, var(--cooler-secondary) 100%);
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 36px;
                margin-bottom: 24px;
                box-shadow: var(--cooler-shadow-lg);
            }

            .empty-title-cooler {
                color: var(--cooler-dark);
                font-size: 24px;
                font-weight: 700;
                margin-bottom: 8px;
            }

            .empty-desc-cooler {
                color: #64748b;
                font-size: 14px;
                margin-bottom: 24px;
            }

            .cooler-empty-rooms {
                text-align: center;
                padding: 32px 16px;
                background: var(--cooler-light);
                border-radius: 12px;
                border: 2px dashed var(--cooler-border);
            }

            .cooler-empty-rooms i {
                font-size: 32px;
                color: #94a3b8;
                margin-bottom: 12px;
            }

            .cooler-empty-rooms p {
                color: #64748b;
                font-size: 13px;
                margin-bottom: 16px;
            }

            .cooler-btn-text {
                background: transparent;
                border: 2px solid var(--cooler-primary);
                color: var(--cooler-primary);
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 13px;
                transition: all 0.3s ease;
            }

            .cooler-btn-text:hover {
                background: var(--cooler-primary);
                color: white;
            }

            /* Dropdown */
            .cooler-dropdown-wrapper {
                position: relative;
            }

            .cooler-dropdown-menu {
                position: absolute;
                top: 100%;
                left: 0;
                z-index: 1000;
                min-width: 180px;
                background: white;
                border-radius: 12px;
                box-shadow: var(--cooler-shadow-lg);
                border: none;
                padding: 8px;
                margin-top: 8px;
                list-style: none;
            }

            .dropdown-item-cooler {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 16px;
                border-radius: 8px;
                color: var(--cooler-dark);
                font-weight: 500;
                font-size: 14px;
                transition: all 0.2s;
            }

            .dropdown-item-cooler:hover {
                background: var(--cooler-light);
                color: var(--cooler-primary);
            }

            .dropdown-item-cooler.danger {
                color: var(--cooler-danger);
            }

            .dropdown-item-cooler.danger:hover {
                background: #fee2e2;
                color: #dc2626;
            }

            /* Modal */
            .cooler-modal-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 1050;
                padding: 20px;
                animation: fadeInCooler 0.3s ease;
            }

            @keyframes fadeInCooler {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .cooler-modal-container {
                width: 100%;
                max-width: 700px;
                animation: slideUpCooler 0.3s ease;
            }

            @keyframes slideUpCooler {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .cooler-modal {
                background: white;
                border-radius: 20px;
                box-shadow: var(--cooler-shadow-lg);
                overflow: hidden;
            }

            .cooler-modal-header {
                background: linear-gradient(135deg, var(--cooler-primary) 0%, var(--cooler-secondary) 100%);
                padding: 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .cooler-modal-header-content {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .cooler-modal-icon {
                width: 48px;
                height: 48px;
                background: rgba(255, 255, 255, 0.25);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 20px;
                backdrop-filter: blur(10px);
            }

            .cooler-modal-title {
                color: white;
                font-size: 20px;
                font-weight: 700;
                margin: 0;
            }

            .cooler-modal-close {
                background: rgba(255, 255, 255, 0.2);
                border: none;
                color: white;
                width: 36px;
                height: 36px;
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .cooler-modal-close:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .cooler-modal-body {
                padding: 24px;
            }

            .cooler-modal-footer {
                padding: 20px 24px;
                background: var(--cooler-light);
                border-top: 1px solid var(--cooler-border);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .cooler-btn-danger {
                background: linear-gradient(135deg, var(--cooler-danger) 0%, #dc2626 100%);
                border: none;
                color: white;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            }

            .cooler-btn-danger:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
                color: white;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .cooler-header-modern {
                    padding: 24px;
                }

                .cooler-card-header {
                    padding: 20px;
                }

                .cooler-card-body {
                    padding: 20px;
                }
            }
        </style>
    @endpush

    @push('scripts')
    <script>
        // Dropdown Functions
        function toggleDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            const allDropdowns = document.querySelectorAll('.cooler-dropdown-menu, .modern-dropdown-menu');
            
            // Close all other dropdowns
            allDropdowns.forEach(d => {
                if (d.id !== 'dropdown-' + id) {
                    d.style.display = 'none';
                }
            });
            
            // Toggle current dropdown
            if (dropdown) {
                dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
            }
        }

        function closeDropdown(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.cooler-dropdown-wrapper, .modern-dropdown-wrapper')) {
                document.querySelectorAll('.cooler-dropdown-menu, .modern-dropdown-menu').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        // Helper function to wait for cuteAlert to be available
        function waitForCuteAlert(callback, maxAttempts = 50) {
            if (typeof window.cuteAlert === 'function') {
                callback();
            } else if (maxAttempts > 0) {
                setTimeout(() => waitForCuteAlert(callback, maxAttempts - 1), 100);
            } else {
                console.error('cuteAlert function is not available on window object after waiting.');
            }
        }

        document.addEventListener('livewire:initialized', () => {
            // Handle delete confirmation for connections
            Livewire.on('confirmDelete', (data) => {
                const connectionId = Array.isArray(data) ? data[0].connectionId : data.connectionId;
                waitForCuteAlert(() => {
                    window.cuteAlert({
                        type: 'warning',
                        title: 'حذف اتصال',
                        description: 'آیا از حذف این اتصال مطمئن هستید؟',
                        timer: 5000,
                        primaryButtonText: 'تایید',
                        secondaryButtonText: 'انصراف'
                    }).then((e) => {
                        if (e === "primaryButtonClicked") {
                            Livewire.dispatch('delete-confirmed', { connectionId: connectionId });
                        }
                    });
                });
            });

            // Handle delete confirmation for coolers
            Livewire.on('confirm-delete-cooler', (data) => {
                const coolerId = data.coolerId;
                const coolerName = data.coolerName;
                waitForCuteAlert(() => {
                    window.cuteAlert({
                        type: 'warning',
                        title: 'حذف کولر',
                        description: 'آیا از حذف کولر \'' + coolerName + '\' مطمئن هستید؟ این عمل قابل بازگشت نیست.',
                        primaryButtonText: 'بله، حذف کن',
                        secondaryButtonText: 'انصراف'
                    }).then((result) => {
                        if (result === 'primaryButtonClicked') {
                            Livewire.dispatch('delete-cooler-confirmed', { coolerId: coolerId });
                        }
                    });
                });
            });

            // Handle confirmed cooler deletion
            Livewire.on('delete-cooler-confirmed', (data) => {
                const coolerId = data.coolerId;
                @this.call('deleteCooler', coolerId);
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
                }
            });
        });
    </script>
    @endpush
</div>
