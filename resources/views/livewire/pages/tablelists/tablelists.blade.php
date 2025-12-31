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
                <div class="row g-0">
                    @foreach ($data['rooms'] as $roomData)
                        <div class="col-lg-6 col-xl-6">
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
                                                                <span class="badge rounded-pill"
                                                                      style="{{ $badgeStyle }} position: relative; padding: 6px 22px 6px 10px; margin: 2px; display: inline-block; font-size: 0.85rem;">
                                                                    {{ $noteText }}
                                                                    <i class="fas fa-times-circle"
                                                                       style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 0.7rem; color: #dc3545; opacity: 0.8;"
                                                                       wire:click="deleteNote({{ $note['id'] }})"
                                                                       title="حذف یادداشت"
                                                                       onmouseover="this.style.opacity='1'; this.style.transform='translateY(-50%) scale(1.2)';"
                                                                       onmouseout="this.style.opacity='0.8'; this.style.transform='translateY(-50%) scale(1)';"></i>
                                                                </span>
                                                            @endforeach
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
        /* --- استایل‌های اصلی برای اسکرول شرطی در جداول تودرتو --- */

        /* ۱. رفتار پیش‌فرض برای دسکتاپ: بدون اسکرول افقی */
        .conditional-scroll-container {
            overflow-x: visible; /* در دسکتاپ اسکرولی نمایش داده نمی‌شود */
        }

        .conditional-scroll-table {
            width: 100%; /* جدول تمام عرض کانتینر را می‌گیرد */
            margin-bottom: 0;
        }

        /* اجازه دادن به شکستن متن در دسکتاپ برای جا شدن در ستون‌ها */
        @media (min-width: 992px) {
            .conditional-scroll-table th,
            .conditional-scroll-table td {
                white-space: normal;
            }
            .action-buttons {
                white-space: nowrap; /* دکمه‌ها نباید بشکنند */
            }
        }


        /* ۲. رفتار برای موبایل و تبلت: فعال‌سازی اسکرول افقی */
        /* این مدیا کوئری برای صفحات کوچکتر از 992px (تبلت و موبایل) اعمال می‌شود */
        @media (max-width: 991.98px) {
            .conditional-scroll-container {
                overflow-x: auto; /* اسکرول افقی فعال می‌شود */
                -webkit-overflow-scrolling: touch; /* اسکرول نرم در iOS */
            }

            .conditional-scroll-table {
                /* عرض حداقلی برای جدول تا اسکرول فعال شود */
                min-width: 800px; /* کمی کوچکتر چون فیلدها ورودی هستند */
            }

            /* جلوگیری از شکستن متن برای حفظ ساختار جدول */
            .conditional-scroll-table th,
            .conditional-scroll-table td {
                white-space: nowrap;
                vertical-align: middle;
            }
        }

        /* --- استایل‌های بهبوددهنده برای موبایل --- */
        @media (max-width: 768px) {
            .conditional-scroll-table {
                font-size: 0.8rem; /* فونت کوچکتر برای جا شدن */
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.4rem 0.5rem; /* فاصله داخلی کمتر */
            }

            /* کوچک کردن فیلدهای ورودی در موبایل */
            .form-control-sm {
                font-size: 0.8rem;
                padding: 0.25rem 0.4rem;
            }

            /* استایل دکمه‌های عملیات برای لمس راحت‌تر */
            .action-buttons .btn {
                padding: 0.25rem 0.4rem;
                font-size: 0.75rem;
            }
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
