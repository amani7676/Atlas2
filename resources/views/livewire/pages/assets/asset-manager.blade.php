<div>
    <div class="container-fluid px-4 py-4" dir="rtl">
        {{-- Modern Header --}}
        <div class="asset-header-modern">
            <div class="asset-header-content">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="asset-icon-modern">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <div class="ms-3">
                            <h2 class="asset-title-modern mb-1">مدیریت انواع دارایی‌ها</h2>
                            <p class="asset-subtitle-modern mb-0">سامانه مدیریت انواع دارایی‌ها</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button wire:click="openAssetTypeModal()" class="asset-btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            افزودن نوع دارایی
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Asset Types Section --}}
        <div class="asset-section-card mb-4">
            <div class="asset-section-header">
                <h3 class="asset-section-title">
                    <i class="fas fa-tags me-2"></i>انواع دارایی‌ها
                </h3>
            </div>
            @if($assetTypes->isEmpty())
                <div class="asset-empty-state">
                    <div class="empty-icon-asset">
                        <i class="fas fa-tags"></i>
                    </div>
                    <h4 class="empty-title-asset">هیچ نوع دارایی تعریف نشده</h4>
                    <p class="empty-desc-asset">برای شروع، اولین نوع دارایی خود را ایجاد کنید</p>
                    <button wire:click="openAssetTypeModal()" class="asset-btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        افزودن نوع دارایی
                    </button>
                </div>
            @else
                <div class="row g-3">
                    @foreach($assetTypes as $type)
                        <div class="col-md-4 col-lg-3">
                            <div class="asset-type-card">
                                <div class="asset-type-icon">
                                    <i class="{{ $type->icon ?? 'fas fa-box' }}"></i>
                                </div>
                                <div class="asset-type-info">
                                    <h5 class="asset-type-name">{{ $type->name }}</h5>
                                    <p class="asset-type-count">{{ $type->assets->count() }} دارایی</p>
                                </div>
                                <div class="asset-type-actions">
                                    <a href="{{ route('amval.type', $type->id) }}" class="asset-action-btn view">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button wire:click="openAssetTypeModal({{ $type->id }})" class="asset-action-btn edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="if(confirm('آیا از حذف نوع دارایی {{ $type->name }} اطمینان دارید؟')) { @this.deleteAssetType({{ $type->id }}) }" class="asset-action-btn delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Asset Type Modal --}}
    @if($showAssetTypeModal)
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ $editingAssetTypeId ? 'ویرایش نوع دارایی' : 'افزودن نوع دارایی جدید' }}</h3>
                    <button wire:click="closeAssetTypeModal()" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>نام نوع دارایی *</label>
                        <input type="text" wire:model="assetTypeName" class="form-control" placeholder="مثال: کولر، هیتر، پنکه">
                        @error('assetTypeName') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>آیکون (FontAwesome)</label>
                        <input type="text" wire:model="assetTypeIcon" class="form-control" placeholder="مثال: fa-fan, fa-fire, fa-key">
                        @error('assetTypeIcon') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>توضیحات</label>
                        <textarea wire:model="assetTypeDescription" class="form-control" rows="3"></textarea>
                        @error('assetTypeDescription') <span class="error-message">{{ $message }}</span> @enderror
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" wire:model="assetTypeIsActive">
                            فعال
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button wire:click="closeAssetTypeModal()" class="btn-secondary">انصراف</button>
                    <button wire:click="saveAssetType()" class="btn-primary">
                        {{ $editingAssetTypeId ? 'به‌روزرسانی' : 'ایجاد' }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <style>
        /* Asset Manager Styles */
        .asset-header-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }

        .asset-header-content {
            color: white;
        }

        .asset-icon-modern {
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

        .asset-title-modern {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .asset-subtitle-modern {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .asset-btn-primary {
            background: white;
            color: #667eea;
            border: none;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .asset-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .asset-btn-secondary {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .asset-btn-secondary:hover {
            background: white;
            color: #667eea;
        }

        .asset-btn-tertiary {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.5);
            padding: 12px 24px;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .asset-btn-tertiary:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: white;
        }

        .asset-filters-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .asset-input-wrapper {
            margin-bottom: 8px;
        }

        .asset-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .asset-input-group {
            position: relative;
        }

        .input-icon-asset {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        .asset-input {
            width: 100%;
            padding: 12px 40px 12px 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .asset-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .asset-select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            background: white;
            cursor: pointer;
        }

        .asset-section-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .asset-section-header {
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #f0f0f0;
        }

        .asset-section-title {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            margin: 0;
        }

        .asset-type-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            transition: all 0.3s;
            position: relative;
        }

        .asset-type-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .asset-type-icon {
            width: 50px;
            height: 50px;
            background: white;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #667eea;
        }

        .asset-type-info {
            flex: 1;
        }

        .asset-type-name {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #333;
        }

        .asset-type-count {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .asset-type-actions {
            display: flex;
            gap: 8px;
        }

        .asset-action-btn {
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }

        .asset-action-btn.view {
            background: #667eea;
            color: white;
        }

        .asset-action-btn.edit {
            background: #4CAF50;
            color: white;
        }

        .asset-action-btn.delete {
            background: #f44336;
            color: white;
        }

        .asset-action-btn:hover {
            transform: scale(1.1);
        }

        .asset-card {
            background: white;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
        }

        .asset-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            border-color: #667eea;
        }

        .asset-card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .asset-card-icon {
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            color: white;
        }

        .asset-card-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            color: white;
        }

        .asset-card-status.status-active {
            background: #4CAF50;
        }

        .asset-card-status.status-inactive {
            background: #9e9e9e;
        }

        .asset-card-status.status-maintenance {
            background: #ff9800;
        }

        .asset-card-body {
            padding: 20px;
        }

        .asset-card-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 4px 0;
            color: #333;
        }

        .asset-card-type {
            font-size: 14px;
            color: #667eea;
            margin: 0 0 12px 0;
            font-weight: 600;
        }

        .asset-card-detail {
            font-size: 13px;
            color: #666;
            margin: 4px 0;
        }

        .asset-rooms {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #f0f0f0;
        }

        .asset-rooms-title {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            margin: 0 0 8px 0;
        }

        .asset-rooms-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .asset-room-badge {
            background: #e3f2fd;
            color: #1976d2;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .asset-room-more {
            background: #f5f5f5;
            color: #666;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .asset-card-footer {
            padding: 16px;
            background: #f9f9f9;
            display: flex;
            gap: 8px;
        }

        .asset-card-btn {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .asset-card-btn.connect {
            background: #667eea;
            color: white;
        }

        .asset-card-btn.edit {
            background: #4CAF50;
            color: white;
        }

        .asset-card-btn.delete {
            background: #f44336;
            color: white;
        }

        .asset-card-btn:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        .asset-empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon-asset {
            width: 80px;
            height: 80px;
            background: #f5f5f5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 32px;
            color: #999;
        }

        .empty-title-asset {
            font-size: 20px;
            font-weight: 700;
            color: #333;
            margin: 0 0 8px 0;
        }

        .empty-desc-asset {
            font-size: 14px;
            color: #666;
            margin: 0 0 24px 0;
        }

        .asset-table {
            width: 100%;
            border-collapse: collapse;
        }

        .asset-table th {
            background: #f5f5f5;
            padding: 12px;
            text-align: right;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }

        .asset-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            color: #555;
        }

        .asset-table tr:hover {
            background: #f9f9f9;
        }

        .asset-table-actions {
            display: flex;
            gap: 8px;
        }

        .table-action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .table-action-btn.edit {
            background: #4CAF50;
            color: white;
        }

        .table-action-btn.delete {
            background: #f44336;
            color: white;
        }

        .table-action-btn:hover {
            transform: scale(1.1);
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
            border-radius: 16px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            padding: 20px 24px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            color: #333;
        }

        .modal-close {
            width: 36px;
            height: 36px;
            border: none;
            background: #f5f5f5;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }

        .modal-close:hover {
            background: #e0e0e0;
        }

        .modal-body {
            padding: 24px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        .form-group .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .error-message {
            color: #f44336;
            font-size: 12px;
            margin-top: 4px;
        }

        .modal-footer {
            padding: 16px 24px;
            border-top: 2px solid #f0f0f0;
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-primary {
            background: #667eea;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-secondary {
            background: #e0e0e0;
            color: #333;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-secondary:hover {
            background: #d0d0d0;
        }

        @media (max-width: 768px) {
            .asset-header-modern {
                padding: 16px;
            }

            .asset-title-modern {
                font-size: 20px;
            }

            .asset-btn-primary,
            .asset-btn-secondary,
            .asset-btn-tertiary {
                padding: 10px 16px;
                font-size: 14px;
            }

            .asset-filters-card {
                padding: 16px;
            }

            .asset-section-card {
                padding: 16px;
            }
        }
    </style>
</div>
