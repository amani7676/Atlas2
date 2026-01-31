<div>


    <div class="card">

        <div class="card-header card-header-empty d-flex justify-content-between align-items-center">
            <span class="span-empty">تخت‌های خالی</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr class="tr-empty">
                        <th>#</th>
                        <th>اتاق</th>
                        <th>شماره تخت</th>
                        <th>کل اتاق</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $counter = 0;
                    @endphp
                    @forelse($emptyBeds as $bed)
                        @php $counter++; @endphp
                        <tr>
                            <td class="text-info">{{ $counter }}</td>
                            <td>{{ $bed->room->name ?? 'N/A' }}</td>
                            <td>{{ $bed->name }}</td>
                            <td>{{ $bed->room->bed_count ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('table_list') }}#{{ $bed->room->name ?? 'N/A' }}" 
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> افزودن
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                هیچ تخت خالی موجود نیست
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Bootstrap Modal --}}
    <div class="modal fade" id="addresidentModal" tabindex="-1" aria-labelledby="addresidentModalLabel"
         aria-hidden="true" wire:ignore.self style="margin-top: 2%">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="addresidentModalLabel">
                        <i class="fas fa-user-plus me-2"></i>
                        @if ($selectedBed)
                            افزودن اقامتگر به تخت {{ $selectedBed['name'] }} - اتاق {{ $selectedBed['room'] }}
                        @else
                            افزودن اقامتگر جدید
                        @endif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            style="margin-right: 10px" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveaddresident">
                        {{-- Personal Information Section --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>اطلاعات شخصی</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name" class="form-label">
                                            نام و نام خانوادگی <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('full_name') is-invalid @enderror" id="full_name"
                                               wire:model="full_name" placeholder="نام کامل اقامتگر را وارد کنید">
                                        @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone" class="form-label">
                                            شماره تلفن
                                        </label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" wire:model="phone">
                                        @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="birth_date_jalali" class="form-label">
                                            تاریخ تولد
                                        </label>
                                        <input type="text" wire:model.defer="birth_date_jalali" data-jdp
                                               id="birth_date_jalali"
                                               class="form-control @error('birth_date_jalali') is-invalid @enderror"
                                               placeholder="تاریخ تولد را انتخاب کنید">
                                        @error('birth_date_jalali')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="job" class="form-label">
                                            شغل
                                        </label>
                                        <select class="form-select @error('job') is-invalid @enderror" id="job"
                                                wire:model="job">
                                            <option value="">انتخاب کنید...</option>
                                            <option value="daneshjo_dolati">دانشجو دولتی</option>
                                            <option value="daneshjo_azad">دانشجو آزاد</option>
                                            <option value="daneshjo_other">دانشجو سایر دانشگاه ها</option>
                                            <option value="karmand_dolat">کارمند دولت</option>
                                            <option value="karmand_shakhse">کارمند شخصی</option>
                                            <option value="azad">آزاد</option>
                                            <option value="nurse">پرستار</option>
                                            <option value="other">سایر</option>
                                        </select>
                                        @error('job')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="referral_source" class="form-label">
                                        نحوه آشنایی
                                    </label>
                                    <select class="form-select @error('referral_source') is-invalid @enderror"
                                            id="referral_source" wire:model="referral_source">
                                        <option value="">انتخاب کنید...</option>
                                        <option value="university_introduction">معرفی دانشگاه</option>
                                        <option value="university_website">سایت دانشگاه</option>
                                        <option value="google">گوگل</option>
                                        <option value="map">نقشه</option>
                                        <option value="khobinja_website">سایت خواب اینجا</option>
                                        <option value="introducing_friends">معرفی دوستان</option>
                                        <option value="street">در سطح خیابان</option>
                                        <option value="divar">دیوار</option>
                                        <option value="other">سایر</option>
                                    </select>
                                    @error('referral_source')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Contract Information Section --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-file-contract me-2"></i>اطلاعات قرارداد</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">

                                    <div class="col-md-4 mb-3">
                                        <label for="payment_date" class="form-label">
                                            تاریخ سررسید <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('payment_date') is-invalid @enderror"
                                               id="payment_date" wire:model="payment_date" placeholder="1404/04/04">
                                        @error('payment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="state" class="form-label">
                                        وضعیت قرارداد <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('state') is-invalid @enderror" id="state"
                                            wire:model="state">
                                        <option value="">یک گزینه انتخاب کنید!!!</option>
                                        <option value="rezerve">رزرو</option>
                                        <option value="nightly">شبانه</option>
                                        <option value="active">فعال</option>
                                        <option value="leaving">در حال خروج</option>
                                    </select>
                                    @error('state')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Documents Section --}}
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>مدارک و تأییدیه‌ها</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="form"
                                                   wire:model="form">
                                            <label class="form-check-label" for="form">
                                                <i class="fas fa-file-alt me-1"></i>
                                                فرم تکمیل شده
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="document"
                                                   wire:model="document">
                                            <label class="form-check-label" for="document">
                                                <i class="fas fa-id-card me-1"></i>
                                                مدارک شناسایی
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="rent"
                                                   wire:model="rent">
                                            <label class="form-check-label" for="rent">
                                                <i class="fas fa-money-bill me-1"></i>
                                                پرداخت اجاره
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="trust"
                                                   wire:model="trust">
                                            <label class="form-check-label" for="trust">
                                                <i class="fas fa-handshake me-1"></i>
                                                ودیعه/ضمانت
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        انصراف
                    </button>
                    <button type="button" class="btn btn-success" wire:click="saveaddresident">
                            <span wire:loading.remove wire:target="saveaddresident">
                                <i class="fas fa-save me-1"></i>
                                ذخیره اقامتگر
                            </span>
                        <span wire:loading wire:target="saveaddresident">
                                <i class="fas fa-spinner fa-spin me-1"></i>
                                در حال ذخیره...
                            </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced CSS --}}
    <style>
        jdp-container {
            z-index: 10000 !important;
        }
    </style>

    {{-- Enhanced JavaScript --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jalali-datepicker/1.2.5/jalali-datepicker.min.js"></script>
    <script>
        document.addEventListener('livewire:init', function() {
            let modalInstance = null;
            const modalElement = document.getElementById('addresidentModal');
            let triggerButton = null;

            Livewire.on('show-modal', function() {
                triggerButton = document.activeElement;

                if (modalInstance) {
                    modalInstance.hide();
                }

                setTimeout(() => {
                    modalInstance = new bootstrap.Modal(modalElement, {
                        backdrop: 'static',
                        keyboard: true
                    });
                    modalInstance.show();

                    modalElement.addEventListener('shown.bs.modal', function() {
                        const firstInput = modalElement.querySelector('#full_name');
                        if (firstInput) {
                            firstInput.focus();
                        }

                        // راه‌اندازی datepicker بعد از باز شدن مودال
                        initJalaliDatePicker();
                    }, {
                        once: true
                    });
                }, 100);
            });

            Livewire.on('hide-modal', function() {
                if (modalInstance) {
                    modalInstance.hide();
                    modalInstance = null;
                }
            });

            modalElement.addEventListener('hidden.bs.modal', function() {
                modalInstance = null;
                Livewire.dispatch('closeModal');

                if (triggerButton) {
                    triggerButton.focus();
                }
            });

            // Handle form submission with Enter key
            modalElement.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    const saveButton = modalElement.querySelector('[wire\\:click="saveaddresident"]');
                    if (saveButton && !saveButton.disabled) {
                        saveButton.click();
                    }
                }
            });
        });

        // Phone formatting
        document.getElementById('phone').addEventListener('input', function(e) {
            // حذف همه کاراکترهای غیر عددی
            let value = e.target.value.replace(/\D/g, '');

            // اضافه کردن خط تیره در موقعیت‌های مناسب
            if (value.length > 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            if (value.length > 8) {
                value = value.substring(0, 8) + '-' + value.substring(8);
            }

            // محدود کردن طول به 13 کاراکتر (با احتساب خط تیره‌ها)
            e.target.value = value.substring(0, 13);
        });

        // Jalali DatePicker functions
        function initJalaliDatePicker() {
            // پاک کردن event listeners قبلی
            const input = document.querySelector('#birth_date_jalali');
            if (input) {
                // تنظیم JalaliDatePicker
                jalaliDatepicker.startWatch({
                    date: true,
                    time: false,
                    autoShow: true,
                    autoHide: true,
                    hideAfterChange: true,
                    persianDigits: true,
                    separatorChars: {
                        date: '/',
                        between: ' ',
                        time: ':'
                    },
                    selector: '#birth_date_jalali'
                });

                // Event listener برای تغییر تاریخ
                input.addEventListener('change', function() {
                    const selectedDate = this.value;
                    if (selectedDate) {
                    @this.set('birth_date_jalali', selectedDate);
                    }
                });

                // Event listener برای input event
                input.addEventListener('input', function() {
                    const selectedDate = this.value;
                    if (selectedDate) {
                    @this.set('birth_date_jalali', selectedDate);
                    }
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initJalaliDatePicker();
        });

        document.addEventListener('livewire:load', function() {
            initJalaliDatePicker();
        });

        document.addEventListener('livewire:update', function() {
            setTimeout(function() {
                initJalaliDatePicker();
            }, 100);
        });

        // Event listener برای form reset
        window.addEventListener('form-reset', function() {
            setTimeout(function() {
                initJalaliDatePicker();
            }, 100);
        });
    </script>
</div>
