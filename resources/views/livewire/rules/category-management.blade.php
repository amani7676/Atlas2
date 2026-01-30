@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    .category-container {
        direction: rtl;
        text-align: right;
    }
    .table th {
        background-color: #f8f9fa;
        font-weight: 600;
    }
    .status-badge {
        font-size: 0.8rem;
    }
    .btn-group-sm > .btn {
        font-size: 0.875rem;
    }
    .modal-content {
        direction: rtl;
        text-align: right;
    }
    .form-check-input:checked {
        background-color: #0d6efd;
        border-color: #0d6efd;
    }
</style>
@endpush

<div class="category-container">
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

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="mb-0">
                <i class="bi bi-folder-fill text-primary"></i>
                مدیریت دسته بندی‌ها
            </h3>
            <p class="text-muted mb-0">ایجاد و مدیریت دسته بندی‌ها برای قوانین</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="openModal">
            <i class="bi bi-plus-circle me-2"></i>
            ایجاد دسته بندی جدید
        </button>
    </div>

    <!-- Search -->
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text">
                    <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control" placeholder="جستجوی دسته بندی..." wire:model.live="search">
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-body p-0">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 50px;">#</th>
                                <th>نام دسته بندی</th>
                                <th>توضیحات</th>
                                <th style="width: 100px;">ترتیب</th>
                                <th style="width: 100px;">وضعیت</th>
                                <th style="width: 100px;">قوانین</th>
                                <th style="width: 120px;">عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $index => $category)
                                <tr>
                                    <td>{{ $categories->firstItem() + $index }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-folder-fill text-warning me-2"></i>
                                            <strong>{{ $category->name }}</strong>
                                        </div>
                                    </td>
                                    <td>
                                        {{ $category->description ?: '<span class="text-muted">بدون توضیحات</span>' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">
                                            {{ $category->display_order }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge status-badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            <i class="bi bi-{{ $category->is_active ? 'check-circle' : 'x-circle' }} me-1"></i>
                                            {{ $category->is_active ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $category->rules()->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-primary" 
                                                    wire:click="openModal({{ $category->id }})"
                                                    title="ویرایش">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                    wire:click="toggleStatus({{ $category->id }})"
                                                    title="{{ $category->is_active ? 'غیرفعال کردن' : 'فعال کردن' }}">
                                                <i class="bi bi-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    wire:click="delete({{ $category->id }})"
                                                    title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="p-3 border-top">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-folder-x fs-1 text-muted"></i>
                    <h5 class="text-muted mt-3">هیچ دسته بندی یافت نشد</h5>
                    <p class="text-muted">اولین دسته بندی خود را ایجاد کنید</p>
                    <button type="button" class="btn btn-primary" wire:click="openModal">
                        <i class="bi bi-plus-circle me-2"></i>
                        ایجاد دسته بندی اول
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="bi bi-{{ $editingId ? 'pencil' : 'plus-circle' }} me-2"></i>
                            {{ $editingId ? 'ویرایش دسته بندی' : 'ایجاد دسته بندی جدید' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="name" class="form-label">
                                    <i class="bi bi-tag me-1"></i>
                                    نام دسته بندی <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       wire:model="name"
                                       placeholder="نام دسته بندی را وارد کنید">
                                @error('name')
                                    <div class="invalid-feedback">
                                        <i class="bi bi-exclamation-circle me-1"></i>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="description" class="form-label">
                                    <i class="bi bi-text-paragraph me-1"></i>
                                    توضیحات
                                </label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" 
                                          rows="3" 
                                          wire:model="description"
                                          placeholder="توضیحات دسته بندی (اختیاری)"></textarea>
                                @error('description')
                                    <div class="invalid-feedback">
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
</script>
@endpush
