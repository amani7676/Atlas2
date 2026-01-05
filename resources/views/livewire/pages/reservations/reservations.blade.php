<div>
    <div class="container-fluid mt-3 px-3 px-md-4" style="width: 90%; max-width: 100%;">


        {{-- Header Section --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="text-primary">
                        <i class="fas fa-bed me-2"></i>
                        رزروها
                    </h2>
                    <button wire:click="showCreateForm" class="btn btn-success">
                        <i class="fas fa-plus me-2"></i>
                        رزرو جدید
                    </button>
                </div>
            </div>
        </div>

        {{-- Form Section --}}
        @if($showForm)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-{{ $editingId ? 'edit' : 'plus' }} me-2"></i>
                            {{ $editingId ? 'ویرایش رزرو' : 'رزرو جدید' }}
                        </h5>
                        <button wire:click="hideForm" class="btn btn-sm btn-outline-light">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="row">
                            {{-- Personal Information --}}
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">نام و نام خانوادگی</label>
                                    <input type="text"
                                           wire:model="full_name"
                                           class="form-control @error('full_name') is-invalid @enderror"
                                           placeholder="نام کامل را وارد کنید">
                                    @error('full_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">شماره تماس <span class="text-danger">*</span></label>
                                    <input type="text"
                                           wire:model.live="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder="0912-345-6789"
                                           maxlength="13"
                                           pattern="09\d{2}-\d{3}-\d{4}"
                                           required>
                                    @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">فرمت: 0912-345-6789 (11 رقم با 09 شروع شود)</small>
                                </div>
                            </div>


                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label required">اولویت</label>
                                    <select wire:model="priority"
                                            class="form-select @error('priority') is-invalid @enderror">
                                        <option value="low">کم</option>
                                        <option value="medium">متوسط</option>
                                        <option value="high">بالا</option>
                                    </select>
                                    @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Note --}}
                            <div class="col-12">
                                <div class="mb-3">
                                    <label class="form-label">یادداشت</label>
                                    <textarea wire:model="note"
                                              class="form-control @error('note') is-invalid @enderror"
                                              rows="3"
                                              placeholder="یادداشت اختیاری..."></textarea>
                                    @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Form Actions --}}
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                {{ $editingId ? 'ویرایش' : 'ذخیره' }}
                            </button>
                            <button type="button" wire:click="hideForm" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                انصراف
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endif

        {{-- Filters Section --}}
{{--        <div class="card mb-4">--}}
{{--            <div class="card-body">--}}
{{--                <div class="row">--}}
{{--                    <div class="col-md-6">--}}
{{--                        <div class="mb-3">--}}
{{--                            <label class="form-label">جستجو</label>--}}
{{--                            <input type="text"--}}
{{--                                   wire:model.live="search"--}}
{{--                                   class="form-control"--}}
{{--                                   placeholder="جستجو در نام یا شماره تماس...">--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="col-md-6">--}}
{{--                        <div class="mb-3">--}}
{{--                            <label class="form-label">فیلتر بر اساس اولویت</label>--}}
{{--                            <select wire:model.live="filterPriority" class="form-select">--}}
{{--                                <option value="">همه اولویت‌ها</option>--}}
{{--                                <option value="high">بالا</option>--}}
{{--                                <option value="medium">متوسط</option>--}}
{{--                                <option value="low">کم</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}

        {{-- Reserves List --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-list me-2"></i>
                    لیست رزروها ({{ count($reserves) }} مورد)
                </h5>
            </div>
            <div class="card-body p-0">
                @if(count($reserves) > 0)
                    <!-- کانتینر با کلاس سفارشی برای کنترل اسکرول -->
                    <div class="conditional-scroll-container">
                        <table class="table table-hover mb-0 conditional-scroll-table">
                            <thead class="table-light">
                            <tr>
                                <th>نام و نام خانوادگی</th>
                                <th>شماره تماس</th>
                                <th>اولویت</th>
                                <th>یادداشت</th>
                                <th>تاریخ ایجاد</th>
                                <th>عملیات</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($reserves as $reserve)
                                <tr>
                                    <td>
                                        <strong>{{ $reserve->full_name }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ $reserve->phone }}</span>
                                    </td>

                                    <td>
                                        <span class="{{ $this->getPriorityClass($reserve->priority) }}">
                                            {{ $this->getPriorityLabel($reserve->priority) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($reserve->note)
                                            <span class="text-muted"
                                                  title="{{ $reserve->note }}"
                                                  data-bs-toggle="tooltip">
                                                {{ $reserve->note}}
                                            </span>
                                        @else
                                            <span class="text-muted fst-italic">بدون یادداشت</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $reserve->created_at_jalali }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm action-buttons">
                                            <button wire:click="edit({{ $reserve->id }})"
                                                    class="btn btn-outline-primary"
                                                    title="ویرایش">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="delete({{ $reserve->id }})"
                                                    class="btn btn-outline-danger"
                                                    title="حذف"
                                                    onclick="return confirm('آیا از حذف این رزرو اطمینان دارید؟')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">هیچ رزروی یافت نشد</h5>
                        <p class="text-muted">برای شروع، یک رزرو جدید ایجاد کنید.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .required::after {
                content: " *";
                color: red;
            }

            .table th {
                border-top: none;
                font-weight: 600;
                color: #495057;
            }

            .btn-group-sm > .btn {
                padding: 0.25rem 0.5rem;
                font-size: 0.875rem;
            }

            .card {
                border: none;
                box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            }

            .card-header {
                background-color: #f8f9fa;
                border-bottom: 1px solid #e9ecef;
            }

            /* --- استایل‌های واکنش‌گرا برای اسکرول افقی --- */
            .conditional-scroll-container {
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                width: 100%;
            }

            .conditional-scroll-table {
                min-width: 800px;
                width: 100%;
                margin-bottom: 0;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                white-space: nowrap;
                vertical-align: middle;
            }

            .action-buttons {
                white-space: nowrap;
            }

            /* موبایل (تا 576px) */
            @media (max-width: 575.98px) {
                .conditional-scroll-table {
                    min-width: 700px;
                    font-size: 0.85rem;
                }

                .conditional-scroll-table th,
                .conditional-scroll-table td {
                    padding: 0.4rem 0.5rem;
                }

                .btn-group-sm > .btn {
                    padding: 0.3rem 0.6rem;
                    font-size: 0.9rem;
                }
            }

            /* تبلت (576px تا 768px) */
            @media (min-width: 576px) and (max-width: 767.98px) {
                .conditional-scroll-table {
                    min-width: 750px;
                }

                .conditional-scroll-table th,
                .conditional-scroll-table td {
                    padding: 0.5rem 0.6rem;
                }
            }

            /* تبلت (768px تا 992px) */
            @media (min-width: 768px) and (max-width: 991.98px) {
                .conditional-scroll-table {
                    min-width: 800px;
                }

                .conditional-scroll-table th,
                .conditional-scroll-table td {
                    padding: 0.6rem 0.7rem;
                }
            }

            /* موبایل - تنظیم container */
            @media (max-width: 767.98px) {
                .container-fluid {
                    width: 100% !important;
                    padding-left: 0.75rem !important;
                    padding-right: 0.75rem !important;
                }

                .card-header h5 {
                    font-size: 18px !important;
                }

                .btn {
                    font-size: 18px !important;
                    padding: 0.5rem 1rem !important;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Initialize tooltips
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl);
                });

                // فرمت‌دهی خودکار شماره تلفن
                const phoneInput = document.querySelector('input[wire\\:model="phone"]');
                if (phoneInput) {
                    phoneInput.addEventListener('input', function (event) {
                        let value = event.target.value.replace(/\D/g, ''); // حذف کاراکترهای غیر عددی
                        
                        // اگر با 0 شروع نشود، اضافه کردن 0
                        if (value.length > 0 && !value.startsWith('0')) {
                            value = '0' + value;
                        }
                        
                        // محدود کردن به 11 رقم
                        value = value.substring(0, 11);
                        
                        // فرمت کردن شماره تلفن
                        let formattedValue = '';
                        if (value.length > 0) {
                            formattedValue = value.substring(0, 4);
                        }
                        if (value.length > 4) {
                            formattedValue += '-' + value.substring(4, 7);
                        }
                        if (value.length > 7) {
                            formattedValue += '-' + value.substring(7, 11);
                        }
                        
                        event.target.value = formattedValue;
                        
                        // به‌روزرسانی Livewire
                        if (window.Livewire) {
                            const component = event.target.closest('[wire\\:id]');
                            if (component) {
                                const wireId = component.getAttribute('wire:id');
                                const livewireComponent = window.Livewire.find(wireId);
                                if (livewireComponent) {
                                    livewireComponent.set('phone', formattedValue);
                                }
                            }
                        }
                    });

                    // اعتبارسنجی هنگام blur
                    phoneInput.addEventListener('blur', function (event) {
                        let value = event.target.value.replace(/\D/g, '');
                        
                        if (value.length > 0 && value.length !== 11) {
                            event.target.classList.add('is-invalid');
                        } else if (value.length === 11 && value.startsWith('09')) {
                            event.target.classList.remove('is-invalid');
                        }
                    });
                }
            });

            // گوش دادن به رویداد 'show-toast'
            window.addEventListener('show-toast', (event) => {
                const params = event.detail[0];

                if (typeof window.cuteToast === 'function') {
                    cuteToast({
                        type: params.type,
                        title: params.title,
                        description: params.description,
                        timer: params.timer
                    });
                } else {
                    console.error('cuteToast function is not available on window object.');
                }
            });

        </script>
    @endpush
</div>
