<div class="resident-management">
    @foreach ($this->allReportService()->getUnitWithDependence() as $data)
        @php
            $colorClass = $this->getColorClass($data['unit']['id']);
        @endphp
        <div class="vahed-card mb-4 p-1">
            <div class="card-header vahed-header" id="header_vahed_{{ $data['unit']['id'] }}" 
                 style="background: linear-gradient(135deg, {{ $data['unit']['color'] ?? '#667eea' }} 0%, {{ $data['unit']['color'] ?? '#764ba2' }} 100%) !important;
                        backdrop-filter: blur(20px) saturate(180%);
                        -webkit-backdrop-filter: blur(20px) saturate(180%);
                        background: linear-gradient(135deg, {{ $data['unit']['color'] ?? '#667eea' }}cc 0%, {{ $data['unit']['color'] ?? '#764ba2' }}cc 100%) !important;
                        border: 1px solid rgba(255, 255, 255, 0.3);
                        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
                        position: relative;
                        overflow: hidden;">
                <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, {{ $data['unit']['color'] ?? '#667eea' }} 0%, {{ $data['unit']['color'] ?? '#764ba2' }} 100%); opacity: 0.85; z-index: -1;"></div>
                <h4 class="mb-0 text-white" style="position: relative; z-index: 1;">{{ $data['unit']['name'] }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="row g-2 g-md-3">
                    @foreach ($data['rooms'] as $roomData)
                        <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                            <div class="otagh-card h-100" id="{{ $roomData['room']['name'] }}">
                                <div class="card-header otagh-header bg--light"
                                     id="otagh-vahed{{ $data['unit']['id'] }}"
                                     style="background: linear-gradient(135deg, {{ $roomData['room']['color'] ?? '#f093fb' }}cc 0%, {{ $roomData['room']['color'] ?? '#f5576c' }}cc 100%) !important; 
                                            backdrop-filter: blur(20px) saturate(180%);
                                            -webkit-backdrop-filter: blur(20px) saturate(180%);
                                            border: 1px solid rgba(255, 255, 255, 0.3);
                                            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
                                            color: white;
                                            position: relative;
                                            overflow: hidden;">
                                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: linear-gradient(135deg, {{ $roomData['room']['color'] ?? '#f093fb' }} 0%, {{ $roomData['room']['color'] ?? '#f5576c' }} 100%); opacity: 0.85; z-index: -1;"></div>
                                    <h5 class="mb-0" style="position: relative; z-index: 1;">{{ $roomData['room']['name'] }}</h5>
                                </div>
                                <div class="card-body p-0" id="tableforvahed{{ $data['unit']['id'] }}">
                                    <!-- این کانتینر اسکرول افقی را به صورت شرطی مدیریت می‌کند -->
                                    <div class="conditional-scroll-container">
                                        <table class="table table-sm table-hover modern-table conditional-scroll-table" id="{{ $roomData['room']['name'] }}">
                                            <thead>
                                            <tr style="background: linear-gradient(135deg, {{ $roomData['room']['color'] ?? '#f093fb' }}cc 0%, {{ $roomData['room']['color'] ?? '#f5576c' }}cc 100%) !important;
                                                    backdrop-filter: blur(20px) saturate(180%);
                                                    -webkit-backdrop-filter: blur(20px) saturate(180%);
                                                    border: 1px solid rgba(255, 255, 255, 0.3);
                                                    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
                                                    position: relative;
                                                    overflow: hidden;">
                                                <th style="color: white !important; position: relative; z-index: 1; background: transparent !important;">تخت</th>
                                                <th style="color: white !important; position: relative; z-index: 1; background: transparent !important;">نام</th>
                                                <th style="color: white !important; position: relative; z-index: 1; background: transparent !important;">تلفن</th>
                                                <th style="color: white !important; position: relative; z-index: 1; background: transparent !important;">سررسید</th>
                                                <th style="color: white !important; position: relative; z-index: 1; background: transparent !important;">مانده تا سررسید</th>
                                                <th style="color: white !important; position: relative; z-index: 1; background: transparent !important;">وضعیت</th>
                                                <th style="color: white !important; position: relative; z-index: 1; background: transparent !important;">عملیات</th>
                                            </tr>
                                            <style>
                                                #{{ $roomData['room']['name'] }} thead tr {
                                                    background: linear-gradient(135deg, {{ $roomData['room']['color'] ?? '#f093fb' }}cc 0%, {{ $roomData['room']['color'] ?? '#f5576c' }}cc 100%) !important;
                                                }
                                                #{{ $roomData['room']['name'] }} thead tr::before {
                                                    content: '';
                                                    position: absolute;
                                                    top: 0;
                                                    left: 0;
                                                    right: 0;
                                                    bottom: 0;
                                                    background: linear-gradient(135deg, {{ $roomData['room']['color'] ?? '#f093fb' }} 0%, {{ $roomData['room']['color'] ?? '#f5576c' }} 100%);
                                                    opacity: 0.85;
                                                    z-index: 0;
                                                    backdrop-filter: blur(20px) saturate(180%);
                                                    -webkit-backdrop-filter: blur(20px) saturate(180%);
                                                }
                                                #{{ $roomData['room']['name'] }} thead th {
                                                    background: transparent !important;
                                                    border: none !important;
                                                }
                                            </style>
                                            </thead>
                                            <tbody>
                                            @foreach ($roomData['beds'] as $bed)
                                                @if (!$bed['contracts']->first() || $bed['contracts'] == null)
                                                    <tr class="empty-bed" style="background-color: #ADD8E6;">
                                                        <!-- آبی برای تخت خالی -->
                                                        <td class="bed-number">
                                                            {{ substr($bed['bed']['name'], -1) }}
                                                        </td>
                                                        <td colspan="4" class="text-center">
                                                            <span class="empty-bed-text">تخت خالی</span>
                                                        </td>
                                                        <td></td>
                                                        <td class="text-center">
                                                            <button
                                                                wire:click="openAddModal('{{ $bed['bed']['name'] }}', '{{ $roomData['room']['name'] }}')"
                                                                class="btn btn-sm btn-outline-success add-resident-btn"
                                                                title="افزودن ساکن">
                                                                <i class="fas fa-user-plus"></i>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @else
                                                    @php
                                                        $contractData = $bed['contracts']->first();
                                                        $contract = $contractData['contract'];
                                                        $resident = $contractData['resident'];
                                                        // تعیین کلاس رنگ بر اساس وضعیت
                                                        $statusClass = match ($contract['state'] ?? '') {
                                                            "nightly" => 'similar-bed', // سیاه
                                                            "rezerve" => 'reserved-bed', // سبز
                                                            "leaving" => 'exiting-bed', // قرمز
                                                            "active" => 'occupied-bed', // پیش‌فرض
                                                        };
                                                    @endphp
                                                    <tr class="{{ $statusClass }}"
                                                        data-resident-id="{{ $resident['id'] }}">

                                                        <td class="bed-number">
                                                            {{ $bed['bed']['name'] }}
                                                        </td>

                                                        <td class="resident-name">
                                                            <input type="text"
                                                                   wire:model="full_name.{{ $resident['id'] }}"
                                                                   class="form-control form-control-sm"
                                                                   value="{{ $resident['full_name'] ?? '' }}">
                                                        </td>

                                                        <td class="resident-phone">
                                                            <div>
                                                                <input type="text"
                                                                       wire:model="phone.{{ $resident['id'] }}"
                                                                       class="form-control form-control-sm phone-input "
                                                                       maxlength="13"
                                                                       value="{{ $phone[$resident['id']] ?? '' }}">

                                                            </div>
                                                        </td>

                                                        <td class="resident-date">
                                                            <input type="text"
                                                                   wire:model="payment_date.{{ $resident['id'] }}"
                                                                   class="form-control form-control-sm"
                                                                   value="{{ $contract['payment_date'] ?? '' }}">
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
                                                                        // اگر نوع end_date است، فقط ماه و روز را نمایش بده
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
                                                            <a wire:click="editResidentInline({{ $resident['id'] }})"
                                                               class="btn btn-sm me-1" style="background: #609966"
                                                               title="ذخیره تغییرات">
                                                                <i class="fas fa-circle-check"
                                                                   style="color: #ffffff;"></i>
                                                            </a>

                                                            <a wire:click="editResident({{ $resident['id'] }})"
                                                               class="btn btn-sm me-1" style="background: #FFBBCC"
                                                               title="ویرایش">
                                                                <i class="fas fa-eye"></i>
                                                            </a>

                                                            <a class="btn btn-sm me-1"
                                                               wire:click="detailsChange({{ $resident['id']}})"
                                                               style="background: #BC6FF1; color: white;">
                                                                <i class="fas fa-gear"></i>

                                                            </a>

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

        // اضافه کردن event listener برای فرمت کردن شماره تلفن در client-side (اختیاری)
        document.addEventListener('DOMContentLoaded', function () {
            document.addEventListener('input', function (e) {
                if (e.target.classList.contains('phone-input')) {
                    let value = e.target.value.replace(/\D/g, '');

                    if (value.length >= 11 && value[0] === '0') {
                        value = value.substring(0, 4) + '-' + value.substring(4, 7) + '-' + value.substring(7, 11);
                    }

                    e.target.value = value;
                }
            });
        });
    </script>
    @endscript
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
    </style>
</div>
