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
                                            <span class="badge rounded-pill text-bg-info p-1 small">{{ $note['note'] }}</span>
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
                                                    <span class="badge rounded-pill text-bg-info p-1 small">{{ $note['note'] }}</span>
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
