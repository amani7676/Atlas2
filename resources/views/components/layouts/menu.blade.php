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
                <div class="sms-credit-circle" id="smsCreditValue" onclick="loadSmsCredit()" title="اعتبار پیامک - کلیک برای تازه‌سازی">
                    <i class="fas fa-sms"></i>
                    <span class="sms-credit-number">
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

<style>
/* Simple Circular SMS Credit */
.material-sms-credit-container {
    position: relative;
}

.sms-credit-circle {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.3);
    position: relative;
    overflow: hidden;
}

.sms-credit-circle:hover {
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.5);
}

.sms-credit-circle:active {
    transform: scale(0.95);
}

.sms-credit-circle i.fa-sms {
    font-size: 16px;
    margin-bottom: 2px;
    opacity: 0.9;
}

.sms-credit-number {
    font-size: 11px;
    font-weight: bold;
    line-height: 1;
}

.sms-credit-circle.bg-success {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%) !important;
    box-shadow: 0 2px 10px rgba(40, 167, 69, 0.3);
}

.sms-credit-circle.bg-warning {
    background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%) !important;
    box-shadow: 0 2px 10px rgba(255, 193, 7, 0.3);
}

.sms-credit-circle.bg-danger {
    background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%) !important;
    box-shadow: 0 2px 10px rgba(220, 53, 69, 0.3);
}

.sms-credit-circle.bg-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    box-shadow: 0 2px 10px rgba(108, 117, 125, 0.3);
}

/* Loading animation */
.sms-credit-circle .fa-spinner {
    font-size: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .sms-credit-circle {
        width: 45px;
        height: 45px;
    }
    
    .sms-credit-circle i.fa-sms {
        font-size: 14px;
    }
    
    .sms-credit-number {
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .sms-credit-circle {
        width: 40px;
        height: 40px;
    }
    
    .sms-credit-circle i.fa-sms {
        font-size: 12px;
        margin-bottom: 1px;
    }
    
    .sms-credit-number {
        font-size: 9px;
    }
}

/* Rotation animation for loading */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.sms-credit-circle.loading {
    animation: spin 1s linear infinite;
}
</style>

<script>
// Load SMS credit after page is completely loaded
window.addEventListener('load', function() {
    // Use setTimeout to ensure it doesn't block anything
    setTimeout(function() {
        loadSmsCredit();
    }, 1000);
});

function loadSmsCredit() {
    const creditCircle = document.getElementById('smsCreditValue');
    
    // Don't load if element doesn't exist
    if (!creditCircle) return;
    
    // Add loading animation
    creditCircle.classList.add('loading');
    creditCircle.style.pointerEvents = 'none';
    
    // Show loading state
    creditCircle.innerHTML = '<i class="fas fa-sms"></i><span class="sms-credit-number"><i class="fas fa-spinner fa-spin"></i></span>';
    creditCircle.className = 'sms-credit-circle bg-secondary loading';
    creditCircle.title = 'در حال بارگذاری...';
    
    fetch('/api/sms/credit', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        }
    })
    .then(response => response.json())
    .then(data => {
        const creditCircle = document.getElementById('smsCreditValue');
        if (creditCircle) {
            creditCircle.classList.remove('loading');
            creditCircle.style.pointerEvents = 'auto';
        }
        
        if (data.success && data.credit) {
            updateSmsCreditDisplay(data.credit);
        } else {
            showSmsCreditError('خطا');
        }
    })
    .catch(error => {
        const creditCircle = document.getElementById('smsCreditValue');
        if (creditCircle) {
            creditCircle.classList.remove('loading');
            creditCircle.style.pointerEvents = 'auto';
        }
        
        console.error('SMS credit load error:', error);
        showSmsCreditError('خطا');
    });
}

function updateSmsCreditDisplay(creditData) {
    const creditCircle = document.getElementById('smsCreditValue');
    
    if (!creditCircle) return;
    
    creditCircle.innerHTML = `<i class="fas fa-sms"></i><span class="sms-credit-number">${creditData.value}</span>`;
    creditCircle.className = `sms-credit-circle bg-${creditData.color}`;
    creditCircle.title = `اعتبار پیامک: ${creditData.value} - آخرین به‌روزرسانی: ${new Date().toLocaleTimeString('fa-IR')}`;
}

function showSmsCreditError(message) {
    const creditCircle = document.getElementById('smsCreditValue');
    
    if (!creditCircle) return;
    
    creditCircle.innerHTML = `<i class="fas fa-sms"></i><span class="sms-credit-number">${message}</span>`;
    creditCircle.className = 'sms-credit-circle bg-secondary';
    creditCircle.title = 'خطا در بارگذاری اعتبار پیامک';
}
</script>
