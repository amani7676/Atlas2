<div>
    <div class="container-fluid px-4 py-4" dir="rtl">
        {{-- Modern Header --}}
        <div class="modern-header-wrapper">
            <div class="modern-header-content">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="modern-icon-circle">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="ms-3">
                            <h2 class="modern-title mb-1">مدیریت کلیدها</h2>
                            <p class="modern-subtitle mb-0">سامانه مدیریت و تخصیص کلیدها</p>
                    </div>
                    </div>
                    <button wire:click="prepareKeyCreate" class="modern-btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        افزودن کلید جدید
                    </button>
                    </div>
                </div>
            </div>

        {{-- Modern Filters --}}
        <div class="modern-filters-card">
            <div class="row g-3">
                    <div class="col-lg-4 col-md-6">
                    <div class="modern-input-wrapper">
                        <label class="modern-label">
                            <i class="fas fa-search me-2"></i>جستجو
                        </label>
                        <div class="modern-input-group">
                            <i class="fas fa-search input-icon"></i>
                            <input type="text" wire:model.live.debounce.300ms="search"
                                   class="modern-input" placeholder="نام، کد یا یادداشت...">
                        </div>
                    </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                    <div class="modern-input-wrapper">
                        <label class="modern-label">
                            <i class="fas fa-building me-2"></i>واحد
                        </label>
                        <select wire:model.live="selectedUnit" class="modern-select">
                            <option value="">همه واحدها</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    <div class="col-lg-2 col-md-6">
                    <div class="modern-input-wrapper">
                        <label class="modern-label">
                            <i class="fas fa-tags me-2"></i>نوع
                        </label>
                        <select wire:model.live="selectedType" class="modern-select">
                            <option value="">همه انواع</option>
                            <option value="room">🏠 اتاق</option>
                            <option value="reception">🏢 پذیرش</option>
                        </select>
                    </div>
                    </div>
                </div>
            </div>

        {{-- Keys Grid --}}
        @if($keys->isEmpty())
            <div class="modern-empty-state">
                <div class="empty-icon-circle">
                    <i class="fas fa-key"></i>
                </div>
                <h4 class="empty-title">هیچ کلیدی یافت نشد</h4>
                <p class="empty-description">برای شروع، اولین کلید خود را ایجاد کنید</p>
                <button wire:click="prepareKeyCreate" class="modern-btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    افزودن اولین کلید
                </button>
            </div>
        @else
            <div class="row g-4">
                            @foreach($keys as $key)
                    <div class="col-xl-4 col-lg-6 col-md-6">
                        <div class="modern-key-card" style="height: auto;">
                            {{-- Card Header --}}
                            <div class="key-card-top">
                                <div class="key-header-content">
                                    <div class="key-avatar">
                                        <i class="fas fa-key"></i>
                                    </div>
                                    <div class="key-info">
                                        <h4 class="key-name">{{ $key->name }}</h4>
                                        <div class="key-code-tag">
                                            <i class="fas fa-hashtag"></i>
                                            <span>{{ $key->code }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="modern-dropdown-wrapper" data-dropdown-id="key-{{ $key->id }}">
                                    <button class="modern-menu-btn" type="button" onclick="toggleDropdown('key-{{ $key->id }}')">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="modern-dropdown-menu" id="dropdown-key-{{ $key->id }}" style="display: none;">
                                        <li>
                                            <a href="#" wire:click="prepareKeyEdit({{ $key->id }})"
                                               onclick="closeDropdown('key-{{ $key->id }}')"
                                               class="dropdown-item-modern">
                                                <i class="fas fa-edit"></i>
                                                <span>ویرایش</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="#" wire:click="confirmRemoveKey({{ $key->id }})"
                                               onclick="event.preventDefault(); closeDropdown('key-{{ $key->id }}');"
                                               class="dropdown-item-modern danger">
                                                <i class="fas fa-trash-alt"></i>
                                                <span>حذف</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- Card Body --}}
                            <div class="key-card-content">
                                @if($key->desc)
                                    <div class="info-chip">
                                        <div class="chip-icon blue">
                                            <i class="fas fa-info-circle"></i>
                                        </div>
                                        <div class="chip-content">
                                            <span class="chip-label">توضیحات</span>
                                            <p class="chip-text">{{ Str::limit($key->desc, 80) }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($key->note)
                                    <div class="info-chip">
                                        <div class="chip-icon orange">
                                            <i class="fas fa-sticky-note"></i>
                                        </div>
                                        <div class="chip-content">
                                            <span class="chip-label">یادداشت</span>
                                            <span class="chip-text">{{ Str::limit($key->note, 50) }}</span>
                                        </div>
                                    </div>
                                @endif

                                {{-- Rooms Section --}}
                                <div class="rooms-section-modern">
                                    <div class="rooms-header">
                                        <div class="rooms-title-wrapper">
                                            <div class="rooms-icon-wrapper">
                                                <i class="fas fa-door-open"></i>
                                            </div>
                                            <div>
                                                <h6 class="rooms-title">اتاق‌های مرتبط</h6>
                                                <span class="rooms-count">{{ $key->rooms->count() }} اتاق</span>
                                            </div>
                                        </div>
                                        <button class="modern-btn-icon" wire:click="prepareAssignmentCreate({{ $key->id }})">
                                    <i class="fas fa-plus"></i>
                                </button>
                                    </div>

                                    @if($key->rooms->count() > 0)
                                        <div class="rooms-list-modern">
                                            @foreach($key->rooms as $room)
                                                <div class="room-chip-modern"
                                                     wire:click="prepareAssignmentEdit({{ $key->id }}, {{ $room->id }})">
                                                    <div class="room-chip-content">
                                                        <div class="room-chip-icon">
                                                            <i class="fas fa-door-open"></i>
                                                        </div>
                                                        <span class="room-chip-name">{{ $room->name }}</span>
                                                    </div>
                                                    <button class="room-chip-delete"
                                                            wire:click.stop="removeAssignment({{ $key->id }}, {{ $room->id }})"
                                                            wire:confirm="آیا از حذف این تخصیص مطمئن هستید؟">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                        @else
                                        <div class="empty-rooms-modern">
                                            <i class="fas fa-door-open"></i>
                                            <p>هیچ اتاقی تخصیص داده نشده</p>
                                            <button class="modern-btn-text" wire:click="prepareAssignmentCreate({{ $key->id }})">
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

            {{-- Pagination --}}
            @if($keys->hasPages())
                <div class="modern-pagination-wrapper">
                    {{ $keys->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- Modern Key Modal --}}
    @if($showKeyModal)
        <div class="modern-modal-overlay" wire:ignore.self>
            <div class="modern-modal-container">
                <div class="modern-modal">
                    <form wire:submit.prevent="saveKey">
                        <div class="modern-modal-header">
                            <div class="modal-header-content">
                                <div class="modal-icon-circle">
                                    <i class="fas fa-key"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title-text">{{ $keyId ? 'ویرایش کلید' : 'افزودن کلید جدید' }}</h5>
                                </div>
                            </div>
                            <button type="button" class="modal-close-btn" wire:click="$set('showKeyModal', false)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modern-modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="modern-input-wrapper">
                                        <label class="modern-label">
                                            نام کلید <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model.defer="keyName"
                                               class="modern-input @error('keyName') is-invalid @enderror"
                                               placeholder="نام کلید را وارد کنید">
                                @error('keyName')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                            </div>
                                <div class="col-md-6">
                                    <div class="modern-input-wrapper">
                                        <label class="modern-label">
                                            کد کلید <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" wire:model.defer="keyCode"
                                               class="modern-input @error('keyCode') is-invalid @enderror"
                                               placeholder="کد کلید را وارد کنید">
                                @error('keyCode')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="modern-input-wrapper">
                                        <label class="modern-label">توضیحات</label>
                                        <textarea wire:model.defer="keyDesc" class="modern-input" rows="3"
                                                  placeholder="توضیحات اختیاری..."></textarea>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="modern-input-wrapper">
                                        <label class="modern-label">یادداشت</label>
                                        <input type="text" wire:model.defer="keyNote" class="modern-input"
                                               placeholder="یادداشت اختیاری...">
                            </div>
                            </div>
                            </div>
                        </div>
                        <div class="modern-modal-footer">
                            <div>
                                <button type="button" class="modern-btn-secondary me-2" wire:click="$set('showKeyModal', false)">
                                    انصراف
                                </button>
                                <button type="submit" class="modern-btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <span wire:loading.remove wire:target="saveKey">ذخیره</span>
                                    <span wire:loading wire:target="saveKey" class="spinner-border spinner-border-sm"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Modern Assignment Modal --}}
    @if($showAssignmentModal)
        <div class="modern-modal-overlay" wire:ignore.self>
            <div class="modern-modal-container">
                <div class="modern-modal">
                    <form wire:submit.prevent="saveAssignment">
                        <div class="modern-modal-header">
                            <div class="modal-header-content">
                                <div class="modal-icon-circle">
                                    <i class="fas fa-link"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title-text">{{ $isEditingAssignment ? 'ویرایش تخصیص' : 'تخصیص اتاق جدید' }}</h5>
                                </div>
                            </div>
                            <button type="button" class="modal-close-btn" wire:click="$set('showAssignmentModal', false)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modern-modal-body">
                            <input type="hidden" wire:model="assignmentKeyId">

                            <div class="modern-input-wrapper">
                                <label class="modern-label">
                                    اتاق <span class="text-danger">*</span>
                                </label>
                                @if($isEditingAssignment)
                                    <input type="text" class="modern-input"
                                           value="{{ \App\Models\Room::find($assignmentRoomId)->name ?? '' }}" readonly>
                                @else
                                    <select wire:model.defer="assignmentRoomId" multiple
                                            class="modern-select @error('assignmentRoomId') is-invalid @enderror"
                                            size="8">
                                        <option value="">یک یا چند اتاق را انتخاب کنید...</option>
                                        @forelse($allFilteredRooms as $room)
                                            <option value="{{ $room->id }}">{{ $room->name }}</option>
                                        @empty
                                            <option value="" disabled>اتاق آزادی برای تخصیص وجود ندارد.</option>
                                        @endforelse
                                    </select>
                                    @error('assignmentRoomId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="text-muted d-block mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        برای انتخاب چند اتاق، Ctrl (یا Cmd در Mac) را نگه دارید
                                    </small>
                                @endif
                            </div>

                            <div class="modern-input-wrapper">
                                <label class="modern-label">تاریخ انقضا (اختیاری)</label>
                                <input type="datetime-local" wire:model.defer="assignmentExpiresAt"
                                       class="modern-input @error('assignmentExpiresAt') is-invalid @enderror">
                                @error('assignmentExpiresAt')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="modern-input-wrapper">
                                <label class="modern-label">یادداشت</label>
                                <textarea wire:model.defer="assignmentNotes" class="modern-input" rows="3"
                                          placeholder="یادداشت اختیاری..."></textarea>
                            </div>
                        </div>
                        <div class="modern-modal-footer">
                            <div>
                                @if($isEditingAssignment)
                                    <button type="button" class="modern-btn-danger" wire:click="removeAssignment"
                                            wire:confirm="آیا از حذف این تخصیص مطمئن هستید؟">
                                        <i class="fas fa-trash me-2"></i>
                                        <span wire:loading.remove wire:target="removeAssignment">حذف</span>
                                        <span wire:loading wire:target="removeAssignment" class="spinner-border spinner-border-sm"></span>
                                    </button>
                                @endif
                            </div>
                            <div>
                                <button type="button" class="modern-btn-secondary me-2" wire:click="$set('showAssignmentModal', false)">
                                    انصراف
                                </button>
                                <button type="submit" class="modern-btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <span wire:loading.remove wire:target="saveAssignment">ذخیره</span>
                                    <span wire:loading wire:target="saveAssignment" class="spinner-border spinner-border-sm"></span>
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
            /* Modern Material Design Styles */
            :root {
                --primary: #6366f1;
                --primary-dark: #4f46e5;
                --primary-light: #818cf8;
                --secondary: #8b5cf6;
                --success: #10b981;
                --danger: #ef4444;
                --warning: #f59e0b;
                --info: #3b82f6;
                --dark: #1f2937;
                --light: #f9fafb;
                --border: #e5e7eb;
                --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
                --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
                --shadow-lg: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }

            /* Header */
            .modern-header-wrapper {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                border-radius: 20px;
                padding: 32px;
                margin-bottom: 24px;
                box-shadow: var(--shadow-lg);
                position: relative;
                overflow: hidden;
            }

            .modern-header-wrapper::before {
                content: '';
                position: absolute;
                top: -50%;
                right: -20%;
                width: 400px;
                height: 400px;
                background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
                border-radius: 50%;
            }

            .modern-header-content {
                position: relative;
                z-index: 1;
            }

            .modern-icon-circle {
                width: 64px;
                height: 64px;
                background: rgba(255, 255, 255, 0.2);
                border-radius: 16px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 28px;
                color: white;
                backdrop-filter: blur(10px);
                box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            }

            .modern-title {
                color: white;
                font-size: 28px;
                font-weight: 700;
                margin: 0;
            }

            .modern-subtitle {
                color: rgba(255, 255, 255, 0.9);
                font-size: 14px;
            }

            .modern-btn-primary {
                background: white;
                color: var(--primary);
                border: none;
                padding: 12px 24px;
                border-radius: 12px;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s ease;
                box-shadow: var(--shadow-md);
            }

            .modern-btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-lg);
                color: var(--primary);
            }

            /* Filters */
            .modern-filters-card {
                background: white;
                border-radius: 16px;
                padding: 24px;
                margin-bottom: 24px;
                box-shadow: var(--shadow);
                border: 1px solid var(--border);
            }

            .modern-input-wrapper {
                margin-bottom: 0;
            }

            .modern-label {
                display: block;
                color: var(--dark);
                font-weight: 600;
                font-size: 13px;
                margin-bottom: 8px;
            }

            .modern-input-group {
                position: relative;
            }

            .input-icon {
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: #9ca3af;
                z-index: 1;
            }

            .modern-input, .modern-select {
                width: 100%;
                padding: 12px 16px;
                border: 2px solid var(--border);
                border-radius: 12px;
                font-size: 14px;
                transition: all 0.3s ease;
                background: white;
                color: var(--dark);
            }

            .modern-input:focus, .modern-select:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            }

            .modern-input-group .modern-input {
                padding-right: 48px;
            }

            /* Key Cards */
            .modern-key-card {
                background: white;
                border-radius: 20px;
                overflow: hidden;
                box-shadow: var(--shadow);
                border: 1px solid var(--border);
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .modern-key-card:hover {
                transform: translateY(-8px);
                box-shadow: var(--shadow-lg);
            }

            .key-card-top {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                padding: 24px;
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
            }

            .key-header-content {
                display: flex;
                align-items: center;
                gap: 16px;
                flex: 1;
            }

            .key-avatar {
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

            .key-info {
                flex: 1;
            }

            .key-name {
                color: white;
                font-size: 20px;
                font-weight: 700;
                margin: 0 0 8px 0;
            }

            .key-code-tag {
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

            .modern-menu-btn {
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

            .modern-menu-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .key-card-content {
                padding: 24px;
                flex: 1;
                display: flex;
                flex-direction: column;
            }

            /* Info Chips */
            .info-chip {
                background: var(--light);
                border: 2px solid var(--border);
                border-radius: 12px;
                padding: 12px;
                margin-bottom: 12px;
                display: flex;
                align-items: flex-start;
                gap: 12px;
                transition: all 0.3s ease;
            }

            .info-chip:hover {
                border-color: var(--primary);
                box-shadow: var(--shadow-sm);
            }

            .chip-icon {
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

            .chip-icon.blue {
                background: linear-gradient(135deg, var(--info) 0%, #2563eb 100%);
            }

            .chip-icon.orange {
                background: linear-gradient(135deg, var(--warning) 0%, #d97706 100%);
            }

            .chip-content {
                flex: 1;
            }

            .chip-label {
                display: block;
                color: #6b7280;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                margin-bottom: 4px;
            }

            .chip-text {
                color: var(--dark);
                font-size: 13px;
                font-weight: 500;
                margin: 0;
            }

            /* Rooms Section */
            .rooms-section-modern {
                margin-top: auto;
                padding-top: 20px;
                border-top: 2px solid var(--border);
            }

            .rooms-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 16px;
            }

            .rooms-title-wrapper {
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .rooms-icon-wrapper {
                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 18px;
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }

            .rooms-title {
                color: var(--dark);
                font-size: 14px;
                font-weight: 700;
                margin: 0 0 2px 0;
            }

            .rooms-count {
                color: #6b7280;
                font-size: 12px;
                font-weight: 500;
            }

            .modern-btn-icon {
                background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
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

            .modern-btn-icon:hover {
                transform: scale(1.1) rotate(90deg);
                box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
            }

            /* Room Chips */
            .rooms-list-modern {
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .room-chip-modern {
                background: white;
                border: 2px solid var(--border);
                border-radius: 12px;
                padding: 12px 16px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            cursor: pointer;
                transition: all 0.3s ease;
            }

            .room-chip-modern:hover {
                border-color: var(--success);
                background: #f0fdf4;
                transform: translateX(-4px);
                box-shadow: var(--shadow-sm);
            }

            .room-chip-content {
                display: flex;
                align-items: center;
                gap: 12px;
                flex: 1;
            }

            .room-chip-icon {
                width: 32px;
                height: 32px;
                background: linear-gradient(135deg, var(--success) 0%, #059669 100%);
                border-radius: 8px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 14px;
            }

            .room-chip-name {
                color: var(--dark);
                font-size: 14px;
                font-weight: 600;
            }

            .room-chip-delete {
                background: transparent;
                border: none;
                color: var(--danger);
                width: 28px;
                height: 28px;
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: all 0.3s ease;
            }

            .room-chip-delete:hover {
                background: #fee2e2;
                color: #dc2626;
                transform: scale(1.1);
            }

            /* Empty States */
            .modern-empty-state {
                background: white;
                border-radius: 20px;
                padding: 64px 32px;
                text-align: center;
                box-shadow: var(--shadow);
            }

            .empty-icon-circle {
                width: 80px;
                height: 80px;
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 36px;
                margin-bottom: 24px;
                box-shadow: var(--shadow-lg);
            }

            .empty-title {
                color: var(--dark);
                font-size: 24px;
                font-weight: 700;
                margin-bottom: 8px;
            }

            .empty-description {
                color: #6b7280;
                font-size: 14px;
                margin-bottom: 24px;
            }

            .empty-rooms-modern {
                text-align: center;
                padding: 32px 16px;
                background: var(--light);
                border-radius: 12px;
                border: 2px dashed var(--border);
            }

            .empty-rooms-modern i {
                font-size: 32px;
                color: #9ca3af;
                margin-bottom: 12px;
            }

            .empty-rooms-modern p {
                color: #6b7280;
                font-size: 13px;
                margin-bottom: 16px;
            }

            .modern-btn-text {
                background: transparent;
                border: 2px solid var(--primary);
                color: var(--primary);
                padding: 8px 16px;
                border-radius: 8px;
                font-weight: 600;
                font-size: 13px;
                transition: all 0.3s ease;
            }

            .modern-btn-text:hover {
                background: var(--primary);
                color: white;
            }

            /* Dropdown */
            .modern-dropdown-wrapper {
                position: relative;
            }

            .modern-dropdown-menu {
                position: absolute;
                top: 100%;
                left: 0;
                z-index: 1000;
                min-width: 180px;
                background: white;
                border-radius: 12px;
                box-shadow: var(--shadow-lg);
                border: none;
                padding: 8px;
                margin-top: 8px;
                list-style: none;
            }

            .dropdown-item-modern {
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 10px 16px;
                border-radius: 8px;
                color: var(--dark);
                font-weight: 500;
                font-size: 14px;
                transition: all 0.2s;
            }

            .dropdown-item-modern:hover {
                background: var(--light);
                color: var(--primary);
            }

            .dropdown-item-modern.danger {
                color: var(--danger);
            }

            .dropdown-item-modern.danger:hover {
                background: #fee2e2;
                color: #dc2626;
            }

            /* Modal */
            .modern-modal-overlay {
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
                animation: fadeIn 0.3s ease;
            }

            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }

            .modern-modal-container {
                width: 100%;
                max-width: 600px;
                animation: slideUp 0.3s ease;
            }

            @keyframes slideUp {
                from {
                    opacity: 0;
                    transform: translateY(20px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .modern-modal {
                background: white;
                border-radius: 20px;
                box-shadow: var(--shadow-lg);
                overflow: hidden;
            }

            .modern-modal-header {
                background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
                padding: 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modal-header-content {
                display: flex;
                align-items: center;
                gap: 16px;
            }

            .modal-icon-circle {
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

            .modal-title-text {
                color: white;
                font-size: 20px;
                font-weight: 700;
                margin: 0;
            }

            .modal-close-btn {
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

            .modal-close-btn:hover {
                background: rgba(255, 255, 255, 0.3);
                transform: scale(1.1);
            }

            .modern-modal-body {
                padding: 24px;
            }

            .modern-modal-footer {
                padding: 20px 24px;
                background: var(--light);
                border-top: 1px solid var(--border);
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

            .modern-btn-secondary {
                background: white;
                border: 2px solid var(--border);
                color: var(--dark);
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s ease;
            }

            .modern-btn-secondary:hover {
                background: var(--light);
                border-color: var(--primary);
                color: var(--primary);
            }

            .modern-btn-danger {
                background: linear-gradient(135deg, var(--danger) 0%, #dc2626 100%);
                border: none;
                color: white;
                padding: 10px 20px;
                border-radius: 10px;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.3s ease;
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            }

            .modern-btn-danger:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
            color: white;
            }

            /* Pagination */
            .modern-pagination-wrapper {
                margin-top: 32px;
                display: flex;
                justify-content: center;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .modern-header-wrapper {
                    padding: 24px;
                }

                .key-card-top {
                    padding: 20px;
                }

                .key-card-content {
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
            const allDropdowns = document.querySelectorAll('.modern-dropdown-menu, .cooler-dropdown-menu');

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
            if (!event.target.closest('.modern-dropdown-wrapper, .cooler-dropdown-wrapper')) {
                document.querySelectorAll('.modern-dropdown-menu, .cooler-dropdown-menu').forEach(dropdown => {
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

        // Handle delete confirmation for keys
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('confirm-delete-key', (data) => {
                const keyId = data.keyId;
                waitForCuteAlert(() => {
                    window.cuteAlert({
                        type: 'warning',
                        title: 'حذف کلید',
                        description: 'آیا از حذف این کلید مطمئن هستید؟',
                        primaryButtonText: 'بله، حذف کن',
                        secondaryButtonText: 'انصراف'
                    }).then((result) => {
                        if (result === 'primaryButtonClicked') {
                            Livewire.dispatch('delete-key-confirmed', { keyId: keyId });
                        }
                    });
                });
            });

            Livewire.on('delete-key-confirmed', (data) => {
                const keyId = data.keyId;
                @this.call('deleteKey', keyId);
            });
        });
    </script>
    @endpush
</div>
