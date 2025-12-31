<div class="card {{ $isExpanded ? 'h-100' : '' }}">
    <div class="card-header card-header-documents d-flex justify-content-between align-items-center" style="cursor: pointer;" wire:click="toggle">
        <span class="fw-bold">مدارک</span>
        <i class="fas {{ $isExpanded ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
    </div>
    @if($isExpanded)
    <div class="card-body p-0">
        <div class="conditional-scroll-container">
            <table class="table table-hover table-sm mb-0 conditional-scroll-table">
                <thead>
                    <tr>
                        <th class="d-none d-md-table-cell">#</th>
                        <th>اتاق</th>
                        <th class="d-none d-lg-table-cell">نام</th>
                        <th class="d-none d-xl-table-cell">تلفن</th>
                        <th class="d-none d-lg-table-cell">سررسید</th>
                        <th class="d-none d-md-table-cell">توضیح</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $counter = 0;
                    @endphp
                    @foreach ($this->allReportService->getAllResidentsWithDetails() as $data)
                        @if (isset($data['resident']) && $data['resident'] !== null && !($data['resident']['document'] ?? false))
                            @php
                                $counter++;
                            @endphp
                            <tr>
                                <td class="text-info d-none d-md-table-cell">{{ $counter }}</td>
                                <td>
                                    <span class="d-md-none fw-bold">{{ $counter }}. </span>
                                    {{ $data['room']['name'] ?? 'N/A' }}
                                </td>
                                <td class="d-none d-lg-table-cell">{{ $data['resident']['full_name'] ?? 'N/A' }}</td>
                                <td class="d-none d-xl-table-cell">{{ $data['resident']['phone'] ?? 'N/A' }}</td>
                                <td class="d-none d-lg-table-cell">{{ $data['contract']['payment_date'] ?? 'N/A' }}</td>
                                <td class="d-none d-md-table-cell" style="max-width: 200px;">
                                    <div class="notes-container">
                                        @foreach ($data['notes'] as $note)
                                            @if ($note['type'] === 'payment')
                                                @php
                                                    $noteRepository = app(\App\Repositories\NoteRepository::class);
                                                    $noteText = $noteRepository->formatNoteForDisplay($note);
                                                    $badgeStyle = $noteRepository->getNoteBadgeStyle($note['type']);
                                                @endphp
                                                <span class="badge rounded-pill note-badge"
                                                      style="{{ $badgeStyle }} position: relative; padding: 4px 20px 4px 8px; margin: 2px 0; display: block; font-size: 0.75rem; width: fit-content;">
                                                    {{ $noteText }}
                                                    <i class="fas fa-times-circle"
                                                       style="position: absolute; right: 3px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 0.65rem; color: #dc3545; opacity: 0.8;"
                                                       onclick="window.dispatchEvent(new CustomEvent('delete-note-event', { detail: { noteId: '{{ $note['id'] }}' } }))"
                                                       title="حذف یادداشت"
                                                       onmouseover="this.style.opacity='1'; this.style.transform='translateY(-50%) scale(1.2)';"
                                                       onmouseout="this.style.opacity='0.8'; this.style.transform='translateY(-50%) scale(1)';"></i>
                                                </span>
                                            @endif
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    @if(isset($data['resident']['id']))
                                        <a href="#" wire:click.prevent="giveDocumented({{ $data['resident']['id'] }})"
                                            class="text-success action-btn">
                                            <i class="fas fa-check-circle"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            {{-- ردیف اطلاعات موبایل --}}
                            <tr class="d-md-none">
                                <td colspan="2" class="small text-muted border-top-0 pt-0">
                                    <div><strong>نام:</strong> {{ $data['resident']['full_name'] ?? 'N/A' }}</div>
                                    <div><strong>تلفن:</strong> {{ $data['resident']['phone'] ?? 'N/A' }}</div>
                                    <div><strong>سررسید:</strong> {{ $data['contract']['payment_date'] ?? 'N/A' }}</div>
                                    @php
                                        $hasPaymentNotes = false;
                                        foreach ($data['notes'] as $note) {
                                            if ($note['type'] === 'payment') {
                                                $hasPaymentNotes = true;
                                                break;
                                            }
                                        }
                                    @endphp
                                    @if($hasPaymentNotes)
                                        <div class="mt-1 notes-container">
                                            @foreach ($data['notes'] as $note)
                                                @if ($note['type'] === 'payment')
                                                    @php
                                                        $noteRepository = app(\App\Repositories\NoteRepository::class);
                                                        $noteText = $noteRepository->formatNoteForDisplay($note);
                                                        $badgeStyle = $noteRepository->getNoteBadgeStyle($note['type']);
                                                    @endphp
                                                    <span class="badge rounded-pill note-badge"
                                                          style="{{ $badgeStyle }} position: relative; padding: 4px 20px 4px 8px; margin: 2px 0; display: block; font-size: 0.75rem; width: fit-content;">
                                                        {{ $noteText }}
                                                        <i class="fas fa-times-circle"
                                                           style="position: absolute; right: 3px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 0.65rem; color: #dc3545; opacity: 0.8;"
                                                           onclick="window.dispatchEvent(new CustomEvent('delete-note-event', { detail: { noteId: '{{ $note['id'] }}' } }))"
                                                           title="حذف یادداشت"
                                                           onmouseover="this.style.opacity='1'; this.style.transform='translateY(-50%) scale(1.2)';"
                                                           onmouseout="this.style.opacity='0.8'; this.style.transform='translateY(-50%) scale(1)';"></i>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

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
