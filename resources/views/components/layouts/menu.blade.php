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
                    <a class="nav-link material-link {{ request()->routeIs('table_list') ? 'active' : '' }}" href="{{ route('table_list') }}">
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
                    <a class="nav-link material-link dropdown-toggle {{ request()->routeIs('amval') || request()->routeIs('amval.type') || request()->routeIs('amval.detail') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-boxes"></i>
                        </span>
                        <span class="material-link-text">دارایی‌ها</span>
                        <i class="fas fa-chevron-down dropdown-arrow ms-1"></i>
                    </a>
                    <ul class="dropdown-menu material-dropdown-menu">
                        <li>
                            <a class="dropdown-item material-dropdown-item {{ request()->routeIs('amval') ? 'active' : '' }}" href="{{ route('amval') }}" data-ripple>
                                <i class="fas fa-list me-2"></i>
                                مدیریت انواع دارایی‌ها
                            </a>
                        </li>
                        @php
                            use App\Models\AssetType;
                            $assetTypes = AssetType::active()->get();
                        @endphp
                        @if($assetTypes->isNotEmpty())
                            <li><hr class="dropdown-divider"></li>
                            @foreach($assetTypes as $type)
                                <li>
                                    <a class="dropdown-item material-dropdown-item {{ request()->routeIs('amval.type') && request()->route('assetTypeId') == $type->id ? 'active' : '' }}" href="{{ route('amval.type', $type->id) }}" data-ripple>
                                        <i class="{{ $type->icon ?? 'fas fa-box' }} me-2"></i>
                                        {{ $type->name }}
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </li>
                <li class="nav-item dropdown material-dropdown">
                    <a class="nav-link material-link dropdown-toggle {{ request()->routeIs('dormitory.builder') || request()->routeIs('Bed_statistic') || request()->routeIs('rules.manager') || request()->routeIs('resident.contacts') || request()->routeIs('message.system') || request()->routeIs('message.sender') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" data-ripple>
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
                        <li><hr class="dropdown-divider"></li>
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

                <li class="nav-item">
                    <a class="nav-link material-link logout-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" data-ripple>
                        <span class="material-link-icon">
                            <i class="fas fa-sign-out-alt"></i>
                        </span>
                        <span class="material-link-text">خروج</span>
                    </a>
                </li>
            </ul>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>

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

/* Logout Link Style */
.logout-link {
    color: #dc3545 !important;
}

.logout-link:hover {
    background: rgba(220, 53, 69, 0.1) !important;
}

.logout-link .material-link-icon {
    color: #dc3545 !important;
}

/* Mobile Menu Improvements */
@media (max-width: 991.98px) {
    .navbar-collapse {
        background: white;
        border-radius: 12px;
        padding: 20px;
        margin-top: 15px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }
    
    .material-nav {
        flex-direction: column;
        width: 100%;
        gap: 4px;
    }
    
    .nav-item {
        width: 100%;
    }
    
    .material-link {
        display: flex;
        align-items: center;
        padding: 12px 16px;
        border-radius: 8px;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .material-link:hover {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    }
    
    .material-link.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .material-link.active .material-link-icon {
        color: white;
    }
    
    .material-dropdown-menu {
        position: static !important;
        transform: none !important;
        width: 100%;
        margin-top: 0;
        padding: 8px;
        background: #f8f9fa;
        border-radius: 8px;
        box-shadow: none;
        border: 1px solid #e9ecef;
        display: none;
    }
    
    .show > .material-dropdown-menu {
        display: block;
        margin-top: 4px;
    }
    
    .dropdown-arrow {
        display: none;
    }
    
    .material-dropdown-item {
        padding: 8px 16px;
        border-radius: 6px;
        margin-bottom: 2px;
    }
    
    .material-dropdown-item:hover {
        background: rgba(102, 126, 234, 0.1);
    }
    
    .material-dropdown-item.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    
    .material-dropdown-item.active i {
        color: white;
    }
    
    .material-search-container {
        order: -1;
        margin-bottom: 15px;
    }
    
    .material-sms-credit-container {
        order: -2;
        margin-bottom: 15px;
        margin-right: 0;
    }
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

// Fix dropdown toggle on mobile
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
    const navLinks = document.querySelectorAll('.material-link:not(.dropdown-toggle)');
    
    dropdownToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function(e) {
            const isMobile = window.innerWidth <= 991.98;
            if (isMobile) {
                const dropdownMenu = this.nextElementSibling;
                const isExpanded = this.classList.contains('show') || dropdownMenu.classList.contains('show');
                
                // Close all other dropdowns first
                document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                    if (menu !== dropdownMenu) {
                        menu.classList.remove('show');
                        menu.previousElementSibling.classList.remove('show');
                        menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Toggle current dropdown
                if (isExpanded) {
                    dropdownMenu.classList.remove('show');
                    this.classList.remove('show');
                    this.setAttribute('aria-expanded', 'false');
                    e.stopPropagation();
                } else {
                    dropdownMenu.classList.add('show');
                    this.classList.add('show');
                    this.setAttribute('aria-expanded', 'true');
                    e.stopPropagation();
                }
            }
        });
    });
    
    // Close all dropdowns when clicking on non-dropdown menu items
    navLinks.forEach(function(link) {
        link.addEventListener('click', function() {
            const isMobile = window.innerWidth <= 991.98;
            if (isMobile) {
                document.querySelectorAll('.dropdown-menu').forEach(function(menu) {
                    menu.classList.remove('show');
                    menu.previousElementSibling.classList.remove('show');
                    menu.previousElementSibling.setAttribute('aria-expanded', 'false');
                });
            }
        });
    });
});
</script>
