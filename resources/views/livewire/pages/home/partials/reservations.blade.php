<div>
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
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
                        @if ($data['contract']['state'] == 'rezerve')
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
        /* --- استایل‌های اصلی برای اسکرول شرطی --- */

        /* ۱. رفتار پیش‌فرض برای دسکتاپ: بدون اسکرول افقی */
        .conditional-scroll-container {
            overflow-x: visible; /* در دسکتاپ اسکرولی نمایش داده نمی‌شود */
            border-radius: 0 0 0.375rem 0.375rem;
        }

        .conditional-scroll-table {
            width: 100%; /* جدول تمام عرض کانتینر را می‌گیرد */
            margin-bottom: 0;
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
                min-width: 900px;
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
                font-size: 0.85rem;
            }

            .conditional-scroll-table th,
            .conditional-scroll-table td {
                padding: 0.5rem 0.75rem;
            }

            /* استایل دکمه عملیات برای لمس راحت‌تر */
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
