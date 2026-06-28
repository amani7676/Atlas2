<div>
    <div class="container-fluid px-4 py-4" dir="rtl">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('amval') }}" class="btn-back">
                <i class="fas fa-arrow-right me-2"></i>
                بازگشت به لیست دارایی‌ها
            </a>
        </div>

        {{-- Asset Header --}}
        <div class="asset-detail-header">
            <div class="asset-detail-header-content">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="asset-detail-icon">
                            <i class="{{ $asset->assetType->icon ?? 'fas fa-box' }}"></i>
                        </div>
                        <div class="ms-3">
                            <h2 class="asset-detail-title mb-1">{{ $asset->name }}</h2>
                            <p class="asset-detail-subtitle mb-0">{{ $asset->assetType->name }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <div class="asset-detail-status status-{{ $asset->status }}">
                            {{ $asset->status_label }}
                        </div>
                        <button wire:click="openAssetModal()" class="asset-detail-btn edit">
                            <i class="fas fa-edit"></i>
                            ویرایش
                        </button>
                        <button wire:click="confirmDeleteAsset()" class="asset-detail-btn delete">
                            <i class="fas fa-trash"></i>
                            حذف
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Asset Information Card --}}
        <div class="asset-info-card mb-4">
            <div class="asset-info-header">
                <h3 class="asset-info-title">
                    <i class="fas fa-info-circle me-2"></i>اطلاعات دارایی
                </h3>
            </div>
            <div class="row g-3">
                @if($asset->number)
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="info-label">شماره:</label>
                            <span class="info-value">{{ $asset->number }}</span>
                        </div>
                    </div>
                @endif
                @if($asset->model)
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="info-label">مدل:</label>
                            <span class="info-value">{{ $asset->model }}</span>
                        </div>
                    </div>
                @endif
                @if($asset->serial_number)
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="info-label">شماره سریال:</label>
                            <span class="info-value">{{ $asset->serial_number }}</span>
                        </div>
                    </div>
                @endif
                @if($asset->installation_date)
                    <div class="col-md-4">
                        <div class="info-item">
                            <label class="info-label">تاریخ نصب:</label>
                            <span class="info-value">{{ \Carbon\Carbon::parse($asset->installation_date)->format('Y/m/d') }}</span>
                        </div>
                    </div>
                @endif
                @if($asset->description)
                    <div class="col-12">
                        <div class="info-item">
                            <label class="info-label">توضیحات:</label>
                            <span class="info-value">{{ $asset->description }}</span>
                        </div>
                    </div>
                @endif
                @if($asset->notes)
                    <div class="col-12">
                        <div class="info-item">
                            <label class="info-label">یادداشت‌ها:</label>
                            <span class="info-value">{{ $asset->notes }}</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Room Connections Section --}}
        <div class="room-connections-card">
            <div class="room-connections-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="room-connections-title">
                        <i class="fas fa-link me-2"></i>اتصالات به اتاق‌ها
                    </h3>
                    <button wire:click="openConnectionModal()" class="btn-add-connection">
                        <i class="fas fa-plus me-2"></i>
                        اتصال جدید
                    </button>
                </div>
            </div>

            {{-- Filters --}}
            <div class="room-filters mb-4">
                <div class="row g-3">
                    <div class="col-lg-6 col-md-6">
                        <div class="filter-input-wrapper">
                            <label class="filter-label">
                                <i class="fas fa-search me-2"></i>جستجوی اتاق
                            </label>
                            <div class="filter-input-group">
                                <i class="fas fa-search input-icon"></i>
                                <input type="text" wire:model.live="searchRoom" 
                                       class="filter-input" placeholder="نام یا کد اتاق...">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="filter-input-wrapper">
                            <label class="filter-label">
                                <i class="fas fa-building me-2"></i>واحد
                            </label>
                            <select wire:model.live="filterUnit" class="filter-select">
                                <option value="">همه واحدها</option>
                                @foreach($units as $unit)
                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Connected Rooms List --}}
            @if($connections->isEmpty())
                <div class="empty-connections">
                    <div class="empty-icon">
                        <i class="fas fa-link"></i>
                    </div>
                    <h4 class="empty-title">هیچ اتاقی متصل نشده</h4>
                    <p class="empty-desc">برای شروع، این دارایی را به اتاق‌ها متصل کنید</p>
                    <button wire:click="openConnectionModal()" class="btn-add-connection">
                        <i class="fas fa-plus me-2"></i>
                        اتصال جدید
                    </button>
                </div>
            @else
                <div class="row g-3">
                    @foreach($connections as $room)
                        <div class="col-lg-4 col-md-6">
                            <div class="room-connection-card">
                                <div class="room-connection-header">
                                    <div class="room-icon">
                                        <i class="fas fa-door-open"></i>
                                    </div>
                                    <div class="room-info">
                                        <h5 class="room-name">{{ $room->name }}</h5>
                                        <p class="room-unit">{{ $room->unit->name ?? 'بدون واحد' }}</p>
                                    </div>
                                </div>
                                <div class="room-connection-body">
                                    @if($room->pivot->connection_type)
                                        <div class="connection-detail">
                                            <label>نوع اتصال:</label>
                                            <span>{{ $room->pivot->connection_type }}</span>
                                        </div>
                                    @endif
                                    @if($room->pivot->connected_at)
                                        <div class="connection-detail">
                                            <label>تاریخ اتصال:</label>
                                            <span>{{ \Carbon\Carbon::parse($room->pivot->connected_at)->format('Y/m/d') }}</span>
                                        </div>
                                    @endif
                                    @if($room->pivot->notes)
                                        <div class="connection-detail">
                                            <label>یادداشت:</label>
                                            <span>{{ $room->pivot->notes }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="room-connection-footer">
                                    <button wire:click="editConnection({{ $room->pivot->id }})" class="connection-btn edit">
                                        <i class="fas fa-edit"></i>
                                        ویرایش
                                    </button>
                                    <button wire:click="confirmDelete({{ $room->pivot->id }})" class="connection-btn delete">
                                        <i class="fas fa-trash"></i>
                                        حذف
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Asset Edit Modal --}}
    @if($showAssetModal)
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>ویرایش دارایی</h3>
                    <button wire:click="closeAssetModal()" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>نوع دارایی *</label>
                        <select wire:model="selectedAssetType" class="form-control">
                            <option value="">انتخاب کنید...</option>
                            @foreach($assetTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedAssetType') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>نام دارایی *</label>
                        <input type="text" wire:model="assetName" class="form-control" placeholder="نام دارایی">
                        @error('assetName') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>شماره</label>
                        <input type="text" wire:model="assetNumber" class="form-control" placeholder="شماره سریال">
                        @error('assetNumber') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>وضعیت *</label>
                        <select wire:model="assetStatus" class="form-control">
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                            <option value="maintenance">تعمیرات</option>
                        </select>
                        @error('assetStatus') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>مدل</label>
                        <input type="text" wire:model="assetModel" class="form-control" placeholder="مدل دستگاه">
                        @error('assetModel') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>شماره سریال</label>
                        <input type="text" wire:model="assetSerialNumber" class="form-control" placeholder="شماره سریال سازنده">
                        @error('assetSerialNumber') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>تاریخ نصب</label>
                        <input type="date" wire:model="assetInstallationDate" class="form-control">
                        @error('assetInstallationDate') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>توضیحات</label>
                        <textarea wire:model="assetDescription" class="form-control" rows="3"></textarea>
                        @error('assetDescription') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>یادداشت‌ها</label>
                        <textarea wire:model="assetNotes" class="form-control" rows="2"></textarea>
                        @error('assetNotes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeAssetModal()" class="btn-secondary">انصراف</button>
                    <button wire:click="saveAsset()" class="btn-primary">
                        به‌روزرسانی
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Connection Modal --}}
    @if($showConnectionModal)
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ $editingConnection ? 'ویرایش اتصال' : 'افزودن اتصال جدید' }}</h3>
                    <button wire:click="closeConnectionModal()" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>دارایی</label>
                        <input type="text" class="form-control" value="{{ $asset->name }} ({{ $asset->assetType->name }})" disabled>
                    </div>
                    @if(!$editingConnection)
                        <div class="form-group mb-3">
                            <label>اتاق‌ها (چند انتخابی) *</label>
                            <select wire:model="selectedRoom" class="form-control" multiple>
                                @foreach($this->filteredRooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} - {{ $room->unit->name ?? 'بدون واحد' }}</option>
                                @endforeach
                            </select>
                            @error('selectedRoom') <span class="error-message">{{ $message }}</span> @enderror
                            <small class="text-muted">برای انتخاب چندگانه، کلید Ctrl را نگه دارید</small>
                        </div>
                    @else
                        <div class="form-group mb-3">
                            <label>اتاق *</label>
                            <select wire:model="selectedRoom" class="form-control">
                                <option value="">انتخاب کنید...</option>
                                @foreach($rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }} - {{ $room->unit->name ?? 'بدون واحد' }}</option>
                                @endforeach
                            </select>
                            @error('selectedRoom') <span class="error-message">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    <div class="form-group mb-3">
                        <label>نوع اتصال</label>
                        <select wire:model="connectionType" class="form-control">
                            <option value="direct">مستقیم</option>
                            <option value="duct">کانالی</option>
                            <option value="central">مرکزی</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label>تاریخ اتصال</label>
                        <input type="date" wire:model="connectedAt" class="form-control">
                        @error('connectedAt') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>یادداشت‌ها</label>
                        <textarea wire:model="notes" class="form-control" rows="2"></textarea>
                        @error('notes') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeConnectionModal()" class="btn-secondary">انصراف</button>
                    <button wire:click="saveConnection()" class="btn-primary">
                        {{ $editingConnection ? 'به‌روزرسانی' : 'ایجاد' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Delete Confirmation Script --}}
    <script>
        @this.on('confirm-delete-asset', (assetName) => {
            if (confirm(`آیا از حذف دارایی "${assetName}" اطمینان دارید؟`)) {
                @this.deleteAsset();
            }
        });

        @this.on('confirmDelete', (data) => {
            if (confirm('آیا از حذف این اتصال اطمینان دارید؟')) {
                @this.deleteConnection(data.connectionId);
            }
        });
    </script>

    <style>
        /* Back Button */
        .btn-back {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: white;
            color: #667eea;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #667eea;
            color: white;
            transform: translateX(-4px);
        }

        /* Asset Detail Header */
        .asset-detail-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .asset-detail-header-content {
            color: white;
        }

        .asset-detail-icon {
            width: 60px;
            height: 60px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            color: white;
        }

        .asset-detail-title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .asset-detail-subtitle {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .asset-detail-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        .asset-detail-status.status-active {
            background: rgba(40, 167, 69, 0.8);
        }

        .asset-detail-status.status-inactive {
            background: rgba(108, 117, 125, 0.8);
        }

        .asset-detail-status.status-maintenance {
            background: rgba(255, 193, 7, 0.8);
        }

        .asset-detail-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }

        .asset-detail-btn.edit {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
        }

        .asset-detail-btn.edit:hover {
            background: white;
            color: #667eea;
        }

        .asset-detail-btn.delete {
            background: rgba(220, 53, 69, 0.2);
            color: white;
            border: 2px solid rgba(220, 53, 69, 0.8);
        }

        .asset-detail-btn.delete:hover {
            background: #dc3545;
            border-color: #dc3545;
        }

        /* Asset Info Card */
        .asset-info-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .asset-info-header {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .asset-info-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .info-item {
            padding: 12px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .info-label {
            display: block;
            font-weight: 600;
            color: #666;
            margin-bottom: 4px;
            font-size: 14px;
        }

        .info-value {
            color: #333;
            font-size: 15px;
        }

        /* Room Connections Card */
        .room-connections-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .room-connections-header {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .room-connections-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .btn-add-connection {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-add-connection:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        /* Room Filters */
        .room-filters {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 16px;
        }

        .filter-input-wrapper {
            margin-bottom: 8px;
        }

        .filter-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .filter-input-group {
            position: relative;
        }

        .input-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .filter-input {
            width: 100%;
            padding: 10px 40px 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .filter-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .filter-select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        /* Empty Connections */
        .empty-connections {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon {
            width: 80px;
            height: 80px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: #999;
        }

        .empty-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .empty-desc {
            color: #666;
            margin-bottom: 20px;
        }

        /* Room Connection Card */
        .room-connection-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .room-connection-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .room-connection-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .room-icon {
            width: 45px;
            height: 45px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: #667eea;
        }

        .room-info {
            flex: 1;
        }

        .room-name {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #333;
        }

        .room-unit {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .room-connection-body {
            margin-bottom: 16px;
        }

        .connection-detail {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .connection-detail:last-child {
            border-bottom: none;
        }

        .connection-detail label {
            font-weight: 600;
            color: #555;
            font-size: 13px;
        }

        .connection-detail span {
            color: #333;
            font-size: 13px;
        }

        .room-connection-footer {
            display: flex;
            gap: 8px;
        }

        .connection-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 13px;
        }

        .connection-btn.edit {
            background: white;
            color: #667eea;
        }

        .connection-btn.edit:hover {
            background: #667eea;
            color: white;
        }

        .connection-btn.delete {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .connection-btn.delete:hover {
            background: #dc3545;
            color: white;
        }

        /* Modal Styles */
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 18px;
            font-weight: 700;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #999;
            transition: all 0.3s;
        }

        .modal-close:hover {
            color: #333;
        }

        .modal-body .form-group {
            margin-bottom: 16px;
        }

        .modal-body label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .modal-body .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .modal-body .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modal-body .form-control:disabled {
            background: #f8f9fa;
            cursor: not-allowed;
        }

        .error-message {
            color: #dc3545;
            font-size: 12px;
            margin-top: 4px;
        }

        .modal-footer {
            display: flex;
            gap: 12px;
            margin-top: 24px;
            padding-top: 16px;
            border-top: 2px solid #f0f0f0;
        }

        .modal-footer button {
            flex: 1;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .asset-detail-header {
                padding: 16px;
            }

            .asset-detail-icon {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .asset-detail-title {
                font-size: 20px;
            }

            .asset-info-card,
            .room-connections-card {
                padding: 16px;
            }

            .room-connection-footer {
                flex-direction: column;
            }
        }
    </style>
</div>
