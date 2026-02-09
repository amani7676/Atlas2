<div class="resident-management" wire:init="loadResidentData">
    @php
        // Cache the units data to avoid multiple calls
        $unitsData = $this->allReportService()->getUnitWithDependence();
    @endphp
    
    @foreach ($unitsData as $data)
        @php
            $colorClass = $this->getColorClass($data['unit']['id']);
            $unitColor = $data['unit']['color'] ?? '#667eea';
        @endphp
        <div class="vahed-card mb-4 p-1">
            <div class="card-header vahed-header" id="header_vahed_{{ $data['unit']['id'] }}">
                <h4 class="mb-0 text-white">{{ $data['unit']['name'] }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="row g-2 g-md-3">
                    @foreach ($data['rooms'] as $roomData)
                        @php
                            $roomColor = $roomData['room']['color'] ?? '#f093fb';
                            $isHighlighted = ($this->highlightRoom == $roomData['room']['name']);
                        @endphp
                        <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                            <div class="otagh-card h-100 {{ $isHighlighted ? 'highlighted-room' : '' }}" id="{{ $roomData['room']['name'] }}">
                                <div class="card-header otagh-header bg--light" id="otagh-vahed{{ $data['unit']['id'] }}">
                                    <h5 class="mb-0">{{ $roomData['room']['name'] }}</h5>
                                </div>
                                <div class="card-body p-0" id="tableforvahed{{ $data['unit']['id'] }}">
                                    <div class="conditional-scroll-container">
                                        <table class="table table-sm table-hover modern-table conditional-scroll-table" id="{{ $roomData['room']['name'] }}">
                                            <thead>
                                                <tr>
                                                    <th>تخت</th>
                                                    <th>نام</th>
                                                    <th>تلفن</th>
                                                    <th>سررسید</th>
                                                    <th>مانده تا سررسید</th>
                                                    <th>وضعیت</th>
                                                    <th>عملیات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($roomData['beds'] as $bed)
                                                    @if (!$bed['contracts']->first() || $bed['contracts'] == null)
                                                        <tr class="empty-bed {{ $isHighlighted ? 'highlighted-bed' : '' }}">
                                                            <td class="bed-number">
                                                                {{ substr($bed['bed']['name'], -1) }}
                                                            </td>
                                                            <td colspan="4" class="text-center">
                                                                <span class="empty-bed-text">
                                                                    {{ $isHighlighted ? 'تخت مورد نظر برای افزودن ساکن' : 'تخت خالی' }}
                                                                </span>
                                                            </td>
                                                            <td></td>
                                                            <td class="text-center">
                                                                <button wire:click="openAddModal('{{ $bed['bed']['name'] }}', '{{ $roomData['room']['name'] }}')"
                                                                        class="btn btn-sm {{ $isHighlighted ? 'btn-warning' : 'btn-outline-success' }} add-resident-btn fast-action-btn"
                                                                        title="افزودن ساکن"
                                                                        onclick="this.classList.add('loading')">
                                                                    <i class="action-icon fas fa-user-plus"></i>
                                                                    <i class="action-spinner fas fa-spinner fa-spin" style="display: none;"></i>
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @else
                                                        @php
                                                            $contractData = $bed['contracts']->first();
                                                            $contract = $contractData['contract'];
                                                            $resident = $contractData['resident'];
                                                            $statusClass = match ($contract['state'] ?? '') {
                                                                "nightly" => 'similar-bed',
                                                                "rezerve" => 'reserved-bed', 
                                                                "leaving" => 'exiting-bed',
                                                                "active" => 'occupied-bed',
                                                                default => 'occupied-bed',
                                                            };
                                                            // Ensure resident data exists in arrays
                                                            $this->ensureResidentDataExists($resident['id']);
                                                        @endphp
                                                        <tr class="{{ $statusClass }}" data-resident-id="{{ $resident['id'] }}">
                                                            <td class="bed-number">
                                                                {{ $bed['bed']['name'] }}
                                                            </td>
                                                            <td class="resident-name">
                                                                <input type="text"
                                                                       wire:model="full_name.{{ $resident['id'] }}"
                                                                       class="form-control form-control-sm"
                                                                       value="{{ $this->getSafeArrayValue($full_name, $resident['id']) }}">
                                                            </td>
                                                            <td class="resident-phone">
                                                                <div>
                                                                    <input type="text"
                                                                           wire:model="phone.{{ $resident['id'] }}"
                                                                           class="form-control form-control-sm phone-input @error('phone.'.$resident['id']) is-invalid @enderror"
                                                                           maxlength="13"
                                                                           value="{{ $this->getSafeArrayValue($phone, $resident['id']) }}"
                                                                           placeholder="09xxxxxxxxx">
                                                                    @error('phone.'.$resident['id'])
                                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                                    @enderror
                                                                    @if($this->getSafeArrayValue($phone, $resident['id']) && !preg_match('/^09[0-9]{9}$/', preg_replace('/[^0-9]/', '', $this->getSafeArrayValue($phone, $resident['id']))))
                                                                        <div class="text-danger small mt-1">
                                                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                                                            نامعتبر
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                            <td class="resident-date">
                                                                <input type="text"
                                                                       wire:model="payment_date.{{ $resident['id'] }}"
                                                                       class="form-control form-control-sm"
                                                                       value="{{ $this->getSafeArrayValue($payment_date, $resident['id']) }}">
                                                            </td>
                                                            <td class="resident-since">
                                                                {!! $statusService->getStatusBadge($contract['day_since_payment']) !!}
                                                            </td>
                                                            <td class="resident-note">
                                                                <div class="notes-container">
                                                                    @foreach ($contractData['notes'] as $note)
                                                                        @php
                                                                            $noteRepository = app(\App\Repositories\NoteRepository::class);
                                                                            $noteText = $note['note'];
                                                                            if ($note['type'] === 'end_date' && preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2})/', $noteText, $matches)) {
                                                                                $noteText = $matches[2] . '/' . $matches[3];
                                                                            } else {
                                                                                $noteText = $noteRepository->formatNoteForDisplay($note);
                                                                            }
                                                                            $badgeStyle = $noteRepository->getNoteBadgeStyle($note['type']);
                                                                        @endphp
                                                                        <span class="badge rounded-pill note-badge"
                                                                              style="{{ $badgeStyle }} position: relative; padding: 6px 22px 6px 10px; margin: 2px 0; display: block; font-size: 0.85rem; width: fit-content;">
                                                                            {{ $noteText }}
                                                                            <i class="fas fa-times-circle"
                                                                               style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 0.7rem; color: #dc3545; opacity: 0.8;"
                                                                               wire:click="deleteNote({{ $note['id'] }})"
                                                                               title="حذف یادداشت"
                                                                               onmouseover="this.style.opacity='1'; this.style.transform='translateY(-50%) scale(1.2)';"
                                                                               onmouseout="this.style.opacity='0.8'; this.style.transform='translateY(-50%) scale(1)';"></i>
                                                                        </span>
                                                                    @endforeach
                                                                </div>
                                                            </td>
                                                            <td class="action-buttons position-relative">
                                                                <!-- سایر دکمه‌ها -->
                                                                <button wire:click="editResidentInline({{ $resident['id'] }})"
                                                                        wire:loading.attr="disabled"
                                                                        wire:target="editResidentInline({{ $resident['id'] }})"
                                                                        class="btn btn-sm me-1" style="background: #609966"
                                                                        title="ذخیره تغییرات">
                                                                    <i wire:loading wire:target="editResidentInline({{ $resident['id'] }})" 
                                                                       class="fas fa-spinner fa-spin"></i>
                                                                    <i wire:loading.remove wire:target="editResidentInline({{ $resident['id'] }})" 
                                                                       class="fas fa-circle-check" style="color: #ffffff;"></i>
                                                                </button>

                                                                <button wire:click="editResident({{ $resident['id'] }})"
                                                                        class="btn btn-sm me-1 fast-action-btn" 
                                                                        style="background: #FFBBCC"
                                                                        title="ویرایش"
                                                                        onclick="this.classList.add('loading')"
                                                                        wire:target="editResidentModal">
                                                                    <i class="action-icon fas fa-eye"></i>
                                                                    <i class="action-spinner fas fa-spinner fa-spin" style="display: none;"></i>
                                                                </button>

                                                                <button wire:click="detailsChange({{ $resident['id'] }})"
                                                                        class="btn btn-sm me-1 fast-action-btn"
                                                                        style="background: #BC6FF1; color: white;"
                                                                        title="تغییرات جزییات"
                                                                        onclick="this.classList.add('loading')"
                                                                        wire:target="detailsChangeModal">
                                                                    <i class="action-icon fas fa-gear"></i>
                                                                    <i class="action-spinner fas fa-spinner fa-spin" style="display: none;"></i>
                                                                </button>

                                                            </td>
                                                        </tr>
                                                    @endif
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    {{-- کامپوننت مودال جداگانه --}}
    <livewire:modals.resident-modal/>
    <livewire:modals.details-changes-modal/>


    @script
    <script>
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

        // Debouncing function for phone input optimization
        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // اضافه کردن event listener برای فرمت کردن شماره تلفن در client-side با debouncing
        document.addEventListener('DOMContentLoaded', function () {
            const debouncedPhoneHandler = debounce(function (e) {
                if (e.target.classList.contains('phone-input')) {
                    let value = e.target.value.replace(/\D/g, '');

                    if (value.length >= 11 && value[0] === '0') {
                        value = value.substring(0, 4) + '-' + value.substring(4, 7) + '-' + value.substring(7, 11);
                    }

                    e.target.value = value;
                }
            }, 200); // Reduced debounce time for better responsiveness

            document.addEventListener('input', debouncedPhoneHandler);

            // Fast Action Buttons - Instant Feedback
            document.addEventListener('DOMContentLoaded', function() {
                // Handle all fast action buttons
                const fastButtons = document.querySelectorAll('.fast-action-btn');
                
                fastButtons.forEach(button => {
                    button.addEventListener('click', function(e) {
                        // Add loading state immediately
                        this.classList.add('loading');
                        
                        // Remove loading after 2 seconds (fallback)
                        setTimeout(() => {
                            this.classList.remove('loading');
                        }, 2000);
                    });
                });
                
                // Listen for Livewire updates to remove loading states
                if (window.Livewire) {
                    window.Livewire.on('modalOpened', () => {
                        // Remove loading states when modal opens
                        document.querySelectorAll('.fast-action-btn.loading').forEach(btn => {
                            btn.classList.remove('loading');
                        });
                    });
                    
                    window.Livewire.on('show-toast', () => {
                        // Remove loading states when toast shows (operation completed)
                        setTimeout(() => {
                            document.querySelectorAll('.fast-action-btn.loading').forEach(btn => {
                                btn.classList.remove('loading');
                            });
                        }, 500);
                    });
                }
            });

            // Auto-scroll to highlighted room using hash
            setTimeout(() => {
                // Get hash from URL
                const urlHash = window.location.hash;
                if (urlHash) {
                    const roomName = urlHash.substring(1); // Remove # symbol
                    
                    // Find the room container by matching room name
                    const roomContainers = document.querySelectorAll('.otagh-card');
                    let targetRoom = null;
                    
                    roomContainers.forEach(container => {
                        const roomHeader = container.querySelector('.otagh-header h5');
                        if (roomHeader && roomHeader.textContent.trim() === roomName) {
                            targetRoom = container;
                        }
                    });
                    
                    if (targetRoom) {
                        // Scroll to the room container
                        targetRoom.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        
                        // Add highlight effect to the room
                        targetRoom.style.border = '3px solid #FFD700';
                        targetRoom.style.boxShadow = '0 0 20px rgba(255, 215, 0, 0.5)';
                        targetRoom.style.transition = 'all 0.3s ease';
                        
                        // Remove room highlight after 3 seconds
                        setTimeout(() => {
                            targetRoom.style.border = '';
                            targetRoom.style.boxShadow = '';
                        }, 3000);
                        
                        // Highlight all empty beds in this room
                        const emptyBeds = targetRoom.querySelectorAll('.highlighted-bed');
                        emptyBeds.forEach(bed => {
                            bed.style.animation = 'pulse 1s ease-in-out 3';
                        });
                    }
                }
            }, 300); // Reduced delay for faster response
        });
    </script>
    @endscript
    
    <!-- Load performance optimizer script -->
    <script src="{{ asset('assets/js/tablelist-optimizer.js') }}" defer></script>
    <style>
        /* --- استایل‌های واکنش‌گرا برای اسکرول افقی در همه اندازه‌ها --- */

        /* کانتینر اصلی - همیشه اسکرول افقی فعال */
        .conditional-scroll-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        /* جدول - عرض حداقلی برای فعال‌سازی اسکرول */
        .conditional-scroll-table {
            min-width: 900px;
            width: 100%;
            margin-bottom: 0;
        }

        /* تنظیم فونت و فاصله‌ها */
        .conditional-scroll-table {
            font-family: 'Vazirmatn', 'Tahoma', sans-serif !important;
        }

        .conditional-scroll-table th,
        .conditional-scroll-table td {
            vertical-align: middle;
            padding: 0.4rem 0.5rem !important;
        }

        /* فاصله بالا و پایین بین ردیف‌ها */
        .conditional-scroll-table tbody tr {
            margin-top: 0.2rem;
            margin-bottom: 0.2rem;
        }

        /* اجازه نمایش کامل متن در td ها */
        .conditional-scroll-table tbody td {
            white-space: normal;
            word-wrap: break-word;
        }

        /* input ها باید متن کامل را نشان دهند - ارتفاع کمی بیشتر */
        .conditional-scroll-table tbody td input.form-control-sm {
            width: 100%;
            min-width: 100px;
            font-family: 'Vazirmatn', 'Tahoma', sans-serif;
            font-size: 0.9rem;
            padding: 0.35rem 0.4rem;
            height: auto;
            min-height: 32px;
        }

        .conditional-scroll-table tbody td.resident-name input {
            min-width: 120px;
        }

        .conditional-scroll-table tbody td.resident-phone input {
            min-width: 110px;
        }

        .conditional-scroll-table tbody td.resident-date input {
            min-width: 100px;
        }

        /* کوچک کردن فقط td شماره تخت - فقط عدد را نشان می‌دهد */
        .conditional-scroll-table tbody td.bed-number {
            padding: 0.25rem 0.3rem !important;
            width: auto;
            min-width: 30px;
            max-width: 35px;
            text-align: center;
        }

        /* دکمه‌های عملیات نباید بشکنند */
        .action-buttons {
            white-space: nowrap;
        }

        /* موبایل (تا 576px) */
        @media (max-width: 575.98px) {
            .conditional-scroll-table {
                min-width: 800px;
                font-size: 0.75rem;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.25rem 0.3rem !important;
            }
            
            .conditional-scroll-table tbody td.bed-number {
                padding: 0.15rem 0.2rem !important;
                font-size: 0.85rem;
                min-width: 25px;
                max-width: 30px;
                text-align: center;
            }

            .conditional-scroll-table tbody tr {
                margin-top: 0.15rem;
                margin-bottom: 0.15rem;
            }

            .conditional-scroll-table tbody td input.form-control-sm {
                font-size: 0.8rem;
                padding: 0.3rem 0.3rem;
                min-width: 80px;
                min-height: 28px;
            }

            .conditional-scroll-table tbody td.resident-name input {
                min-width: 100px;
            }

            .conditional-scroll-table tbody td.resident-phone input {
                min-width: 90px;
            }

            .conditional-scroll-table tbody td.resident-date input {
                min-width: 85px;
            }

            .action-buttons .btn {
                padding: 0.2rem 0.3rem;
                font-size: 0.7rem;
                min-width: 28px;
                height: 28px;
            }

            .resident-management .row {
                margin: 0;
            }

            .vahed-card {
                margin-bottom: 1rem !important;
            }

            .col-lg-6 {
                padding: 0.25rem;
            }
        }

        /* تبلت کوچک (576px تا 768px) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .conditional-scroll-table {
                min-width: 850px;
                font-size: 0.8rem;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.3rem 0.4rem !important;
            }
            
            .conditional-scroll-table tbody td.bed-number {
                padding: 0.2rem 0.3rem !important;
                font-size: 0.9rem;
                min-width: 28px;
                max-width: 32px;
                text-align: center;
            }

            .conditional-scroll-table tbody tr {
                margin-top: 0.18rem;
                margin-bottom: 0.18rem;
            }

            .conditional-scroll-table tbody td input.form-control-sm {
                font-size: 0.85rem;
                padding: 0.32rem 0.35rem;
                min-width: 90px;
                min-height: 30px;
            }

            .conditional-scroll-table tbody td.resident-name input {
                min-width: 110px;
            }

            .conditional-scroll-table tbody td.resident-phone input {
                min-width: 100px;
            }

            .conditional-scroll-table tbody td.resident-date input {
                min-width: 95px;
            }

            .action-buttons .btn {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }
        }

        /* تبلت (768px تا 992px) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .conditional-scroll-table {
                min-width: 900px;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.4rem 0.5rem !important;
            }
            
            .conditional-scroll-table tbody td.bed-number {
                padding: 0.25rem 0.35rem !important;
                min-width: 30px;
                max-width: 35px;
                text-align: center;
            }

            .conditional-scroll-table tbody tr {
                margin-top: 0.2rem;
                margin-bottom: 0.2rem;
            }

            .conditional-scroll-table tbody td input.form-control-sm {
                min-width: 100px;
                padding: 0.35rem 0.4rem;
                min-height: 32px;
            }

            .conditional-scroll-table tbody td.resident-name input {
                min-width: 120px;
            }

            .conditional-scroll-table tbody td.resident-phone input {
                min-width: 110px;
            }

            .conditional-scroll-table tbody td.resident-date input {
                min-width: 100px;
            }
        }

        /* لپتاپ کوچک (992px تا 1200px) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .conditional-scroll-table {
                min-width: 950px;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.5rem 0.6rem !important;
            }
            
            .conditional-scroll-table tbody td.bed-number {
                padding: 0.3rem 0.4rem !important;
                min-width: 32px;
                max-width: 36px;
                text-align: center;
            }

            .conditional-scroll-table tbody tr {
                margin-top: 0.2rem;
                margin-bottom: 0.2rem;
            }

            .conditional-scroll-table tbody td input.form-control-sm {
                min-width: 110px;
                padding: 0.38rem 0.4rem;
                min-height: 34px;
            }

            .conditional-scroll-table tbody td.resident-name input {
                min-width: 130px;
            }

            .conditional-scroll-table tbody td.resident-phone input {
                min-width: 120px;
            }

            .conditional-scroll-table tbody td.resident-date input {
                min-width: 110px;
            }
        }

        /* لپتاپ و دسکتاپ (1200px به بالا) */
        @media (min-width: 1200px) {
            .conditional-scroll-table {
                min-width: 1000px;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.5rem 0.6rem !important;
            }
            
            .conditional-scroll-table tbody td.bed-number {
                padding: 0.35rem 0.45rem !important;
                min-width: 35px;
                max-width: 40px;
                text-align: center;
            }

            .conditional-scroll-table tbody tr {
                margin-top: 0.25rem;
                margin-bottom: 0.25rem;
            }

            .conditional-scroll-table tbody td input.form-control-sm {
                min-width: 120px;
                padding: 0.4rem 0.4rem;
                min-height: 36px;
            }

            .conditional-scroll-table tbody td.resident-name input {
                min-width: 140px;
            }

            .conditional-scroll-table tbody td.resident-phone input {
                min-width: 130px;
            }

            .conditional-scroll-table tbody td.resident-date input {
                min-width: 120px;
            }
        }

        /* استایل برای نمایش عمودی notes */
        .notes-container {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .note-badge {
            display: block !important;
            width: fit-content;
            max-width: 100%;
        }

        /* استایل‌های اصلی شما که بدون تغییر باقی می‌مانند */
        .similar-bed {
            background-color: #7F8CAA; /* سیاه */
            color: #ffffff; /* متن سفید برای خوانایی */
        }

        .reserved-bed {
            background-color: #DEE791; /* سبز */
            color: #ffffff;
        }

        .exiting-bed {
            background-color: #FFAAAA; /* قرمز */
            color: #ffffff;
        }

        .empty-bed {
            background-color: #ADD8E6; /* آبی */
        }

        .highlighted-bed {
            background-color: #FFD700 !important; /* طلایی برای تخت مشخص شده */
            box-shadow: 0 0 15px rgba(255, 215, 0, 0.7);
            border: 2px solid #FFA500;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }

        /* Fast Action Buttons */
        .fast-action-btn {
            position: relative;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .fast-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .fast-action-btn:active {
            transform: translateY(0);
        }

        .fast-action-btn.loading {
            pointer-events: none;
            opacity: 0.7;
        }

        .fast-action-btn.loading .action-icon {
            display: none;
        }

        .fast-action-btn.loading .action-spinner {
            display: inline-block !important;
        }

        .action-icon {
            transition: opacity 0.2s ease;
        }

        .action-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Special styling for Add Resident button */
        .add-resident-btn {
            transition: all 0.2s ease;
            position: relative;
        }

        .add-resident-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .add-resident-btn.btn-warning:hover {
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
        }

        .add-resident-btn.loading {
            transform: scale(0.95);
            opacity: 0.8;
        }

        .add-resident-btn.loading .action-icon {
            display: none;
        }

        .add-resident-btn.loading .action-spinner {
            display: inline-block !important;
        }
        
        /* استایل‌های برای هدرهای واحد و اتاق */
        .vahed-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            position: relative;
            overflow: hidden;
        }
        
        .otagh-header {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
            backdrop-filter: blur(20px) saturate(180%);
            -webkit-backdrop-filter: blur(20px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .otagh-header h5 {
            position: relative;
            z-index: 1;
        }
        
        .highlighted-room {
            border: 3px solid #FFD700;
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
            transition: all 0.3s ease;
        }
        
        .highlighted-bed {
            background-color: #FFD700 !important;
            animation: pulse 1s ease-in-out 3;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</div>
