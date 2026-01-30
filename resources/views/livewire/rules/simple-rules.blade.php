@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .rules-container {
        direction: rtl;
        text-align: right;
    }
    .category-sidebar {
        max-height: 600px;
        overflow-y: auto;
    }
    .category-item {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .category-item:hover {
        background-color: #f8f9fa;
    }
    .category-item.active {
        background-color: #e3f2fd;
        border-right: 3px solid #2196f3;
    }
    .rule-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
        cursor: pointer;
    }
    .rule-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        border-left-color: #2196f3;
    }
    .rule-title {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .rule-title:hover {
        background-color: #f8f9fa;
    }
    .rule-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
        opacity: 0;
    }
    .rule-content.expanded {
        max-height: 500px;
        opacity: 1;
        padding-top: 1rem;
    }
    .rule-toggle-icon {
        transition: transform 0.3s ease;
    }
    .rule-toggle-icon.rotated {
        transform: rotate(180deg);
    }
    .modal-content {
        direction: rtl;
        text-align: right;
    }
    .content-textarea {
        min-height: 300px;
        resize: vertical;
        direction: rtl;
        text-align: right;
        font-family: Vazirmatn, Tahoma, Arial, sans-serif;
        font-size: 14px;
        line-height: 1.6;
    }
    .status-badge {
        font-size: 0.8rem;
    }
</style>
@endpush

