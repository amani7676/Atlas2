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
<link rel="stylesheet" href="{{ asset('assets/css/app.css') }}?v={{ filemtime(public_path('assets/css/app.css')) }}">

<!-- Jalali Datepicker CSS -->
<link rel="stylesheet" href="https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css" crossorigin="anonymous">




@livewireStyles

<style>
    /* تنظیم فونت سایز 16px برای همه متن‌های سایت */
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
</style>
