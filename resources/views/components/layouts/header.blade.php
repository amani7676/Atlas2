<!-- Preconnect for faster resource loading -->
<link rel="preconnect" href="https://cdn.jsdelivr.net">
<link rel="preconnect" href="https://cdnjs.cloudflare.com">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="dns-prefetch" href="https://unpkg.com">

<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" crossorigin="anonymous">
<!-- Google Fonts (Vazir) -->
<link href="https://fonts.googleapis.com/css2?family=Vazirmatn:wght@300;400;500;700;900&display=swap" rel="stylesheet">

<!-- Custom CSS with cache busting -->
@php
    $cssPath = public_path('assets/css/app.css');
    $cssVersion = file_exists($cssPath) ? filemtime($cssPath) : time();
@endphp
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ $cssVersion }}">

<!-- Jalali Datepicker CSS -->
<link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css" crossorigin="anonymous">




@livewireStyles

<style>
    /* تنظیم فونت سایز 16px برای همه متن‌های سایت (دسکتاپ) */
    body, 
    body *,
    .form-control,
    .form-select,
    input,
    textarea,
    select,
    button,
    a,
    p,
    span,
    div,
    h1, h2, h3, h4, h5, h6,
    label,
    table,
    th,
    td {
        font-size: 16px !important;
    }
    
    /* استثنا برای عناصر کوچک */
    .small,
    small,
    .text-sm,
    .badge,
    .btn-sm {
        font-size: 14px !important;
    }
    
    /* استثنا برای عناصر خیلی کوچک */
    .text-xs {
        font-size: 12px !important;
    }

    /* تبلت (768px تا 1024px) - فونت بزرگتر */
    @media (min-width: 768px) and (max-width: 1024px) {
        body, 
        body *,
        .form-control,
        .form-select,
        input,
        textarea,
        select,
        button,
        a,
        p,
        span,
        div,
        label,
        table,
        th,
        td {
            font-size: 18px !important;
        }
        
        h1 { font-size: 28px !important; }
        h2 { font-size: 24px !important; }
        h3 { font-size: 22px !important; }
        h4 { font-size: 20px !important; }
        h5 { font-size: 18px !important; }
        h6 { font-size: 17px !important; }
        
        .small,
        small,
        .text-sm,
        .badge,
        .btn-sm {
            font-size: 16px !important;
        }
        
        .text-xs {
            font-size: 14px !important;
        }
    }

    /* موبایل (تا 767px) - فونت بزرگتر و bold برای خوانایی بهتر */
    @media (max-width: 767px) {
        body, 
        body *,
        .form-control,
        .form-select,
        input,
        textarea,
        select,
        button,
        a,
        p,
        span,
        div,
        label,
        table,
        th,
        td {
            font-size: 20px !important;
            font-weight: 600 !important;
        }
        
        h1 { 
            font-size: 32px !important; 
            font-weight: 700 !important;
        }
        h2 { 
            font-size: 28px !important; 
            font-weight: 700 !important;
        }
        h3 { 
            font-size: 26px !important; 
            font-weight: 700 !important;
        }
        h4 { 
            font-size: 24px !important; 
            font-weight: 700 !important;
        }
        h5 { 
            font-size: 22px !important; 
            font-weight: 700 !important;
        }
        h6 { 
            font-size: 21px !important; 
            font-weight: 700 !important;
        }
        
        .small,
        small,
        .text-sm,
        .badge,
        .btn-sm {
            font-size: 18px !important;
            font-weight: 600 !important;
        }
        
        .text-xs {
            font-size: 16px !important;
            font-weight: 600 !important;
        }
        
        /* دکمه‌ها در موبایل بزرگتر و bold */
        .btn {
            font-size: 20px !important;
            font-weight: 600 !important;
            padding: 0.6rem 1.2rem !important;
        }
        
        /* Input ها در موبایل بزرگتر و bold */
        .form-control,
        .form-select {
            font-size: 20px !important;
            font-weight: 600 !important;
            padding: 0.7rem 0.85rem !important;
        }
    }

    /* موبایل کوچک (تا 480px) - فونت حتی بزرگتر و bold */
    @media (max-width: 480px) {
        body, 
        body *,
        .form-control,
        .form-select,
        input,
        textarea,
        select,
        button,
        a,
        p,
        span,
        div,
        label,
        table,
        th,
        td {
            font-size: 21px !important;
            font-weight: 600 !important;
        }
        
        h1 { 
            font-size: 34px !important; 
            font-weight: 700 !important;
        }
        h2 { 
            font-size: 30px !important; 
            font-weight: 700 !important;
        }
        h3 { 
            font-size: 28px !important; 
            font-weight: 700 !important;
        }
        h4 { 
            font-size: 26px !important; 
            font-weight: 700 !important;
        }
        h5 { 
            font-size: 24px !important; 
            font-weight: 700 !important;
        }
        h6 { 
            font-size: 22px !important; 
            font-weight: 700 !important;
        }
        
        .small,
        small,
        .text-sm,
        .badge,
        .btn-sm {
            font-size: 19px !important;
            font-weight: 600 !important;
        }
        
        .text-xs {
            font-size: 17px !important;
            font-weight: 600 !important;
        }
        
        .btn {
            font-size: 21px !important;
            font-weight: 600 !important;
            padding: 0.7rem 1.3rem !important;
        }
        
        .form-control,
        .form-select {
            font-size: 21px !important;
            font-weight: 600 !important;
            padding: 0.8rem 0.9rem !important;
        }
    }

    /* بهبود منوی موبایل */
    @media (max-width: 991.98px) {
        .material-navbar {
            padding: 8px 0 !important;
        }

        .material-nav {
            gap: 0 !important;
            padding: 4px 0 !important;
        }

        .material-link {
            padding: 14px 20px !important;
            font-size: 18px !important;
            font-weight: 600 !important;
            border-radius: 0 !important;
            margin: 0 !important;
            width: 100% !important;
            display: flex !important;
            align-items: center !important;
        }

        .material-link-icon {
            width: 24px !important;
            height: 24px !important;
            font-size: 1.1rem !important;
            margin-left: 12px !important;
        }

        .material-link-text {
            font-size: 18px !important;
            font-weight: 600 !important;
            flex: 1 !important;
        }

        /* Dropdown در موبایل */
        .material-dropdown {
            width: 100% !important;
        }

        .material-dropdown .material-link {
            justify-content: space-between !important;
        }

        .material-dropdown-menu {
            position: static !important;
            float: none !important;
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            border-top: 1px solid rgba(0, 0, 0, 0.1) !important;
            border-radius: 0 !important;
            box-shadow: none !important;
            background: rgba(0, 0, 0, 0.03) !important;
        }

        .material-dropdown-item {
            padding: 14px 20px 14px 56px !important;
            font-size: 18px !important;
            font-weight: 600 !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
            display: block !important;
            width: 100% !important;
        }

        .material-dropdown-item:last-child {
            border-bottom: none !important;
        }

        .material-dropdown-item:hover,
        .material-dropdown-item:focus {
            background: rgba(187, 148, 87, 0.15) !important;
            color: #bb9457 !important;
        }

        .dropdown-toggle::after {
            margin-right: auto !important;
            margin-left: 12px !important;
            transition: transform 0.3s ease !important;
        }

        .dropdown-toggle[aria-expanded="true"]::after {
            transform: rotate(180deg) !important;
        }

        .material-search-container {
            margin: 12px 16px !important;
            padding: 0 !important;
        }

        .navbar-toggler {
            padding: 8px 12px !important;
            font-size: 1.2rem !important;
        }

        .material-toggler-icon {
            width: 28px !important;
            height: 28px !important;
        }

        .material-toggler-icon span {
            height: 3px !important;
        }

        #navbarContent {
            max-height: calc(100vh - 80px);
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
        }
    }

    /* موبایل کوچک (تا 480px) */
    @media (max-width: 480px) {
        .material-navbar {
            padding: 6px 0 !important;
        }

        .material-link {
            padding: 16px 20px !important;
            font-size: 19px !important;
        }

        .material-link-icon {
            width: 26px !important;
            height: 26px !important;
            font-size: 1.15rem !important;
            margin-left: 14px !important;
        }

        .material-link-text {
            font-size: 19px !important;
        }

        .material-dropdown-item {
            padding: 16px 20px 16px 60px !important;
            font-size: 19px !important;
        }

        .material-dropdown-menu {
            border-top: 1px solid rgba(0, 0, 0, 0.12) !important;
        }

        .material-search-container {
            margin: 10px 12px !important;
        }
    }
</style>
