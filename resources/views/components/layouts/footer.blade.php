<!-- Bootstrap Bundle with Popper -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset("assets/js/app.js") }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js"></script>

<script>

    let ticking = false;
    let lastScrollY = 0;
    let isNavbarShrunk = false;
    const SCROLL_THRESHOLD = 50;

    function updateNavbar() {
        const navbar = document.querySelector('.modern-navbar');
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

    window.addEventListener('scroll', function() {
        if (!ticking) {
            requestAnimationFrame(updateNavbar);
            ticking = true;
        }
    });

</script>
