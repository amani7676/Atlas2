<div class="resident-management">
    @foreach ($this->allReportService()->getUnitWithDependence() as $data)
        @php
            $colorClass = $this->getColorClass($data['unit']['id']);
        @endphp
        <div class="vahed-card mb-4 p-1">
            <div class="card-header vahed-header " id="header_vahed_{{ $data['unit']['id'] }}">
                <h4 class="mb-0 text-white">{{ $data['unit']['name'] }}</h4>
            </div>
            <div class="card-body p-0">
                <div class="row g-2 g-md-3">
                    @foreach ($data['rooms'] as $roomData)
                        <div class="col-12 col-md-6 col-lg-6 col-xl-6">
                            <div class="otagh-card h-100" id="{{ $roomData['room']['name'] }}">
                                <div class="card-header otagh-header bg--light"
                                     id="otagh-vahed{{ $data['unit']['id'] }}">
                                    <h5 class="mb-0">{{ $roomData['room']['name'] }}</h5>
                                </div>
                                <div class="card-body p-0" id="tableforvahed{{ $data['unit']['id'] }}">
                                    <!-- این کانتینر اسکرول افقی را به صورت شرطی مدیریت می‌کند -->
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

        /* جلوگیری از شکستن متن */
        .conditional-scroll-table th,
        .conditional-scroll-table td {
            white-space: nowrap;
            vertical-align: middle;
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
                padding: 0.3rem 0.4rem;
            }

            .form-control-sm {
                font-size: 0.75rem;
                padding: 0.2rem 0.3rem;
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
                padding: 0.4rem 0.5rem;
            }

            .form-control-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.4rem;
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
                padding: 0.5rem 0.6rem;
            }
        }

        /* لپتاپ کوچک (992px تا 1200px) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .conditional-scroll-table {
                min-width: 950px;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.6rem 0.7rem;
            }
        }

        /* لپتاپ و دسکتاپ (1200px به بالا) */
        @media (min-width: 1200px) {
            .conditional-scroll-table {
                min-width: 1000px;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.7rem 0.8rem;
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
