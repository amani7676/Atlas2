<!-- Local Assets Footer - Works Offline -->
<!-- Bootstrap Bundle with Popper -->
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}" defer></script>

@php
    $jsPath = public_path('assets/js/app.js');
    $jsVersion = file_exists($jsPath) ? filemtime($jsPath) : time();
@endphp
<script src="{{ asset("assets/js/app.js") }}?v={{ $jsVersion }}" defer></script>

<!-- Vite compiled assets -->
@vite(['resources/js/app.js'])

<!-- Local JavaScript Libraries -->
<script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/chart.min.js') }}"></script>
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>

<!-- Simple date fallback - no external library needed -->
<script>
    console.log('Using simple date fallback...');
    
    // Create simple datepicker function
    window.jalaliDatepicker = function(element, options) {
        if (typeof element === 'string') {
            element = document.querySelector(element);
        }
        if (element) {
            element.type = 'date';
            console.log('Using native HTML5 date picker for:', element);
        }
        return {
            show: function() { console.log('Date picker show called'); },
            hide: function() { console.log('Date picker hide called'); }
        };
    };
    
    console.log('Simple date fallback created successfully!');
</script>

<script>
    let ticking = false;
    let lastScrollY = 0;
    let isNavbarShrunk = false;
    const SCROLL_THRESHOLD = 50;

    function updateNavbar() {
        const navbar = document.querySelector('.material-navbar') || document.querySelector('.modern-navbar');
        if (!navbar) return;
        
        const currentScrollY = window.scrollY;

        // اضافه کردن hysteresis برای جلوگیری از لرزش
        const shrinkPoint = SCROLL_THRESHOLD;
        const expandPoint = SCROLL_THRESHOLD - 36; // کمی کمتر برای hysteresis

        if (currentScrollY >= shrinkPoint && !isNavbarShrunk) {
            navbar.classList.add('shrink');
            isNavbarShrunk = true;
        } else if (currentScrollY <= expandPoint && isNavbarShrunk) {
            navbar.classList.remove('shrink');
            isNavbarShrunk = false;
        }

        lastScrollY = currentScrollY;
        ticking = false;
    }
    
    // Material Design Ripple Effect
    document.addEventListener('DOMContentLoaded', function() {
        const rippleElements = document.querySelectorAll('[data-ripple]');
        
        rippleElements.forEach(element => {
            element.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = x + 'px';
                ripple.style.top = y + 'px';
                ripple.classList.add('ripple');
                
                this.appendChild(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });
        });
        
        // Active link detection - فقط برای لینک‌های مستقیم (نه dropdown toggle)
        const currentPath = window.location.pathname;
        const directLinks = document.querySelectorAll('.material-link:not(.dropdown-toggle)');
        
        directLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (!href || href === '#' || href === 'javascript:void(0)') {
                link.classList.remove('active');
                return;
            }
            
            try {
                const hrefUrl = new URL(href, window.location.origin);
                const hrefPath = hrefUrl.pathname;
                
                // برای صفحه اصلی (/)
                if (hrefPath === '/' || hrefPath === '') {
                    if (currentPath === '/' || currentPath === '') {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                } else {
                    // برای سایر صفحات - فقط اگر مسیر دقیقاً یکسان باشد
                    if (currentPath === hrefPath) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                }
            } catch (e) {
                // اگر URL معتبر نبود، از مقایسه ساده استفاده می‌کنیم
                const homeRoute = '{{ route("home") }}';
                if (currentPath === '/' || currentPath === '') {
                    if (href === homeRoute || href === '/' || href.endsWith('/')) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                } else {
                    if (href === currentPath) {
                        link.classList.add('active');
                    } else {
                        link.classList.remove('active');
                    }
                }
            }
        });
        
        // برای dropdown toggle ها - فقط اگر یکی از آیتم‌های dropdown active باشد
        const dropdownToggles = document.querySelectorAll('.material-link.dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            const dropdown = toggle.closest('.material-dropdown');
            if (dropdown) {
                const activeItem = dropdown.querySelector('.material-dropdown-item.active');
                if (activeItem) {
                    toggle.classList.add('active');
                } else {
                    toggle.classList.remove('active');
                }
            }
        });

        // بهبود dropdown در موبایل - استفاده از Bootstrap dropdown
        if (window.innerWidth <= 991.98) {
            // استفاده از Bootstrap dropdown API
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const dropdown = bootstrap.Dropdown.getInstance(this);
                    if (dropdown) {
                        dropdown.toggle();
                    } else {
                        // اگر dropdown instance وجود نداشت، ایجاد کن
                        new bootstrap.Dropdown(this).toggle();
                    }
                });
            });

            // بستن dropdown با کلیک خارج از آن
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.material-dropdown')) {
                    document.querySelectorAll('.dropdown-toggle[aria-expanded="true"]').forEach(toggle => {
                        const dropdown = bootstrap.Dropdown.getInstance(toggle);
                        if (dropdown) {
                            dropdown.hide();
                        }
                    });
                }
            });
        }
    });

    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    });

</script>
