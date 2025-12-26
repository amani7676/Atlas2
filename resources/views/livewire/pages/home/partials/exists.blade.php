<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="span-exit">خروجی‌ها</span>
        </div>
        <div class="card-body p-0">
            <!-- این کانتینر اسکرول افقی را مدیریت می‌کند -->
            <div class="scrollable-table-container">
                <table class="table table-hover responsive-data-table">
                    <thead>
                    <tr class="tr-exit">
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

                    @foreach ($this->allReportService->getAllResidentsWithDetails('contract.day_since_payment', 'asc') as $data)
                        @if ($data['contract']['state'] === 'leaving')
                            @php $counter++; @endphp
                            <tr>
                                <td class="text-info">{{ $counter }}</td>
                                <td>{{ $data['room']['name'] }}</td>
                                <td>{{ $data['bed']['name'] }} <i class="fa-solid fa-water"></i>
                                    {{ $data['room']['bed_count'] }}</td>
                                <td>{{ $data['resident']['full_name'] }}</td>
                                <td>{{ $data['resident']['phone'] }}</td>
                                <td>{{ $data['contract']['payment_date'] }}</td>
                                <td>
                                    {!! $statusService->getStatusBadge($data['contract']['day_since_payment']) !!}
                                </td>
                                <td>
                                    @foreach ($data['notes'] as $note)
                                        <span class="badge rounded-pill text-bg-info p-2">{{ $note['note'] }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <a href="{{ route('table_list')}}#{{ $data['room']['name'] }}" target="_blank"
                                       class="action-btn">
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
        /* --- استایل‌های اصلی برای اسکرول افقی قطعی --- */

        /* ۱. کانتینر اصلی که اسکرول را ایجاد می‌کند */
        .scrollable-table-container {
            overflow-x: auto; /* فعال‌سازی اسکرول افقی */
            -webkit-overflow-scrolling: touch; /* برای اسکرول نرم در آیفون */
            border-radius: 0 0 0.375rem 0.375rem; /* گرد کردن گوشه‌های پایین */
        }

        /* ۲. جدول - این مهم‌ترین بخش است */
        .responsive-data-table {
            /* این مقدار باعث می‌شود جدول همیشه عریض‌تر از صفحه موبایل باشد */
            min-width: 900px;
            margin-bottom: 0;
        }

        /* ۳. جلوگیری از شکستن متن در سلول‌ها */
        .responsive-data-table th,
        .responsive-data-table td {
            /* این ویژگی تضمین می‌کند که محتوای هر سلول در یک خط باقی بماند */
            white-space: nowrap;
            vertical-align: middle;
        }

        /* --- استایل‌های بهبوددهنده برای موبایل --- */
        @media (max-width: 768px) {
            /* فونت را کمی کوچک‌تر می‌کنیم تا محتوا بهتر جا شود */
            .responsive-data-table {
                font-size: 0.85rem;
            }

            /* فاصله داخلی سلول‌ها را کم می‌کنیم */
            .responsive-data-table th,
            .responsive-data-table td {
                padding: 0.5rem 0.75rem;
            }

            /* استایل دکمه عملیات برای لمس راحت‌تر در موبایل */
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
                transition: background-color 0.2s, color 0.2s;
            }

            .action-btn:hover {
                background-color: #0d6efd;
                color: white !important;
            }
        }

        /* استایل برای دسکتاپ */
        @media (min-width: 769px) {
            .action-btn {
                color: #0d6efd;
                text-decoration: none;
                transition: transform 0.2s;
            }
            .action-btn:hover {
                transform: scale(1.2);
            }
        }
    </style>
</div>
