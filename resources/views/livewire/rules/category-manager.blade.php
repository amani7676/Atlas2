<div>
    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="bi bi-folder"></i>
                مدیریت دسته بندی‌ها
            </h5>
            <button type="button" class="btn btn-primary" wire:click="openModal">
                <i class="bi bi-plus"></i>
                دسته بندی جدید
            </button>
        </div>
        <div class="card-body">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>نام</th>
                                <th>توضیحات</th>
                                <th>ترتیب نمایش</th>
                                <th>وضعیت</th>
                                <th>تعداد قوانین</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description ?: '-' }}</td>
                                    <td>{{ $category->display_order }}</td>
                                    <td>
                                        <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $category->is_active ? 'فعال' : 'غیرفعال' }}
                                        </span>
                                    </td>
                                    <td>{{ $category->rules()->count() }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal({{ $category->id }})">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-{{ $category->is_active ? 'warning' : 'success' }}" 
                                                    wire:click="toggleStatus({{ $category->id }})">
                                                <i class="bi bi-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" wire:click="delete({{ $category->id }})">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-5">
                    <i class="bi bi-folder-x fs-1"></i>
                    <p class="mt-3">هیچ دسته بندی یافت نشد</p>
                    <button type="button" class="btn btn-primary" wire:click="openModal">
                        ایجاد دسته بندی اول
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $editingId ? 'ویرایش دسته بندی' : 'ایجاد دسته بندی جدید' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="name" class="form-label">نام دسته بندی *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" wire:model="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">توضیحات</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" rows="3" wire:model="description"></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="display_order" class="form-label">ترتیب نمایش</label>
                                        <input type="number" class="form-control @error('display_order') is-invalid @enderror" 
                                               id="display_order" wire:model="display_order" min="0">
                                        @error('display_order')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label d-block">وضعیت</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_active" 
                                                   wire:model="is_active">
                                            <label class="form-check-label" for="is_active">
                                                فعال
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">انصراف</button>
                        <button type="button" class="btn btn-primary" wire:click="save">
                            {{ $editingId ? 'بروزرسانی' : 'ایجاد' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
