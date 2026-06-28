<div>
    <div class="container-fluid px-4 py-4" dir="rtl">
        {{-- Back Button --}}
        <div class="mb-4">
            <a href="{{ route('amval') }}" class="btn-back">
                <i class="fas fa-arrow-right me-2"></i>
                بازگشت به مدیریت دارایی‌ها
            </a>
        </div>

        {{-- Header --}}
        <div class="asset-header-modern">
            <div class="asset-header-content">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center">
                        <div class="asset-icon-modern">
                            <i class="{{ $assetType->icon ?? 'fas fa-box' }}"></i>
                        </div>
                        <div class="ms-3">
                            <h2 class="asset-title-modern mb-1">مدیریت {{ $assetType->name }}</h2>
                            <p class="asset-subtitle-modern mb-0">{{ $assetType->description ?? '' }}</p>
                        </div>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button wire:click="openAssetModal()" class="asset-btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            افزودن {{ $assetType->name }}
                        </button>
                        <button wire:click="openConnectionModal()" class="asset-btn-secondary">
                            <i class="fas fa-link me-2"></i>
                            اتصال جدید
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="asset-filters-card">
            <div class="row g-3">
                <div class="col-lg-4 col-md-6">
                    <div class="asset-input-wrapper">
                        <label class="asset-label">
                            <i class="fas fa-search me-2"></i>جستجوی {{ $assetType->name }}
                        </label>
                        <div class="asset-input-group">
                            <i class="fas fa-search input-icon-asset"></i>
                            <input type="text" wire:model.live="searchAsset" 
                                   class="asset-input" placeholder="نام، شماره، مدل یا نام اتاق...">
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6">
                    <div class="asset-input-wrapper">
                        <label class="asset-label">
                            <i class="fas fa-filter me-2"></i>وضعیت
                        </label>
                        <select wire:model.live="filterStatus" class="asset-select">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="active">فعال</option>
                            <option value="inactive">غیرفعال</option>
                            <option value="maintenance">تعمیرات</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="asset-input-wrapper">
                        <label class="asset-label">
                            <i class="fas fa-search me-2"></i>جستجوی اتاق
                        </label>
                        <div class="asset-input-group">
                            <i class="fas fa-search input-icon-asset"></i>
                            <input type="text" wire:model.live="searchRoom" 
                                   class="asset-input" placeholder="نام یا کد اتاق...">
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="asset-input-wrapper">
                        <label class="asset-label">
                            <i class="fas fa-building me-2"></i>واحد
                        </label>
                        <select wire:model.live="filterUnit" class="asset-select">
                            <option value="">همه واحدها</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- Search Results Box --}}
        @if($searchAsset || $searchRoom || $filterStatus || $filterUnit)
            <div class="search-results-box mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="search-results-count">
                            <i class="fas fa-database me-2"></i>
                            {{ $assets->count() }} {{ $assetType->name }} یافت شد
                        </span>
                    </div>
                    <button wire:click="$set('searchAsset', ''); $set('searchRoom', ''); $set('filterStatus', ''); $set('filterUnit', '')" class="clear-search-btn">
                        <i class="fas fa-times me-1"></i>
                        پاک کردن فیلترها
                    </button>
                </div>
                
                {{-- Connections Table in Search Results --}}
                @if($connections->isNotEmpty() && $connections->some(fn($c) => $c->rooms->isNotEmpty()))
                    <div class="connections-table-wrapper">
                        <h5 class="connections-table-title">
                            <i class="fas fa-link me-2"></i>اتصالات {{ $assetType->name }} ({{ $connections->sum(fn($c) => $c->rooms->count()) }} اتصال)
                        </h5>
                        <div class="table-responsive">
                            <table class="connections-table">
                                <thead>
                                    <tr>
                                        <th>{{ $assetType->name }}</th>
                                        <th>اتاق</th>
                                        <th>واحد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($connections as $asset)
                                        @foreach($asset->rooms as $room)
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <i class="{{ $assetType->icon ?? 'fas fa-box' }} me-2"></i>
                                                        {{ $asset->name }}
                                                    </div>
                                                </td>
                                                <td>{{ $room->name }}</td>
                                                <td>{{ $room->unit->name ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    @if($searchAsset || $searchRoom || $filterStatus || $filterUnit)
                        <div class="no-connections-message">
                            <i class="fas fa-info-circle me-2"></i>
                            هیچ اتصالی برای نتایج سرچ یافت نشد
                        </div>
                    @endif
                @endif
            </div>
        @endif

        {{-- Assets Grid --}}
        <div class="asset-section-card">
            <div class="asset-section-header">
                <h3 class="asset-section-title">
                    <i class="fas fa-boxes me-2"></i>{{ $assetType->name }}ها
                </h3>
            </div>
            @if($this->assets->isEmpty())
                <div class="asset-empty-state">
                    <div class="empty-icon-asset">
                        <i class="{{ $assetType->icon ?? 'fas fa-box' }}"></i>
                    </div>
                    <h4 class="empty-title-asset">هیچ {{ $assetType->name }}ی یافت نشد</h4>
                    <p class="empty-desc-asset">برای شروع، اولین {{ $assetType->name }} خود را ایجاد کنید</p>
                    <button wire:click="openAssetModal()" class="asset-btn-primary">
                        <i class="fas fa-plus me-2"></i>
                        افزودن {{ $assetType->name }}
                    </button>
                </div>
            @else
                <div class="row g-4">
                    @foreach($this->assets as $asset)
                        <div class="col-lg-4 col-md-6">
                            <div class="asset-card">
                                <div class="asset-card-header">
                                    <div class="asset-card-icon">
                                        <i class="{{ $assetType->icon ?? 'fas fa-box' }}"></i>
                                    </div>
                                    <div class="asset-card-status status-{{ $asset->status }}">
                                        {{ $asset->status_label }}
                                    </div>
                                </div>
                                <div class="asset-card-body">
                                    <h5 class="asset-card-title">{{ $asset->name }}</h5>
                                    @if($asset->number)
                                        <p class="asset-card-detail">
                                            <i class="fas fa-hashtag me-1"></i>{{ $asset->number }}
                                        </p>
                                    @endif
                                    @if($asset->model)
                                        <p class="asset-card-detail">
                                            <i class="fas fa-cog me-1"></i>{{ $asset->model }}
                                        </p>
                                    @endif
                                    @if($asset->rooms->isNotEmpty())
                                        <div class="asset-rooms">
                                            <p class="asset-rooms-title">
                                                <i class="fas fa-door-open me-1"></i>
                                                اتاق‌های متصل:
                                            </p>
                                            <div class="asset-rooms-list">
                                                @foreach($asset->rooms->take(3) as $room)
                                                    <span class="asset-room-badge">{{ $room->name }}</span>
                                                @endforeach
                                                @if($asset->rooms->count() > 3)
                                                    <span class="asset-room-more">+{{ $asset->rooms->count() - 3 }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="asset-card-footer">
                                    <button wire:click="openConnectionModal({{ $asset->id }})" class="asset-card-btn connect">
                                        <i class="fas fa-link"></i>
                                        اتصال
                                    </button>
                                    <button wire:click="openAssetModal({{ $asset->id }})" class="asset-card-btn edit">
                                        <i class="fas fa-edit"></i>
                                        ویرایش
                                    </button>
                                    <button onclick="if(confirm('آیا از حذف {{ $asset->name }} اطمینان دارید؟')) { @this.deleteAsset({{ $asset->id }}) }" class="asset-card-btn delete">
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

        {{-- Connections Table --}}
        <div class="asset-section-card mt-4">
            <div class="asset-section-header">
                <h3 class="asset-section-title">
                    <i class="fas fa-link me-2"></i>اتصالات {{ $assetType->name }} به اتاق‌ها
                </h3>
            </div>
            @if($connections->isEmpty() || $connections->every(fn($c) => $c->rooms->isEmpty()))
                <div class="asset-empty-state">
                    <div class="empty-icon-asset">
                        <i class="fas fa-link"></i>
                    </div>
                    <h4 class="empty-title-asset">هیچ اتصالی یافت نشد</h4>
                    <p class="empty-desc-asset">{{ $assetType->name }}ها را به اتاق‌ها متصل کنید</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="asset-table">
                        <thead>
                            <tr>
                                <th>{{ $assetType->name }}</th>
                                <th>اتاق</th>
                                <th>واحد</th>
                                <th>نوع اتصال</th>
                                <th>تاریخ اتصال</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($connections as $asset)
                                @foreach($asset->rooms as $room)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $assetType->icon ?? 'fas fa-box' }} me-2"></i>
                                                {{ $asset->name }}
                                            </div>
                                        </td>
                                        <td>{{ $room->name }}</td>
                                        <td>{{ $room->unit->name ?? '-' }}</td>
                                        <td>{{ $room->pivot->connection_type ?? '-' }}</td>
                                        <td>{{ $room->pivot->connected_at ? \Carbon\Carbon::parse($room->pivot->connected_at)->format('Y/m/d') : '-' }}</td>
                                        <td>
                                            <div class="asset-table-actions">
                                                <button wire:click="editConnection({{ $room->pivot->id }})" class="table-action-btn edit">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="if(confirm('آیا از حذف این اتصال اطمینان دارید؟')) { @this.deleteConnection({{ $room->pivot->id }}) }" class="table-action-btn delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Asset Modal --}}
    @if($showAssetModal)
        <div class="modal-backdrop">
            <div class="modal-content">
                <div class="modal-header">
                    <h3>{{ $editingAssetId ? 'ویرایش ' . $assetType->name : 'افزودن ' . $assetType->name . ' جدید' }}</h3>
                    <button wire:click="closeAssetModal()" class="modal-close">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>نوع دارایی</label>
                        <input type="text" class="form-control" value="{{ $assetType->name }}" disabled>
                    </div>
                    <div class="form-group mb-3">
                        <label>نام {{ $assetType->name }} *</label>
                        <input type="text" wire:model="assetName" class="form-control" placeholder="نام {{ $assetType->name }}">
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
                        {{ $editingAssetId ? 'به‌روزرسانی' : 'ایجاد' }}
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
                        <label>{{ $assetType->name }}</label>
                        <select wire:model="selectedAsset" class="form-control">
                            <option value="">انتخاب کنید...</option>
                            @foreach($assets as $asset)
                                <option value="{{ $asset->id }}">{{ $asset->name }}</option>
                            @endforeach
                        </select>
                        @error('selectedAsset') <span class="error-message">{{ $message }}</span> @enderror
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

        .asset-filters-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 24px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .search-results-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 16px 24px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .search-results-count {
            color: white;
            font-weight: 600;
            font-size: 16px;
        }

        .clear-search-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }

        .clear-search-btn:hover {
            background: white;
            color: #667eea;
        }

        .connections-table-wrapper {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
        }

        .connections-table-title {
            color: #333;
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 12px 0;
        }

        .connections-table {
            width: 100%;
            border-collapse: collapse;
        }

        .connections-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 16px 20px;
            text-align: right;
            font-weight: 700;
            font-size: 16px;
            border-bottom: none;
        }

        .connections-table td {
            padding: 16px 20px;
            border-bottom: 1px solid #e0e0e0;
            color: #333;
            font-size: 15px;
            font-weight: 500;
        }

        .connections-table tr:hover {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
        }

        .connections-table tr:last-child td {
            border-bottom: none;
        }

        .no-connections-message {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            padding: 16px;
            margin-top: 16px;
            color: #666;
            font-size: 14px;
            text-align: center;
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

        .asset-empty-state {
            text-align: center;
            padding: 60px 20px;
        }

        .empty-icon-asset {
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

        .empty-title-asset {
            font-size: 18px;
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .empty-desc-asset {
            color: #666;
            margin-bottom: 20px;
        }

        .asset-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .asset-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .asset-card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .asset-card-icon {
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

        .asset-card-status {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
        }

        .asset-card-status.status-active {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }

        .asset-card-status.status-inactive {
            background: rgba(108, 117, 125, 0.2);
            color: #6c757d;
        }

        .asset-card-status.status-maintenance {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .asset-card-body {
            margin-bottom: 16px;
        }

        .asset-card-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0 0 8px 0;
            color: #333;
        }

        .asset-card-detail {
            font-size: 13px;
            color: #666;
            margin: 4px 0;
        }

        .asset-rooms {
            margin-top: 12px;
        }

        .asset-rooms-title {
            font-size: 12px;
            font-weight: 600;
            color: #555;
            margin: 0 0 8px 0;
        }

        .asset-rooms-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .asset-room-badge {
            background: rgba(255, 255, 255, 0.6);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            color: #333;
        }

        .asset-room-more {
            background: rgba(102, 126, 234, 0.2);
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            color: #667eea;
        }

        .asset-card-footer {
            display: flex;
            gap: 8px;
        }

        .asset-card-btn {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 13px;
        }

        .asset-card-btn.connect {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
        }

        .asset-card-btn.connect:hover {
            background: #667eea;
            color: white;
        }

        .asset-card-btn.edit {
            background: white;
            color: #667eea;
        }

        .asset-card-btn.edit:hover {
            background: #667eea;
            color: white;
        }

        .asset-card-btn.delete {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .asset-card-btn.delete:hover {
            background: #dc3545;
            color: white;
        }

        .asset-table {
            width: 100%;
            border-collapse: collapse;
        }

        .asset-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: right;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }

        .asset-table td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
            color: #333;
        }

        .asset-table-actions {
            display: flex;
            gap: 8px;
        }

        .table-action-btn {
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .table-action-btn.edit {
            background: rgba(102, 126, 234, 0.2);
            color: #667eea;
        }

        .table-action-btn.edit:hover {
            background: #667eea;
            color: white;
        }

        .table-action-btn.delete {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }

        .table-action-btn.delete:hover {
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
            .asset-header-modern {
                padding: 16px;
            }

            .asset-icon-modern {
                width: 50px;
                height: 50px;
                font-size: 24px;
            }

            .asset-title-modern {
                font-size: 20px;
            }

            .asset-section-card {
                padding: 16px;
            }

            .asset-card-footer {
                flex-direction: column;
            }
        }
    </style>
</div>