<div class="rules-container">
    <!-- Flash Messages -->
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Categories Sidebar -->
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="bi bi-folder-fill me-2"></i>
                        دسته بندی‌ها
                    </h6>
                </div>
                <div class="card-body category-sidebar">
                    <div class="list-group">
                        <button type="button" 
                                class="list-group-item list-group-item-action category-item {{ !$selectedCategory ? 'active' : '' }}" 
                                wire:click="clearFilters">
                            <i class="bi bi-grid me-2"></i>
                            همه قوانین
                        </button>
                        @foreach($categories as $category)
                            <button type="button" 
                                    class="list-group-item list-group-item-action category-item {{ $selectedCategory == $category->id ? 'active' : '' }}" 
                                    wire:click="filterByCategory({{ $category->id }})">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <i class="bi bi-folder me-2"></i>
                                        {{ $category->name }}
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $category->rules()->count() }}</span>
                                </div>
                            </button>
                        @endforeach
                    </div>
                    
                    @if($categories->count() === 0)
                        <div class="text-center text-muted py-4">
                            <i class="bi bi-folder-x fs-1"></i>
                            <p class="mt-2">هیچ دسته بندی یافت نشد</p>
                            <a href="{{ route('category.management') }}" class="btn btn-sm btn-primary">
                                ایجاد دسته بندی
                            </a>
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
                                <i class="bi bi-file-text-fill me-2"></i>
                                قوانین
                                @if($selectedCategory)
                                    <small class="text-muted">
                                        - {{ $categories->find($selectedCategory)->name ?? '' }}
                                    </small>
                                @endif
                            </h5>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex justify-content-end gap-2">
                                <div class="input-group" style="max-width: 300px;">
                                    <span class="input-group-text">
                                        <i class="bi bi-search"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control" 
                                           placeholder="جستجوی قانون..." 
                                           wire:model.live="search">
                                </div>
                                <button type="button" class="btn btn-primary" wire:click="openModal">
                                    <i class="bi bi-plus-circle me-2"></i>
                                    قانون جدید
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($rules->count() > 0)
                        <div class="rules-list">
                            @foreach($rules as $rule)
                                <div class="rule-item mb-2" wire:key="rule-{{ $rule->id }}">
                                    <div class="card rule-card">
                                        <div class="rule-title p-3 d-flex justify-content-between align-items-center" 
                                             onclick="toggleRule({{ $rule->id }})">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-chevron-down rule-toggle-icon ms-2 me-3" id="icon-{{ $rule->id }}"></i>
                                                <h6 class="mb-0 me-2">{{ $rule->title }}</h6>
                                                <span class="badge status-badge {{ $rule->is_active ? 'bg-success' : 'bg-secondary' }} me-2">
                                                    {{ $rule->is_active ? 'فعال' : 'غیرفعال' }}
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <small class="text-muted me-3">
                                                    <i class="bi bi-folder me-1"></i>
                                                    {{ $rule->category->name }}
                                                </small>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-outline-primary" wire:click="openModal({{ $rule->id }})">
                                                        <i class="bi bi-pencil"></i>
                                                    </button>
                                                    <button type="button" 
                                                            class="btn btn-outline-{{ $rule->is_active ? 'warning' : 'success' }}" 
                                                            wire:click="toggleStatus({{ $rule->id }})">
                                                        <i class="bi bi-{{ $rule->is_active ? 'eye-slash' : 'eye' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" wire:click="delete({{ $rule->id }})">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="rule-content px-3" id="content-{{ $rule->id }}">
                                            <div class="mb-3">
                                                {!! $rule->content !!}
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center text-muted small">
                                                <span>
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ $rule->created_at->format('Y/m/d H:i') }}
                                                </span>
                                                <span>
                                                    <i class="bi bi-sort-numeric-down me-1"></i>
                                                    ترتیب: {{ $rule->display_order }}
                                                </span>
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
                            <h5 class="mt-3">هیچ قانونی یافت نشد</h5>
                            <p class="text-muted">
                                @if($selectedCategory)
                                    در این دسته بندی قانونی وجود ندارد
                                @else
                                    هنوز قانونی ایجاد نشده است
                                @endif
                            </p>
                            <button type="button" class="btn btn-primary" wire:click="openModal">
                                <i class="bi bi-plus-circle me-2"></i>
                                ایجاد قانون اول
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Rule Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-{{ $editingId ? 'pencil' : 'plus-circle' }} me-2"></i>
                            {{ $editingId ? 'ویرایش قانون' : 'ایجاد قانون جدید' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">
                                            <i class="bi bi-tag me-1"></i>
                                            عنوان قانون <span class="text-danger">*</span>
                                        </label>
                                        <input type="text" 
                                               class="form-control @error('title') is-invalid @enderror" 
                                               id="title" 
                                               wire:model="title"
                                               placeholder="عنوان قانون را وارد کنید">
                                        @error('title')
                                            <div class="invalid-feedback">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="rule_category_id" class="form-label">
                                            <i class="bi bi-folder me-1"></i>
                                            دسته بندی <span class="text-danger">*</span>
                                        </label>
                                        <select class="form-select @error('rule_category_id') is-invalid @enderror" 
                                                id="rule_category_id" 
                                                wire:model="rule_category_id">
                                            <option value="">انتخاب کنید...</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ $rule_category_id == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('rule_category_id')
                                            <div class="invalid-feedback">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="content" class="form-label">
                                    <i class="bi bi-text-paragraph me-1"></i>
                                    محتوای قانون <span class="text-danger">*</span>
                                </label>
                                <textarea id="ruleContent" 
                                          name="ruleContent" 
                                          class="form-control content-textarea @error('content') is-invalid @enderror" 
                                          wire:model="content"
                                          placeholder="محتوای قانون را وارد کنید...">{{ $content }}</textarea>
                                @error('content')
                                    <div class="text-danger">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="display_order" class="form-label">
                                            <i class="bi bi-sort-numeric-down me-1"></i>
                                            ترتیب نمایش <span class="text-danger">*</span>
                                        </label>
                                        <input type="number" 
                                               class="form-control @error('display_order') is-invalid @enderror" 
                                               id="display_order" 
                                               wire:model="display_order" 
                                               min="0"
                                               placeholder="0">
                                        @error('display_order')
                                            <div class="invalid-feedback">
                                                <i class="bi bi-exclamation-circle me-1"></i>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">
                                            <i class="bi bi-toggle-on me-1"></i>
                                            وضعیت
                                        </label>
                                        <div class="form-check form-switch mt-2">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   id="is_active" 
                                                   wire:model="is_active">
                                            <label class="form-check-label" for="is_active">
                                                <span class="{{ $is_active ? 'text-success' : 'text-secondary' }}">
                                                    {{ $is_active ? 'فعال' : 'غیرفعال' }}
                                                </span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                <i class="bi bi-x-circle me-2"></i>
                                انصراف
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-{{ $editingId ? 'check-circle' : 'plus-circle' }} me-2"></i>
                                {{ $editingId ? 'بروزرسانی' : 'ایجاد' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
    
    <!-- Script to initialize TinyMCE when modal opens -->
    @if($showModal)
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.dispatch('open-rule-editor');
            });
        </script>
    @endif
</div>

@push('scripts')
<script>
    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });

    // Toggle rule content
    function toggleRule(ruleId) {
        const content = document.getElementById('content-' + ruleId);
        const icon = document.getElementById('icon-' + ruleId);
        
        if (content.classList.contains('expanded')) {
            content.classList.remove('expanded');
            icon.classList.remove('rotated');
        } else {
            // Close all other expanded rules
            document.querySelectorAll('.rule-content.expanded').forEach(function(el) {
                el.classList.remove('expanded');
            });
            document.querySelectorAll('.rule-toggle-icon.rotated').forEach(function(el) {
                el.classList.remove('rotated');
            });
            
            // Open this rule
            content.classList.add('expanded');
            icon.classList.add('rotated');
        }
    }

    // Prevent event bubbling for action buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-group')) {
            e.stopPropagation();
        }
    });
</script>
@endpush
