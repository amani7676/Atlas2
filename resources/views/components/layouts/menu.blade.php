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
                    <a class="nav-link material-link {{ request()->routeIs('reservations') ? 'active' : '' }}" href="{{ route("reservations") }}" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </span>
                        <span class="material-link-text">رزرو کردن</span>
                    </a>
                </li>

                <li class="nav-item dropdown material-dropdown">
                    <a class="nav-link material-link dropdown-toggle {{ request()->routeIs('report.*') || request()->routeIs('report.exited_residents') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-ripple>
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
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('report.exited_residents') ? 'active' : '' }}" href="{{ route('report.exited_residents') }}" data-ripple>
                                <i class="fas fa-sign-out-alt me-2"></i>
                                اقامتگران خروجی
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown material-dropdown">
                    <a class="nav-link material-link dropdown-toggle {{ request()->routeIs('message.system') || request()->routeIs('message.sender') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <span class="material-link-text">پیام‌ها</span>
                        <i class="fas fa-chevron-down dropdown-arrow ms-1"></i>
                    </a>
                    <ul class="dropdown-menu material-dropdown-menu">
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('message.system') ? 'active' : '' }}" href="{{ route('message.system') }}" data-ripple>
                                <i class="fas fa-cog me-2"></i>
                                سیستم پیام
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('message.sender') ? 'active' : '' }}" href="{{ route('message.sender') }}" data-ripple>
                                <i class="fas fa-paper-plane me-2"></i>
                                ارسال پیام
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown material-dropdown">
                    <a class="nav-link material-link dropdown-toggle {{ request()->routeIs('dormitory.builder') || request()->routeIs('coolers') || request()->routeIs('keys') || request()->routeIs('heaters') || request()->routeIs('Bed_statistic') || request()->routeIs('rules.manager') || request()->routeIs('resident.contacts') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-ripple>
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
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('heaters') ? 'active' : '' }}" href="{{ route('heaters') }}" data-ripple>
                                <i class="fas fa-fire me-2"></i>
                                هیترها
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('Bed_statistic') ? 'active' : '' }}" href="{{ route("Bed_statistic") }}" data-ripple>
                                <i class="fas fa-chart-bar me-2"></i>
                                آمار تخت‌ها
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('rules.manager') ? 'active' : '' }}" href="{{ route('rules.manager') }}" data-ripple>
                                <i class="fas fa-gavel me-2"></i>
                                قوانین
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('category.management') ? 'active' : '' }}" href="{{ route('category.management') }}" data-ripple>
                                <i class="fas fa-folder me-2"></i>
                                مدیریت دسته بندی‌ها
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('resident.contacts') ? 'active' : '' }}" href="{{ route('resident.contacts') }}" data-ripple>
                                <i class="fas fa-phone-alt me-2"></i>
                                شماره تماس اقامتگران
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- SMS Credit Display -->
            <div class="material-sms-credit-container me-3">
                <div class="d-flex align-items-center">
                    <span class="badge bg-secondary material-sms-credit-value" id="smsCreditValue">
                        <i class="fas fa-spinner fa-spin"></i>
                    </span>
                </div>
            </div>

            <!-- Search Section -->
            <div class="material-search-container">
                <livewire:components.live-search/>
            </div>
        </div>
    </div>
</nav>

<script>
// Load SMS credit after page is completely loaded
window.addEventListener('load', function() {
    // Use setTimeout to ensure it doesn't block anything
    setTimeout(function() {
        loadSmsCredit();
    }, 1000);
});

function loadSmsCredit() {
    const creditValue = document.getElementById('smsCreditValue');
    
    // Don't load if element doesn't exist
    if (!creditValue) return;
    
    fetch('/api/sms/credit', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.credit) {
            updateSmsCreditDisplay(data.credit);
        } else {
            showSmsCreditError('خطا');
        }
    })
    .catch(error => {
        console.error('SMS credit load error:', error);
        showSmsCreditError('خطا');
    });
}

function updateSmsCreditDisplay(creditData) {
    const creditValue = document.getElementById('smsCreditValue');
    
    if (!creditValue) return;
    
    creditValue.textContent = creditData.value;
    creditValue.className = `badge bg-${creditData.color} material-sms-credit-value`;
    creditValue.title = `آخرین به‌روزرسانی: ${new Date().toLocaleTimeString('fa-IR')}`;
}

function showSmsCreditError(message) {
    const creditValue = document.getElementById('smsCreditValue');
    
    if (!creditValue) return;
    
    creditValue.textContent = message;
    creditValue.className = 'badge bg-secondary material-sms-credit-value';
    creditValue.title = 'خطا';
}
</script>
