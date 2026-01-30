<div>
    <div class="container-fluid px-4 mt-4">
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-filter me-2"></i>
                    فیلترها
                </h5>
                @if($startDate || $endDate || $unitId || $roomId || $name || $phone || $nationalCode)
                    <span class="badge bg-light text-dark">
                        <i class="fas fa-check-circle me-1"></i>
                        فیلتر فعال
                    </span>
                @endif
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- فیلتر تاریخ شروع -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">تاریخ شروع:</label>
                        <input type="text" 
                               id="start_date_picker" 
                               wire:model="startDate"
                               class="form-control" 
                               placeholder="روی این فیلد کلیک کنید"
                               style="cursor: pointer; background-color: #fff;">
                    </div>

                    <!-- فیلتر تاریخ پایان -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">تاریخ پایان:</label>
                        <input type="text" 
                               id="end_date_picker" 
                               wire:model="endDate"
                               class="form-control" 
                               placeholder="روی این فیلد کلیک کنید"
                               style="cursor: pointer; background-color: #fff;">
                    </div>

                    <!-- فیلتر واحد -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">
                            <i class="fas fa-building me-1"></i>
                            واحد:
                        </label>
                        <select wire:model.live="unitId" class="form-select">
                            <option value="">همه واحدها</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit['id'] }}">{{ $unit['name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- فیلتر اتاق -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">
                            <i class="fas fa-door-open me-1"></i>
                            اتاق:
                        </label>
                        <select wire:model.live="roomId" class="form-select" {{ $unitId ? '' : 'disabled' }}>
                            <option value="">همه اتاق‌ها</option>
                            @if($unitId)
                                @foreach($rooms as $room)
                                    <option value="{{ $room['id'] }}">{{ $room['name'] }}</option>
                                @endforeach
                            @else
                                <option value="">ابتدا واحد را انتخاب کنید</option>
                            @endif
                        </select>
                    </div>

                    <!-- فیلتر نام -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">
                            <i class="fas fa-user me-1"></i>
                            نام:
                        </label>
                        <input type="text" 
                               wire:model.live.debounce.300ms="name"
                               class="form-control" 
                               placeholder="جستجو بر اساس نام...">
                    </div>

                    <!-- فیلتر تلفن -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">
                            <i class="fas fa-phone me-1"></i>
                            شماره تلفن:
                        </label>
                        <input type="text" 
                               wire:model.live.debounce.300ms="phone"
                               class="form-control" 
                               placeholder="جستجو بر اساس تلفن...">
                    </div>

                    <!-- فیلتر کد ملی -->
                    <div class="col-md-6 col-lg-3">
                        <label class="form-label">
                            <i class="fas fa-id-card me-1"></i>
                            کد ملی:
                        </label>
                        <input type="text" 
                               wire:model.live.debounce.300ms="nationalCode"
                               class="form-control" 
                               placeholder="جستجو بر اساس کد ملی...">
                    </div>

                    <!-- دکمه‌های عملیات -->
                    <div class="col-md-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <button wire:click="resetFilters" class="btn btn-secondary">
                                <i class="fas fa-redo me-2"></i>
                                پاک کردن همه فیلترها
                            </button>
                        </div>
                        <div class="text-muted small">
                            <i class="fas fa-info-circle me-1"></i>
                            تعداد نتایج: <strong>{{ count($residents) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- جدول اقامتگران خروجی -->
        <div class="card">
            <div class="card-header bg-light text-dark border">
                <h5 class="mb-0">
                    <i class="fas fa-sign-out-alt me-2"></i>
                    اقامتگران خروجی
                    <span class="badge bg-primary ms-2">{{ count($residents) }}</span>
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>نام</th>
                                <th>کد ملی</th>
                                <th>تلفن</th>
                                <th>واحد</th>
                                <th>اتاق</th>
                                <th>تخت</th>
                                <th>
                                    <a href="#" wire:click.prevent="sortBy('start_date')" class="text-decoration-none text-dark">
                                        تاریخ شروع
                                        @if($sortBy === 'start_date')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="#" wire:click.prevent="sortBy('end_date')" class="text-decoration-none text-dark">
                                        تاریخ پایان
                                        @if($sortBy === 'end_date')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>
                                    <a href="#" wire:click.prevent="sortBy('deleted_at')" class="text-decoration-none text-dark">
                                        تاریخ حذف
                                        @if($sortBy === 'deleted_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                                        @else
                                            <i class="fas fa-sort ms-1"></i>
                                        @endif
                                    </a>
                                </th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($residents as $index => $data)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $data['resident']['full_name'] ?? '-' }}</td>
                                    <td>{{ $data['resident']['document'] ?? '-' }}</td>
                                    <td>
                                        @php
                                            $phone = $data['resident']['phone'] ?? '';
                                            $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
                                            $isValid = preg_match('/^09[0-9]{9}$/', $cleanPhone);
                                        @endphp
                                        @if($isValid && $phone !== '-')
                                            <span class="text-success">
                                                <i class="fas fa-phone me-1"></i>
                                                {{ $phone }}
                                            </span>
                                        @elseif($phone !== '-')
                                            <span class="text-danger text-decoration-line-through">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                {{ $phone }}
                                            </span>
                                        @else
                                            {{ $phone }}
                                        @endif
                                    </td>
                                    <td>{{ $data['unit']['name'] ?? '-' }}</td>
                                    <td>{{ $data['room']['name'] ?? '-' }}</td>
                                    <td>
                                        <span class="bed-info-container">
                                            <span class="bed-number-badge">
                                                <i class="fas fa-bed"></i>
                                                {{ $data['bed']['name'] ?? '-' }}
                                            </span>
                                        </span>
                                    </td>
                                    <td>{{ $data['contract']['start_date'] ?? '-' }}</td>
                                    <td>{{ $data['contract']['end_date'] ?? '-' }}</td>
                                    <td>
                                        @if(isset($data['contract']['deleted_at']) && $data['contract']['deleted_at'])
                                            <span class="badge bg-danger">{{ $data['contract']['deleted_at'] }}</span>
                                        @elseif(isset($data['resident']['deleted_at']) && $data['resident']['deleted_at'])
                                            <span class="badge bg-danger">{{ $data['resident']['deleted_at'] }}</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <button 
                                            wire:click="deleteResident({{ $data['resident']['id'] }})"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('آیا از حذف کامل این اقامتگر اطمینان دارید؟ این عمل غیرقابل بازگشت است.')"
                                            title="حذف کامل اقامتگر">
                                            <i class="fas fa-trash-alt"></i>
                                            حذف
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="11" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2"></i>
                                        <p class="text-muted">هیچ اقامتگر خروجی یافت نشد</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @script
    <script>
        (function() {
            function initDatePickers() {
                // Wait for jalaliDatepicker to be loaded
                if (typeof jalaliDatepicker === 'undefined') {
                    console.warn('Jalali Datepicker not loaded yet, retrying...');
                    setTimeout(initDatePickers, 200);
                    return;
                }

                // Initialize start date picker
                const startInput = document.getElementById('start_date_picker');
                if (startInput) {
                    // Remove existing instance if any
                    if (startInput._jalaliDatepicker) {
                        jalaliDatepicker.stopWatch('#start_date_picker');
                    }

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
                        selector: '#start_date_picker'
                    });

                    startInput.addEventListener('change', function() {
                        const selectedDate = this.value;
                        if (selectedDate) {
                            $wire.set('startDate', selectedDate);
                        }
                    });
                }

                // Initialize end date picker
                const endInput = document.getElementById('end_date_picker');
                if (endInput) {
                    // Remove existing instance if any
                    if (endInput._jalaliDatepicker) {
                        jalaliDatepicker.stopWatch('#end_date_picker');
                    }

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
                        selector: '#end_date_picker'
                    });

                    endInput.addEventListener('change', function() {
                        const selectedDate = this.value;
                        if (selectedDate) {
                            $wire.set('endDate', selectedDate);
                        }
                    });
                }
            }

            // Initialize when DOM is ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(initDatePickers, 500);
                });
            } else {
                setTimeout(initDatePickers, 500);
            }

            // Re-initialize after Livewire updates
            document.addEventListener('livewire:init', function() {
                setTimeout(initDatePickers, 500);
            });

            document.addEventListener('livewire:update', function() {
                setTimeout(initDatePickers, 500);
            });

            // Also try after window load
            window.addEventListener('load', function() {
                setTimeout(initDatePickers, 500);
            });
        })();
    </script>
    @endscript
</div>

