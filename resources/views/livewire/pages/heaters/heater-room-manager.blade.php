<div>
    <div class="container-fluid px-4 py-4" dir="rtl">
        {{-- Modern Header --}}
        <div class="heater-header-modern">
            <div class="heater-header-content">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="heater-icon-modern">
                            <i class="fas fa-fire"></i>
                        </div>
                        <div class="ms-3">
                            <h2 class="heater-title-modern mb-1">مدیریت هیترها</h2>
                            <p class="heater-subtitle-modern mb-0">سامانه مدیریت هیترها و اتاق‌ها</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button wire:click="openHeaterModal()" class="heater-btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            افزودن هیتر
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="heater-filters-card">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="heater-input-wrapper">
                        <label class="heater-label">
                            <i class="fas fa-search me-2"></i>جستجوی هیتر
                        </label>
                        <div class="heater-input-group">
                            <i class="fas fa-search input-icon-heater"></i>
                            <input type="text" wire:model.live.debounce.500ms="searchHeater" 
                                   class="heater-input" placeholder="نام، شماره، مدل یا شماره سریال...">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="heater-input-wrapper">
                        <label class="heater-label">
                            <i class="fas fa-filter me-2"></i>وضعیت
                        </label>
                        <select wire:model.live="filterStatus" class="heater-select">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                            <option value="maintenance">تعمیرات</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="heater-input-wrapper">
                        <label class="heater-label">
                            <i class="fas fa-building me-2"></i>واحد
                        </label>
                        <select wire:model.live="filterUnit" class="heater-select">
                            <option value="">همه واحدها</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Heaters Table/Grid --}}
        @if($filteredHeaters->isEmpty())
            <div class="heater-empty-state">
                <div class="empty-icon-heater">
                    <i class="fas fa-fire"></i>
                </div>
                <h4 class="empty-title-heater">هیچ هیتری یافت نشد</h4>
                <p class="empty-desc-heater">برای شروع، اولین هیتر خود را ایجاد کنید</p>
                <button wire:click="openHeaterModal()" class="heater-btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    افزودن اولین هیتر
                </button>
            </div>
        @else
            <div class="row g-4">
                @foreach($filteredHeaters as $heater)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="heater-card-modern">
                            {{-- Card Header --}}
                            <div class="heater-card-header">
                                <div class="heater-header-info">
                                    <div class="heater-avatar-modern">
                                        <i class="fas fa-fire"></i>
                                    </div>
                                    <div class="heater-info-modern">
                                        <h4 class="heater-name-modern">{{ $heater->name }}</h4>
                                        @if($heater->number)
                                            <div class="heater-number-tag">
                                                <i class="fas fa-hashtag"></i>
                                                <span>{{ $heater->number }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="heater-dropdown-wrapper" data-dropdown-id="heater-{{ $heater->id }}">
                                    <button class="heater-menu-btn" type="button" onclick="toggleDropdown('heater-{{ $heater->id }}')">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="heater-dropdown-menu" id="dropdown-heater-{{ $heater->id }}" style="display: none;">
                                        <li>
                                            <a href="#" wire:click="openHeaterModal({{ $heater->id }})" 
                                               onclick="closeDropdown('heater-{{ $heater->id }}')"
                                               class="dropdown-item-heater">
                                                <i class="fas fa-edit"></i>
                                                <span>ویرایش</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" wire:click="confirmDeleteHeater({{ $heater->id }})" 
                                               onclick="event.preventDefault(); closeDropdown('heater-{{ $heater->id }}');"
                                               class="dropdown-item-heater danger">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>حذف</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="heater-card-body">
                                {{-- Status Badge --}}
                                <div class="mb-3">
                                    <span class="status-badge-heater status-{{ $heater->status }}">
                                        @if($heater->status === 'active')
                                            <i class="fas fa-check-circle me-1"></i>
                                        @elseif($heater->status === 'inactive')
                                            <i class="fas fa-times-circle me-1"></i>
                                        @else
                                            <i class="fas fa-tools me-1"></i>
                                        @endif
                                        {{ $heater->status_label }}
                                    </span>
                                </div>

                                {{-- Heater Details --}}
                                <div class="heater-details-list">
                                    @if($heater->model)
                                        <div class="heater-detail-item">
                                            <i class="fas fa-tag"></i>
                                            <span class="heater-detail-label">مدل:</span>
                                            <span class="heater-detail-value">{{ $heater->model }}</span>
                                        </div>
                                    @endif
                                    @if($heater->serial_number)
                                        <div class="heater-detail-item">
                                            <i class="fas fa-barcode"></i>
                                            <span class="heater-detail-label">شماره سریال:</span>
                                            <span class="heater-detail-value">{{ $heater->serial_number }}</span>
                                        </div>
                                    @endif
                                    @if($heater->installation_date)
                                        <div class="heater-detail-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span class="heater-detail-label">تاریخ نصب:</span>
                                            <span class="heater-detail-value">{{ $heater->installation_date->format('Y/m/d') }}</span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Room Assignment --}}
                                <div class="heater-room-section">
                                    <div class="heater-room-title-wrapper">
                                        <div class="heater-room-icon">
                                            <i class="fas fa-door-open"></i>
                                        </div>
                                        <div>
                                            <h6 class="heater-room-title">اتاق اختصاصی</h6>
                                        </div>
                                    </div>

                                    @if($heater->room)
                                        <div class="heater-room-assigned">
                                            <div class="heater-room-chip">
                                                <div class="heater-room-chip-content">
                                                    <div class="heater-room-chip-icon">
                                                        <i class="fas fa-door-open"></i>
                                                    </div>
                                                    <div class="heater-room-chip-info">
                                                        <span class="heater-room-chip-name">{{ $heater->room->name }}</span>
                                                        @if($heater->room->unit)
                                                            <span class="heater-room-chip-type">{{ $heater->room->unit->name }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="heater-empty-room">
                                            <i class="fas fa-door-open"></i>
                                            <p>هیچ اتاقی اختصاص داده نشده</p>
                                        </div>
                                    @endif
                                </div>

                                @if($heater->desc)
                                    <div class="heater-description">
                                        <p>{{ $heater->desc }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $filteredHeaters->links() }}
            </div>
        @endif
    </div>

    {{-- Heater Modal --}}
    @if($showHeaterModal)
        <div class="heater-modal-overlay" wire:ignore.self wire:click="closeHeaterModal">
            <div class="heater-modal-container" wire:click.stop>
                <div class="heater-modal">
                    <form wire:submit.prevent="saveHeater">
                        <div class="heater-modal-header">
                            <div class="heater-modal-header-content">
                                <div class="heater-modal-icon">
                                    <i class="fas fa-fire"></i>
                                </div>
                                <div>
                                    <h5 class="heater-modal-title">{{ $editingHeaterId ? 'ویرایش هیتر' : 'افزودن هیتر جدید' }}</h5>
                                </div>
                            </div>
                            <button type="button" class="heater-modal-close" wire:click="closeHeaterModal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="heater-modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">
                                            نام هیتر <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model.defer="heaterName"
                                               class="heater-input @error('heaterName') is-invalid @enderror"
                                               placeholder="نام هیتر را وارد کنید">
                                        @error('heaterName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">
                                            شماره هیتر
                                        </label>
                                        <input type="number" wire:model.defer="heaterNumber"
                                               class="heater-input @error('heaterNumber') is-invalid @enderror"
                                               placeholder="شماره هیتر (اختیاری)">
                                        @error('heaterNumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">وضعیت</label>
                                        <select wire:model.defer="heaterStatus" class="heater-select">
                                            <option value="active">فعال</option>
                                            <option value="inactive">غیرفعال</option>
                                            <option value="maintenance">تعمیرات</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">اتاق</label>
                                        <select wire:model.defer="heaterRoomId" class="heater-select @error('heaterRoomId') is-invalid @enderror">
                                            <option value="">انتخاب اتاق (اختیاری)</option>
                                            @foreach($rooms as $room)
                                                <option value="{{ $room->id }}">
                                                    {{ $room->name }}
                                                    @if($room->unit) - {{ $room->unit->name }} @endif
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('heaterRoomId')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        @if($errors->has('heaterRoomId'))
                                            <small class="text-danger d-block mt-1">{{ $errors->first('heaterRoomId') }}</small>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">مدل</label>
                                        <input type="text" wire:model.defer="heaterModel"
                                               class="heater-input" placeholder="مدل هیتر (اختیاری)">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">شماره سریال</label>
                                        <input type="text" wire:model.defer="heaterSerialNumber"
                                               class="heater-input @error('heaterSerialNumber') is-invalid @enderror"
                                               placeholder="شماره سریال (اختیاری)">
                                        @error('heaterSerialNumber')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">تاریخ نصب</label>
                                        <input type="date" wire:model.defer="heaterInstallationDate"
                                               class="heater-input">
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="heater-input-wrapper">
                                        <label class="heater-label">توضیحات</label>
                                        <textarea wire:model.defer="heaterDesc" class="heater-input" rows="3"
                                                  placeholder="توضیحات اختیاری..."></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="heater-modal-footer">
                            <div>
                                <button type="button" class="heater-btn-secondary me-2" wire:click="closeHeaterModal">
                                    انصراف
                                </button>
                                <button type="submit" class="heater-btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <span wire:loading.remove wire:target="saveHeater">ذخیره</span>
                                    <span wire:loading wire:target="saveHeater" class="spinner-border spinner-border-sm"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
    <script>
        // Dropdown Functions - Define globally
        window.toggleDropdown = function(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            if (!dropdown) return;
            
            const allDropdowns = document.querySelectorAll('.heater-dropdown-menu');
            
            allDropdowns.forEach(d => {
                if (d.id !== 'dropdown-' + id) {
                    d.style.display = 'none';
                }
            });
            
            dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
        };

        window.closeDropdown = function(id) {
            const dropdown = document.getElementById('dropdown-' + id);
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        };

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.heater-dropdown-wrapper')) {
                document.querySelectorAll('.heater-dropdown-menu').forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });

        // Listen for delete confirmation
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('confirm-delete-heater', (data) => {
                const heaterId = data.heaterId;
                const heaterName = data.heaterName;
                if (confirm('آیا از حذف هیتر "' + heaterName + '" اطمینان دارید؟')) {
                    @this.call('deleteHeater', heaterId);
                }
            });
        });
    </script>
    @endpush

    <style>
        :root {
            --heater-primary: #f97316;
            --heater-secondary: #ea580c;
            --heater-info: #fb923c;
            --heater-dark: #1f2937;
            --heater-light: #f1f5f9;
            --heater-border: #e2e8f0;
            --heater-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --heater-shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --heater-shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Header */
        .heater-header-modern {
            background: linear-gradient(135deg, var(--heater-primary) 0%, var(--heater-secondary) 100%) !important;
            border-radius: 20px !important;
            padding: 32px !important;
            margin-bottom: 24px !important;
            box-shadow: var(--heater-shadow-lg) !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .heater-header-modern::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            border-radius: 50%;
        }

        .heater-header-content {
            position: relative;
            z-index: 1;
        }

        .heater-icon-modern {
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

        .heater-title-modern {
            color: white !important;
            font-size: 28px !important;
            font-weight: 700 !important;
            margin: 0 !important;
        }

        .heater-subtitle-modern {
            color: rgba(255, 255, 255, 0.9) !important;
            font-size: 14px !important;
        }

        .heater-btn-primary {
            background: white !important;
            color: var(--heater-primary) !important;
            border: none !important;
            padding: 12px 24px !important;
            border-radius: 12px !important;
            font-weight: 600 !important;
            font-size: 14px !important;
            transition: all 0.3s ease !important;
            box-shadow: var(--heater-shadow-md) !important;
        }

        .heater-btn-primary:hover {
            transform: translateY(-2px) !important;
            box-shadow: var(--heater-shadow-lg) !important;
            color: var(--heater-primary) !important;
        }

        .heater-btn-secondary {
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

        .heater-btn-secondary:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }

        /* Filters */
        .heater-filters-card {
            background: white;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: var(--heater-shadow);
            border: 1px solid var(--heater-border);
        }

        .heater-input-wrapper {
            margin-bottom: 0;
        }

        .heater-label {
            display: block;
            color: var(--heater-dark);
            font-weight: 600;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .heater-input-group {
            position: relative;
        }

        .input-icon-heater {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 1;
        }

        .heater-input, .heater-select {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid var(--heater-border);
            border-radius: 12px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
            color: var(--heater-dark);
        }

        .heater-input-group .heater-input {
            padding-right: 48px;
        }

        .heater-input:focus, .heater-select:focus {
            outline: none;
            border-color: var(--heater-primary);
            box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
        }

        /* Heater Card */
        .heater-card-modern {
            background: white !important;
            border-radius: 20px !important;
            overflow: hidden !important;
            box-shadow: var(--heater-shadow) !important;
            border: 1px solid var(--heater-border) !important;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .heater-card-modern:hover {
            transform: translateY(-8px) !important;
            box-shadow: var(--heater-shadow-lg) !important;
        }

        .heater-card-header {
            background: linear-gradient(135deg, var(--heater-primary) 0%, var(--heater-secondary) 100%) !important;
            padding: 24px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: flex-start !important;
        }

        .heater-header-info {
            display: flex;
            align-items: center;
            gap: 16px;
            flex: 1;
        }

        .heater-avatar-modern {
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

        .heater-info-modern {
            flex: 1;
        }

        .heater-name-modern {
            color: white !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            margin: 0 0 8px 0 !important;
        }

        .heater-number-tag {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 8px;
            font-size: 12px;
            color: white;
        }

        .heater-menu-btn {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .heater-menu-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .heater-dropdown-wrapper {
            position: relative;
        }

        .heater-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border-radius: 12px;
            box-shadow: var(--heater-shadow-lg);
            padding: 8px;
            margin-top: 8px;
            min-width: 160px;
            z-index: 1000;
            list-style: none;
        }

        .dropdown-item-heater {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 8px;
            color: var(--heater-dark);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .dropdown-item-heater:hover {
            background: var(--heater-light);
        }

        .dropdown-item-heater.danger {
            color: #ef4444;
        }

        .dropdown-item-heater.danger:hover {
            background: #fee2e2;
        }

        .heater-card-body {
            padding: 24px !important;
            flex: 1;
        }

        .status-badge-heater {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-badge-heater.status-active {
            background: #dcfce7;
            color: #166534;
        }

        .status-badge-heater.status-inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-badge-heater.status-maintenance {
            background: #fef3c7;
            color: #92400e;
        }

        .heater-details-list {
            margin-bottom: 20px;
        }

        .heater-detail-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 0;
            font-size: 14px;
        }

        .heater-detail-item i {
            color: var(--heater-primary);
            width: 20px;
        }

        .heater-detail-label {
            color: #64748b;
            font-weight: 500;
        }

        .heater-detail-value {
            color: var(--heater-dark);
            font-weight: 600;
        }

        .heater-room-section {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid var(--heater-border);
        }

        .heater-room-title-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
        }

        .heater-room-icon {
            width: 32px;
            height: 32px;
            background: var(--heater-light);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--heater-primary);
        }

        .heater-room-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--heater-dark);
            margin: 0;
        }

        .heater-room-assigned {
            margin-top: 12px;
        }

        .heater-room-chip {
            background: var(--heater-light);
            border: 2px solid var(--heater-border);
            border-radius: 12px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .heater-room-chip:hover {
            border-color: var(--heater-primary);
            background: #fff7ed;
        }

        .heater-room-chip-content {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .heater-room-chip-icon {
            width: 36px;
            height: 36px;
            background: var(--heater-primary);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .heater-room-chip-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .heater-room-chip-name {
            font-weight: 600;
            color: var(--heater-dark);
            font-size: 14px;
        }

        .heater-room-chip-type {
            font-size: 12px;
            color: #64748b;
        }

        .heater-empty-room {
            text-align: center;
            padding: 24px;
            color: #94a3b8;
        }

        .heater-empty-room i {
            font-size: 32px;
            margin-bottom: 8px;
            display: block;
        }

        .heater-empty-room p {
            margin: 0;
            font-size: 14px;
        }

        .heater-description {
            margin-top: 16px;
            padding-top: 16px;
            border-top: 1px solid var(--heater-border);
        }

        .heater-description p {
            color: #64748b;
            font-size: 13px;
            margin: 0;
            line-height: 1.6;
        }

        /* Empty State */
        .heater-empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
            box-shadow: var(--heater-shadow);
        }

        .empty-icon-heater {
            width: 120px;
            height: 120px;
            background: var(--heater-light);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 24px;
            color: var(--heater-primary);
            font-size: 48px;
        }

        .empty-title-heater {
            color: var(--heater-dark);
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .empty-desc-heater {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 24px;
        }

        /* Modal */
        .heater-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }

        .heater-modal-container {
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .heater-modal {
            background: white;
            border-radius: 20px;
            box-shadow: var(--heater-shadow-lg);
            overflow: hidden;
        }

        .heater-modal-header {
            background: linear-gradient(135deg, var(--heater-primary) 0%, var(--heater-secondary) 100%);
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .heater-modal-header-content {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .heater-modal-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.25);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
        }

        .heater-modal-title {
            color: white;
            font-size: 20px;
            font-weight: 700;
            margin: 0;
        }

        .heater-modal-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .heater-modal-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .heater-modal-body {
            padding: 24px;
        }

        .heater-modal-footer {
            padding: 24px;
            border-top: 1px solid var(--heater-border);
            display: flex;
            justify-content: flex-end;
        }

        .invalid-feedback {
            display: block;
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
        }
    </style>
</div>
