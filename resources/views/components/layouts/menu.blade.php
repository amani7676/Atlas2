<nav class="navbar navbar-expand-lg material-navbar" id="materialNavbar">
    <div class="container-fluid px-4">
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler material-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="material-toggler-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Main Navigation Menu -->
            <ul class="navbar-nav material-nav ms-auto me-3">
                <li class="nav-item">
                    <a class="nav-link material-link {{ request()->routeIs('home') || request()->path() === '/' ? 'active' : '' }}" href="{{ route("home") }}" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-home"></i>
                        </span>
                        <span class="material-link-text">صفحه اصلی</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link material-link {{ request()->routeIs('table_list') ? 'active' : '' }}" href="{{ route('table_list') }}" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-list"></i>
                        </span>
                        <span class="material-link-text">لیست اقامتگران</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link material-link {{ request()->routeIs('Bed_statistic') ? 'active' : '' }}" href="{{ route("Bed_statistic") }}" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-chart-bar"></i>
                        </span>
                        <span class="material-link-text">آمار تخت‌ها</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link material-link {{ request()->routeIs('reservations') ? 'active' : '' }}" href="{{ route("reservations") }}" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </span>
                        <span class="material-link-text">رزرو کردن</span>
                    </a>
                </li>

                <li class="nav-item dropdown material-dropdown">
                    <a class="nav-link material-link dropdown-toggle {{ request()->routeIs('report.*') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-file-alt"></i>
                        </span>
                        <span class="material-link-text">گزارش‌ها</span>
                        <i class="fas fa-chevron-down dropdown-arrow ms-1"></i>
                    </a>
                    <ul class="dropdown-menu material-dropdown-menu">
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('report.list_current_resident') ? 'active' : '' }}" href="{{ route("report.list_current_resident") }}" data-ripple>
                                <i class="fas fa-users me-2"></i>
                                اقای عنایتی
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('report.chart_one') ? 'active' : '' }}" href="{{ route("report.chart_one") }}" data-ripple>
                                <i class="fas fa-chart-line me-2"></i>
                                Chart One
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item" href="#" data-ripple>
                                <i class="fas fa-sign-out-alt me-2"></i>
                                اقامتگران خروجی
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown material-dropdown">
                    <a class="nav-link material-link dropdown-toggle {{ request()->routeIs('dormitory.builder') || request()->routeIs('coolers') || request()->routeIs('keys') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-ellipsis-h"></i>
                        </span>
                        <span class="material-link-text">سایر</span>
                        <i class="fas fa-chevron-down dropdown-arrow ms-1"></i>
                    </a>
                    <ul class="dropdown-menu material-dropdown-menu">
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('dormitory.builder') ? 'active' : '' }}" href="{{ route('dormitory.builder') }}" data-ripple>
                                <i class="fas fa-building me-2"></i>
                                ساخت خوابگاه
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('coolers') ? 'active' : '' }}" href="{{ route("coolers") }}" data-ripple>
                                <i class="fa-solid fa-fan me-2"></i>
                                کولرها
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('keys') ? 'active' : '' }}" href="{{ route('keys') }}" data-ripple>
                                <i class="fa-solid fa-key me-2"></i>
                                کلیدها
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Search Section -->
            <div class="material-search-container">
                <livewire:components.live-search/>
            </div>
        </div>
    </div>
</nav>
