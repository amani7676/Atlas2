<nav class="navbar navbar-expand-lg modern-navbar">
    <div class="container">
        <!-- دکمه موبایل -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- منوی اصلی -->
            <ul class="nav-menu ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route("home") }}">
                        <i class="fas fa-home"></i>
                        صفحه اصلی
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('table_list') }}">
                        <i class="fas fa-list"></i>
                        لیست اقامتگران
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route("Bed_statistic") }}">
                        <i class="fas fa-chart-bar"></i>
                        آمار تخت ها
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route("reservations") }}">
                        <i class="fas fa-calendar-plus"></i>
                        رزرو کردن
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-file-alt"></i>
                        گزارش‌ها
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route("report.list_current_resident") }}">
                                <i class="fas fa-users"></i>
                                اقای عنایتی
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route("report.chart_one") }}">
                                <i class="fas fa-users"></i>
                                Chart One
                            </a>
                        </li>
                        <li><a class="dropdown-item" href="#">
                                <i class="fas fa-sign-out-alt"></i>
                                اقامتگران خروجی
                            </a></li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route("coolers") }}">
                        <i class="fa-solid fa-fan"></i>
                        کولرها
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('keys') }}">
                        <i class="fa-solid fa-key"></i>
                        کلیدها
                    </a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-h"></i>
                        سایر
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="{{ route('dormitory.builder') }}">
                                <i class="fas fa-building"></i>
                                ساخت خوابگاه
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- بخش جستجو با Livewire -->
            <div style="position: relative;">
                <livewire:components.live-search/>
            </div>
        </div>
    </div>
</nav>
