<div>
    <div class="modal fade" id="residentModal" tabindex="-1" aria-labelledby="residentModalLabel"
         aria-hidden="true" wire:ignore.self style="margin-top: 3.5% !important;">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <div class="modal-header bg-{{ $this->modalMode == 'edit' ? 'warning' : 'danger' }} text-white">
                    <h5 class="modal-title" id="residentModalLabel">
                        @if ($modalMode === 'edit')
                            <i class="fas fa-user-edit me-2"></i>
                            ویرایش اطلاعات اقامتگر
                        @else
                            <i class="fas fa-user-plus me-2"></i>
                            @if ($this->selectedBed)
                                افزودن اقامتگر به تخت {{ $this->selectedBed['name'] }} -
                                اتاق {{ $this->selectedBed['room'] }}
                            @else
                                افزودن اقامتگر جدید
                            @endif
                        @endif
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            style="margin-right: 10px" aria-label="بستن"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="saveResident">
                        {{-- Bed Info (فقط در حالت ویرایش نمایش داده شود) --}}
                        @if ($modalMode === 'edit' && $this->selectedBed)
                            <div class="alert alert-info mb-4">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>تخت:</strong> {{ $this->selectedBed['name'] }} -
                                <strong>اتاق:</strong> {{ $this->selectedBed['room'] }}
                            </div>
                        @endif

                        {{-- Personal Information Section --}}
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>اطلاعات شخصی</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="full_name_modal" class="form-label">
                                            نام و نام خانوادگی <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('full_name_modal') is-invalid @enderror"
                                               id="full_name_modal"
                                               wire:model="full_name_modal" placeholder="نام کامل اقامتگر را وارد کنید">
                                        @error('full_name_modal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="phone_modal" class="form-label">
                                            شماره تلفن
                                        </label>
                                        <input type="text"
                                               class="form-control @error('phone_modal') is-invalid @enderror"
                                               id="phone_modal" wire:model="phone_modal"
                                               maxlength="13">
                                        @error('phone_modal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="age_modal" class="form-label">
                                            سن
                                        </label>
{{--                                        <input type="number"--}}
{{--                                               class="form-control @error('age_modal') is-invalid @enderror"--}}
{{--                                               id="age_modal" wire:model="age_modal" min="1" max="120"--}}
{{--                                               placeholder="سن به سال">--}}


{{--                                        @error('age_modal')--}}
{{--                                        <div class="invalid-feedback">{{ $message }}</div>--}}
{{--                                        @enderror--}}


                                        <div class="mb-4">
{{--                                            <label class="block text-gray-700 text-sm font-bold mb-2">--}}
{{--                                                تاریخ تولد--}}
{{--                                            </label>--}}
                                            <input type="text" wire:model.defer="birth_date_jalali_modal" data-jdp
                                                   id="birth_date_jalali_modal"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500"
                                                   placeholder="تاریخ تولد را انتخاب کنید">
                                            @error('birth_date_jalali_modal') <span
                                                class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="job_modal" class="form-label">
                                            شغل
                                        </label>
                                        <select class="form-select @error('job_modal') is-invalid @enderror"
                                                id="job_modal"
                                                wire:model="job_modal">
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
                                        @error('job_modal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="referral_source_modal" class="form-label">
                                        نحوه آشنایی
                                    </label>
                                    <select class="form-select @error('referral_source_modal') is-invalid @enderror"
                                            id="referral_source_modal" wire:model="referral_source_modal">
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
                                    @error('referral_source_modal')
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
                                        <label for="payment_date_modal" class="form-label">
                                            تاریخ سررسید <span class="text-danger">*</span>
                                        </label>
                                        <input type="text"
                                               class="form-control @error('payment_date_modal') is-invalid @enderror"
                                               id="payment_date_modal" wire:model="payment_date_modal"
                                               placeholder="1404/04/04">
                                        @error('payment_date_modal')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="state_modal" class="form-label">
                                        وضعیت قرارداد <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('state_modal') is-invalid @enderror"
                                            id="state_modal"
                                            wire:model="state_modal">
                                        <option value="">یک گزینه انتخاب کنید!!!</option>
                                        <option value="rezerve">رزرو</option>
                                        <option value="nightly">شبانه</option>
                                        <option value="active">فعال</option>
                                        <option value="leaving">در حال خروج</option>
                                    </select>
                                    @error('state_modal')
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
                                        <div
                                            class="form-check d-flex justify-content-between flex-row-reverse align-items-center"
                                        ">
                                        <input class="form-check-input" type="checkbox" id="form_modal"
                                               wire:model="form_modal">
                                        <label class="form-check-label" for="form_modal">
                                            <i class="fas fa-file-alt me-1"></i>
                                            فرم تکمیل شده
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <div
                                        class="form-check d-flex justify-content-between flex-row-reverse align-items-center"
                                    ">
                                    <input class="form-check-input" type="checkbox" id="document_modal"
                                           wire:model="document_modal">
                                    <label class="form-check-label" for="document_modal">
                                        <i class="fas fa-id-card me-1"></i>
                                        مدارک شناسایی
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div
                                    class="form-check d-flex justify-content-between flex-row-reverse align-items-center"
                                ">
                                <input class="form-check-input" type="checkbox" id="rent_modal"
                                       wire:model="rent_modal">
                                <label class="form-check-label" for="rent_modal">
                                    <i class="fas fa-money-bill me-1"></i>
                                    پرداخت اجاره
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="form-check d-flex justify-content-between flex-row-reverse align-items-center"
                            ">
                            <input class="form-check-input" type="checkbox" id="trust_modal"
                                   wire:model="trust_modal">
                            <label class="form-check-label" for="trust_modal">
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
    <button type="button" class="btn {{ $modalMode === 'edit' ? 'btn-warning' : 'btn-success' }}"
            wire:click="saveResident">
                    <span wire:loading.remove wire:target="saveResident">
                        @if ($modalMode === 'edit')
                            <i class="fas fa-save me-1"></i>
                            بروزرسانی اطلاعات
                        @else
                            <i class="fas fa-save me-1"></i>
                            ذخیره اقامتگر
                        @endif
                    </span>
        <span wire:loading wire:target="saveResident">
                        <i class="fas fa-spinner fa-spin me-1"></i>
                        در حال {{ $modalMode === 'edit' ? 'بروزرسانی' : 'ذخیره' }}...
                    </span>
    </button>
</div>
</div>
</div>
</div>
<style>
    jdp-container {
        z-index: 10000 !important;
    }
</style>

<script>
    document.addEventListener('livewire:init', function () {
        let modalInstance = null;
        const modalElement = document.getElementById('residentModal');
        let triggerButton = null;

        Livewire.on('show-modal', function () {
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

                modalElement.addEventListener('shown.bs.modal', function () {
                    const firstInput = modalElement.querySelector('#full_name_modal');
                    if (firstInput) {
                        firstInput.focus();
                    }
                }, {
                    once: true
                });


            }, 100);
        });

        Livewire.on('hide-modal', function () {
            if (modalInstance) {
                modalInstance.hide();
                modalInstance = null;
            }
        });

        modalElement.addEventListener('hidden.bs.modal', function () {
            modalInstance = null;
            Livewire.dispatch('closeModal');

            if (triggerButton) {
                triggerButton.focus();
            }
        });

        // Handle form submission with Enter key
        modalElement.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                e.preventDefault();
                const saveButton = modalElement.querySelector('[wire\\:click="saveResident"]');
                if (saveButton && !saveButton.disabled) {
                    saveButton.click();
                }
            }
        });


        // --- شروع بخش جدید برای فرمت‌دهی شماره تلفن ---
        const phoneInput = document.getElementById('phone_modal');
        if (phoneInput) {
            phoneInput.addEventListener('input', function (event) {
                let value = event.target.value.replace(/\D/g, ''); // حذف کاراکترهای غیر عددی
                let formattedValue = '';

                if (value.length > 0) {
                    formattedValue += value.substring(0, 4);
                }
                if (value.length > 4) {
                    formattedValue += '-' + value.substring(4, 7);
                }
                if (value.length > 7) {
                    formattedValue += '-' + value.substring(7, 11);
                }

                event.target.value = formattedValue;

                // این بخش برای اطمینان از همگام‌سازی با Livewire ضروری است
                // Livewire معمولاً با تغییرات مستقیم در value المنت به صورت خودکار به‌روز نمی‌شود
                // مگر اینکه رویداد 'input' دوباره فعال شود.
                if (Livewire.find(event.target.closest('[wire\\:id]').getAttribute('wire:id'))) {
                    Livewire.find(event.target.closest('[wire\\:id]').getAttribute('wire:id')).set('phone_modal', formattedValue);
                }
            });
        }
        // --- پایان بخش جدید ---

    });


    //     datepicker
    document.addEventListener('DOMContentLoaded', function () {
        initJalaliDatePicker();
    });

    document.addEventListener('livewire:load', function () {
        initJalaliDatePicker();
    });

    document.addEventListener('livewire:update', function () {
        setTimeout(function () {
            initJalaliDatePicker();
        }, 100);
    });

    function initJalaliDatePicker() {
        // پاک کردن event listeners قبلی
        const input = document.querySelector('#birth_date_jalali_modal');
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
                selector: '#birth_date_jalali_modal'
            });

            // Event listener برای تغییر تاریخ
            input.addEventListener('change', function () {
                const selectedDate = this.value;
                if (selectedDate) {
                @this.set('birth_date_jalali_modal', selectedDate)
                    ;
                }
            });

            // Event listener برای input event
            input.addEventListener('input', function () {
                const selectedDate = this.value;
                if (selectedDate) {
                @this.set('birth_date_jalali_modal', selectedDate)
                    ;
                }
            });
        }
    }

    // Event listener برای form reset
    window.addEventListener('form-reset', function () {
        setTimeout(function () {
            initJalaliDatePicker();
        }, 100);
    });
</script>
</div>
