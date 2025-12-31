<div>
    <div class="card mb-4">
        <div class="card-header card-header-rezerve d-flex justify-content-between align-items-center">
            <span class='span-rezerve'>رزروها</span>
        </div>
        <div class="card-body p-0">
            <!-- کانتینر با کلاس سفارشی برای کنترل اسکرول -->
            <div class="conditional-scroll-container">
                <table class="table table-hover conditional-scroll-table">
                    <thead>
                    <tr class="tr-rezerve">
                        <th>#</th>
                        <th>اتاق</th>
                        <th>تخت / کل</th>
                        <th>نام</th>
                        <th>تلفن</th>
                        <th>سررسید</th>
                        <th>مانده</th>
                        <th>توضیحات</th>
                        <th>عملیات</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $counter = 0;
                    @endphp
                    @foreach ($this->allReportService->getAllResidentsWithDetails() as $data)
                        @if (isset($data['contract']) && $data['contract'] !== null && $data['contract']['state'] == 'rezerve')
                            @php $counter++; @endphp
                            <tr>
                                <td class="text-info">{{ $counter }}</td>
                                <td>{{ $data['room']['name'] ?? 'N/A' }}</td>
                                <td>
                                    <span class="bed-info-container">
                                        <span class="bed-number-badge">
                                            <i class="fas fa-bed"></i>
                                            {{ $data['bed']['name'] ?? 'N/A' }}
                                        </span>
                                        <span class="bed-total-badge">
                                            <i class="fas fa-door-open"></i>
                                            {{ $data['room']['bed_count'] ?? 'N/A' }}
                                        </span>
                                    </span>
                                </td>
                                <td>{{ $data['resident']['full_name'] ?? 'N/A' }}</td>
                                <td>{{ $data['resident']['phone'] ?? 'N/A' }}</td>
                                <td>{{ $data['contract']['payment_date'] ?? 'N/A' }}</td>
                                <td>
                                    {!! $statusService->getStatusBadge($data['contract']['day_since_payment'] ?? 0) !!}
                                </td>
                                <td>
                                    <div class="notes-container">
                                        @foreach ($data['notes'] as $note)
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
                                                   onclick="window.dispatchEvent(new CustomEvent('delete-note-event', { detail: { noteId: '{{ $note['id'] }}' } }))"
                                                   title="حذف یادداشت"
                                                   onmouseover="this.style.opacity='1'; this.style.transform='translateY(-50%) scale(1.2)';"
                                                   onmouseout="this.style.opacity='0.8'; this.style.transform='translateY(-50%) scale(1)';"></i>
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('table_list')}}#{{ $data['room']['name'] }}" target="_blank" class="action-btn">
                                        <i class="fas fa-external-link-alt"></i>
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

    <style>
        /* --- استایل‌های واکنش‌گرا برای اسکرول افقی در همه اندازه‌ها --- */

        /* کانتینر اصلی - همیشه اسکرول افقی فعال */
        .conditional-scroll-container {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 0 0 0.375rem 0.375rem;
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

        /* موبایل (تا 576px) */
        @media (max-width: 575.98px) {
            .conditional-scroll-table {
                min-width: 800px;
                font-size: 0.8rem;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.4rem 0.6rem;
            }

            .action-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 32px;
                height: 32px;
                border-radius: 0.25rem;
                background-color: #e9ecef;
                color: #0d6efd;
                text-decoration: none;
            }
        }

        /* تبلت کوچک (576px تا 768px) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .conditional-scroll-table {
                min-width: 850px;
                font-size: 0.85rem;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.5rem 0.75rem;
            }

            .action-btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                width: 34px;
                height: 34px;
                border-radius: 0.25rem;
                background-color: #e9ecef;
                color: #0d6efd;
                text-decoration: none;
            }
        }

        /* تبلت (768px تا 992px) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .conditional-scroll-table {
                min-width: 900px;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.6rem 0.8rem;
            }
        }

        /* لپتاپ کوچک (992px تا 1200px) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .conditional-scroll-table {
                min-width: 950px;
            }
        }

        /* لپتاپ و دسکتاپ (1200px به بالا) */
        @media (min-width: 1200px) {
            .conditional-scroll-table {
                min-width: 1000px;
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
    </style>
</div>
