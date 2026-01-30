@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .rules-container {
        direction: rtl;
        text-align: right;
    }
    .category-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .category-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .category-card.active {
        border-color: #0d6efd;
        background-color: #f8f9fa;
    }
    .rule-card {
        transition: all 0.3s ease;
    }
    .rule-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .modal-content {
        direction: rtl;
        text-align: right;
    }
    .tinymce-container {
        min-height: 400px;
    }
    .status-badge {
        font-size: 0.8rem;
    }
    .search-box {
        max-width: 300px;
    }
    .category-list {
        max-height: 600px;
        overflow-y: auto;
    }
    .rule-content-preview {
        max-height: 100px;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('livewire:init', () => {
        console.log('Livewire initialized for rules manager');
        
        // Handle modal events
        Livewire.on('category-saved', () => {
            console.log('Category saved event received');
            showToast('دسته بندی با موفقیت ذخیره شد', 'success');
        });

        Livewire.on('category-deleted', () => {
            console.log('Category deleted event received');
            showToast('دسته بندی با موفقیت حذف شد', 'success');
        });

        Livewire.on('rule-saved', () => {
            console.log('Rule saved event received');
            showToast('قانون با موفقیت ذخیره شد', 'success');
        });

        Livewire.on('rule-deleted', () => {
            console.log('Rule deleted event received');
            showToast('قانون با موفقیت حذف شد', 'success');
        });

        Livewire.on('error', (message) => {
            console.log('Error event received:', message);
            showToast(message, 'error');
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            console.log('Showing toast:', message, type);
            const toast = document.createElement('div');
            toast.className = `alert alert-${type === 'error' ? 'danger' : type} position-fixed top-0 start-50 translate-middle-x mt-3`;
            toast.style.zIndex = '9999';
            toast.style.minWidth = '300px';
            toast.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
                    ${message}
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // Add click listeners for debugging
        document.addEventListener('click', function(e) {
            if (e.target.matches('[wire\\:click*="saveRule"]')) {
                console.log('Save rule button clicked');
                // Check if ruleContent has value
                const ruleContent = document.getElementById('ruleContent');
                if (ruleContent) {
                    console.log('Rule content value:', ruleContent.value);
                    // Check if TinyMCE is initialized
                    const editor = tinymce.get('ruleContent');
                    if (editor) {
                        console.log('TinyMCE editor content:', editor.getContent());
                    }
                }
            }
        });
    });
</script>
@endpush

<!-- Include TinyMCE configuration -->
<x-head.tinymce-config />

<div class="rules-container">
    <!-- Test Button -->
    <div class="mb-3">
        <button type="button" class="btn btn-info" wire:click="testSave">
            <i class="bi bi-bug"></i>
            تست Livewire
        </button>
    </div>
    
    <div class="row">
        <!-- Categories Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-folder"></i>
                        دسته بندی‌ها
                    </h5>
                    <button type="button" class="btn btn-sm btn-primary" wire:click="openCategoryModal()">
                        <i class="bi bi-plus"></i>
                    </button>
                </div>
                <div class="card-body category-list">
                    @if($categories->count() > 0)
                        <div class="list-group list-group-flush">
                            <button type="button" class="list-group-item list-group-item-action {{ !$selectedCategory ? 'active' : '' }}" 
                                    wire:click="clearFilters()">
                                <i class="bi bi-grid"></i>
                                همه قوانین
                            </button>
                            @foreach($categories as $category)
                                <div class="list-group-item list-group-item-action category-card {{ $selectedCategory == $category->id ? 'active' : '' }}"
                                     wire:click="filterByCategory({{ $category->id }})">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">{{ $category->name }}</h6>
                                            <small class="text-muted">{{ $category->rules()->count() }} قانون</small>
                                        </div>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><button class="dropdown-item" wire:click="openCategoryModal({{ $category->id }})">
                                                    <i class="bi bi-pencil"></i> ویرایش
                                                </button></li>
                                                <li><button class="dropdown-item" wire:click="toggleCategoryStatus({{ $category->id }})">
                                                    <i class="bi bi-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i> 
                                                    {{ $category->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}
                                                </button></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><button class="dropdown-item text-danger" wire:click="deleteCategory({{ $category->id }})">
                                                    <i class="bi bi-trash"></i> حذف
                                                </button></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-folder-x fs-1"></i>
                            <p class="mt-2">هیچ دسته بندی یافت نشد</p>
                            <button type="button" class="btn btn-primary" wire:click="openCategoryModal()">
                                ایجاد دسته بندی اول
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rules Content -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                <i class="bi bi-file-text"></i>
                                قوانین
                                @if($selectedCategory)
                                    <small class="text-muted">- {{ $categories->find($selectedCategory)->name ?? '' }}</small>
                                @endif
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end gap-2">
                                <div class="input-group search-box">
                                    <input type="text" class="form-control" placeholder="جستجوی قانون..." wire:model.live="search">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                </div>
                                <button type="button" class="btn btn-primary" wire:click="openRuleModal()">
                                    <i class="bi bi-plus"></i>
                                    قانون جدید
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($rules->count() > 0)
                        <div class="row">
                            @foreach($rules as $rule)
                                <div class="col-md-6 mb-3">
                                    <div class="card rule-card h-100">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $rule->title }}</h6>
                                            <div>
                                                <span class="badge status-badge {{ $rule->is_active ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $rule->is_active ? 'فعال' : 'غیرفعال' }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="rule-content-preview" wire:ignore>
                                                {!! $rule->content !!}
                                            </div>
                                            <small class="text-muted">
                                                <i class="bi bi-folder"></i> {{ $rule->category->name }}
                                            </small>
                                        </div>
                                        <div class="card-footer">
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openRuleModal({{ $rule->id }})">
                                                    <i class="bi bi-pencil"></i> ویرایش
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-{{ $rule->is_active ? 'warning' : 'success' }}" 
                                                        wire:click="toggleRuleStatus({{ $rule->id }})">
                                                    <i class="bi bi-{{ $rule->is_active ? 'eye-slash' : 'eye' }}"></i> 
                                                    {{ $rule->is_active ? 'غیرفعال' : 'فعال' }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteRule({{ $rule->id }})">
                                                    <i class="bi bi-trash"></i> حذف
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $rules->links() }}
                        </div>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-file-x fs-1"></i>
                            <p class="mt-3">هیچ قانونی یافت نشد</p>
                            @if($selectedCategory)
                                <button type="button" class="btn btn-outline-primary" wire:click="clearFilters">
                                    نمایش همه قوانین
                                </button>
                            @else
                                <button type="button" class="btn btn-primary" wire:click="openRuleModal">
                                    ایجاد قانون اول
                                </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Modal -->
@if($showCategoryModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingCategory ? 'ویرایش دسته بندی' : 'ایجاد دسته بندی جدید' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeCategoryModal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">نام دسته بندی *</label>
                            <input type="text" class="form-control @error('categoryName') is-invalid @enderror" 
                                   id="categoryName" wire:model="categoryName">
                            @error('categoryName')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">توضیحات</label>
                            <textarea class="form-control @error('categoryDescription') is-invalid @enderror" 
                                      id="categoryDescription" rows="3" wire:model="categoryDescription"></textarea>
                            @error('categoryDescription')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="categoryDisplayOrder" class="form-label">ترتیب نمایش</label>
                                    <input type="number" class="form-control @error('categoryDisplayOrder') is-invalid @enderror" 
                                           id="categoryDisplayOrder" wire:model="categoryDisplayOrder" min="0">
                                    @error('categoryDisplayOrder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label d-block">وضعیت</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="categoryIsActive" 
                                               wire:model="categoryIsActive">
                                        <label class="form-check-label" for="categoryIsActive">
                                            فعال
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCategoryModal">انصراف</button>
                    <button type="button" class="btn btn-primary" wire:click="saveCategory">
                        {{ $editingCategory ? 'بروزرسانی' : 'ایجاد' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Rule Modal -->
@if($showRuleModal)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $editingRule ? 'ویرایش قانون' : 'ایجاد قانون جدید' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeRuleModal"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="ruleTitle" class="form-label">عنوان قانون *</label>
                                    <input type="text" class="form-control @error('ruleTitle') is-invalid @enderror" 
                                           id="ruleTitle" wire:model="ruleTitle">
                                    @error('ruleTitle')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label for="ruleCategoryId" class="form-label">دسته بندی *</label>
                                    <select class="form-select @error('ruleCategoryId') is-invalid @enderror" 
                                            id="ruleCategoryId" wire:model="ruleCategoryId">
                                        <option value="">انتخاب کنید...</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ $ruleCategoryId == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ruleCategoryId')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="ruleContent" class="form-label">محتوای قانون *</label>
                            <div class="tinymce-container">
                                <x-forms.tinymce-editor :content="$ruleContent" />
                            </div>
                            @error('ruleContent')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="ruleDisplayOrder" class="form-label">ترتیب نمایش</label>
                                    <input type="number" class="form-control @error('ruleDisplayOrder') is-invalid @enderror" 
                                           id="ruleDisplayOrder" wire:model="ruleDisplayOrder" min="0">
                                    @error('ruleDisplayOrder')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label d-block">وضعیت</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="ruleIsActive" 
                                               wire:model="ruleIsActive">
                                        <label class="form-check-label" for="ruleIsActive">
                                            فعال
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeRuleModal">انصراف</button>
                    <button type="button" class="btn btn-primary" wire:click="saveRule">
                        {{ $editingRule ? 'بروزرسانی' : 'ایجاد' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.dispatch('open-rule-editor');
        });
    </script>
@endif
