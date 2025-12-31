<div class="card">
    <div class="card-header card-header-payments d-flex justify-content-between align-items-center">
        <span>بدهی‌ها</span>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>#</th>
                    <th>اتاق</th>
                    <th>نام</th>
                    <th>تلفن</th>
                    <th>سررسید</th>
                    <th>توضیح</th>
                    <th>عملیات</th>
                </tr>
                </thead>
                <tbody>

                @php $counter = 1; @endphp

                @foreach ($this->allReportService->getAllResidentsWithDetails() as $data)
                    @if ($data['notes']->contains(fn($note) => in_array($note['type'], ['payment'])))
                        <tr>
                            <td class="text-info">{{ $counter++ }}</td>
                            <td>{{ $data['room']['name'] }}</td>
                            <td>{{ $data['resident']['full_name'] }}</td>
                            <td>{{ $data['resident']['phone'] }}</td>
                            <td>{{ $data['contract']['payment_date'] }}</td>
                            <td style="max-width: 250px;">
                                @foreach ($data['notes'] as $note)
                                    @php
                                        $noteText = $note['note'];
                                        // اگر نوع end_date است، فقط ماه و روز را نمایش بده
                                        if ($note['type'] === 'end_date' && preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2})/', $noteText, $matches)) {
                                            $noteText = $matches[2] . '/' . $matches[3];
                                        } else {
                                            $noteText = $this->noteRepository->formatNoteForDisplay($note);
                                        }
                                        $badgeStyle = $this->noteRepository->getNoteBadgeStyle($note['type']);
                                    @endphp
                                    <span class="badge rounded-pill"
                                          style="{{ $badgeStyle }} position: relative; padding: 6px 22px 6px 10px; margin: 2px; display: inline-block; font-size: 0.85rem;">
                                        {{ $noteText }}
                                        <i class="fas fa-times-circle"
                                           style="position: absolute; right: 4px; top: 50%; transform: translateY(-50%); cursor: pointer; font-size: 0.7rem; color: #dc3545; opacity: 0.8;"
                                           onclick="window.dispatchEvent(new CustomEvent('delete-note-event', { detail: { noteId: '{{ $note['id'] }}' } }))"
                                           title="حذف یادداشت"
                                           onmouseover="this.style.opacity='1'; this.style.transform='translateY(-50%) scale(1.2)';"
                                           onmouseout="this.style.opacity='0.8'; this.style.transform='translateY(-50%) scale(1)';"></i>
                                    </span>
                                @endforeach
                            </td>
                            <td>
                                <a href="{{ route('table_list') }}#{{ $data['room']['name'] }}" target="_blank"
                                   class="text-primary action-btn">
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
