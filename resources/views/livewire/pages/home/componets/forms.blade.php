<div class="card {{ $isExpanded ? 'h-100' : '' }}">
    <div class="card-header d-flex justify-content-between align-items-center" style="cursor: pointer;" wire:click="toggle">
        <span class="fw-bold">فرم‌ها</span>
        <i class="fas {{ $isExpanded ? 'fa-chevron-up' : 'fa-chevron-down' }}"></i>
    </div>
    @if($isExpanded)
    <div class="card-body p-2 p-md-3">
        <div class="table-responsive">
            <table class="table table-hover table-sm mb-0">
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
                       @if (!$data['resident']['form'])
                            @php
                                $counter++;
                            @endphp
                            <tr>
                                <td class="text-info d-none d-md-table-cell">{{ $counter }}</td>
                                <td>
                                    <span class="d-md-none fw-bold">{{ $counter }}. </span>
                                    {{ $data['room']['name'] }}
                                </td>
                                <td class="d-none d-lg-table-cell">{{ $data['resident']['full_name'] }}</td>
                                <td class="d-none d-xl-table-cell">{{ $data['resident']['phone'] }}</td>
                                <td class="d-none d-lg-table-cell">{{ $data['contract']['payment_date'] }}</td>
                                <td class="d-none d-md-table-cell" style="max-width: 200px;">
                                    @foreach ($data['notes'] as $note)
                                        @if ($note['type'] === 'payment')
                                            @php
                                                $noteRepository = app(\App\Repositories\NoteRepository::class);
                                                $noteText = $noteRepository->formatNoteForDisplay($note);
                                                $badgeStyle = $noteRepository->getNoteBadgeStyle($note['type']);
                                            @endphp
                                            <span class="badge rounded-pill"
                                                  style="{{ $badgeStyle }} position: relative; padding: 4px 20px 4px 8px; margin: 2px; display: inline-block; font-size: 0.75rem;">
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
                                </td>
                                <td>
                                    <a href="#" wire:click.prevent="giveForm({{ $data['resident']['id'] }})"
                                        class="text-success action-btn">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                </td>
                            </tr>
                            {{-- ردیف اطلاعات موبایل --}}
                            <tr class="d-md-none">
                                <td colspan="2" class="small text-muted border-top-0 pt-0">
                                    <div><strong>نام:</strong> {{ $data['resident']['full_name'] }}</div>
                                    <div><strong>تلفن:</strong> {{ $data['resident']['phone'] }}</div>
                                    <div><strong>سررسید:</strong> {{ $data['contract']['payment_date'] }}</div>
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
                                        <div class="mt-1">
                                            @foreach ($data['notes'] as $note)
                                                @if ($note['type'] === 'payment')
                                                    @php
                                                        $noteRepository = app(\App\Repositories\NoteRepository::class);
                                                        $noteText = $noteRepository->formatNoteForDisplay($note);
                                                        $badgeStyle = $noteRepository->getNoteBadgeStyle($note['type']);
                                                    @endphp
                                                    <span class="badge rounded-pill"
                                                          style="{{ $badgeStyle }} position: relative; padding: 4px 20px 4px 8px; margin: 2px; display: inline-block; font-size: 0.75rem;">
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
</div>
