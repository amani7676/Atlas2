# راهنمای نصب و استفاده از Assets آفلاین

این راهنما به شما کمک می‌کند تا تمام کتابخانه‌های خارجی را دانلود کرده و به صورت محلی استفاده کنید تا سایت شما بدون اینترنت نیز کار کند.

## مرحله ۱: دانلود کتابخانه‌ها

### روش ۱: استفاده از اسکریپت خودکار (توصیه شده)

```bash
# در ریشه پروژه لاراول خود اجرا کنید:
php download-assets.php
```

این اسکریپت تمام کتابخانه‌های زیر را دانلود و در پوشه `public/assets` ذخیره می‌کند:

#### CSS Files:
- Bootstrap 5.3.0
- Font Awesome 6.0.0
- Vazir Font (Google Fonts)
- Jalali Datepicker

#### JavaScript Files:
- Bootstrap Bundle with Popper
- SweetAlert2
- Chart.js
- jQuery 3.6.0
- Jalali Datepicker

#### Fonts:
- Vazir Font files (تمام وزن‌ها)

### روش ۲: دانلود دستی

اگر اسکریپت کار نکرد، می‌توانید فایل‌ها را دستی دانلود کنید:

```bash
# ایجاد پوشه‌های مورد نیاز
mkdir -p public/assets/css
mkdir -p public/assets/js
mkdir -p public/assets/fonts/vazir

# دانلود CSS ها
wget -O public/assets/css/bootstrap.min.css https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css
wget -O public/assets/css/fontawesome.min.css https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css
wget -O public/assets/css/jalalidatepicker.min.css https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.css

# دانلود JS ها
wget -O public/assets/js/bootstrap.bundle.min.js https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js
wget -O public/assets/js/sweetalert2.min.js https://cdn.jsdelivr.net/npm/sweetalert2@11
wget -O public/assets/js/chart.min.js https://cdn.jsdelivr.net/npm/chart.js
wget -O public/assets/js/jquery.min.js https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js
wget -O public/assets/js/jalalidatepicker.min.js https://unpkg.com/@majidh1/jalalidatepicker/dist/jalalidatepicker.min.js
```

## مرحله ۲: به‌روزرسانی قالب‌ها

برای استفاده از assets محلی، باید قالب‌های خود را به نسخه‌های محلی تغییر دهید:

### تغییر در فایل‌های Blade:

**به جای:**
```blade
@include('components.layouts.header')
```

**استفاده کنید:**
```blade
@include('components.layouts.header-local')
```

**به جای:**
```blade
@include('components.layouts.footer')
```

**استفاده کنید:**
```blade
@include('components.layouts.footer-local')
```

**یا به طور کامل:**
```blade
@include('components.layouts.app-local')
```

### تغییرات اصلی در قالب‌ها:

#### header-local.blade.php:
- حذف CDN links
- اضافه کردن local asset links
- حذف preconnect و dns-prefetch

#### footer-local.blade.php:
- حذف CDN script tags
- اضافه کردن local script tags

## مرحله ۳: ساخت Vite Assets

```bash
# نصب وابستگی‌ها
npm install

# ساخت assets برای production
npm run build
```

## مرحله ۴: تست آفلاین

برای تست اینکه آیا assets به درستی کار می‌کنند:

1. **در مرورگر:** Developer Tools → Network tab → Offline
2. **یا:** اینترنت خود را قطع کرده و صفحه را رفرش کنید

## مرحله ۵: تنظیم Cache (اختیاری)

برای بهینه‌سازی بیشتر، می‌توانید cache headers را در `.htaccess` تنظیم کنید:

```apache
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 year"
    ExpiresByType application/javascript "access plus 1 year"
    ExpiresByType font/woff2 "access plus 1 year"
    ExpiresByType font/woff "access plus 1 year"
</IfModule>

<IfModule mod_headers.c>
    <FilesMatch "\.(css|js|woff|woff2)$">
        Header set Cache-Control "public, max-age=31536000"
    </FilesMatch>
</IfModule>
```

## ساختار پوشه‌ها

پس از دانلود، ساختار پوشه‌ها به این شکل خواهد بود:

```
public/
├── assets/
│   ├── css/
│   │   ├── bootstrap.min.css
│   │   ├── fontawesome.min.css
│   │   ├── jalalidatepicker.min.css
│   │   └── vazir-font.css
│   ├── js/
│   │   ├── bootstrap.bundle.min.js
│   │   ├── chart.min.js
│   │   ├── jquery.min.js
│   │   ├── jalalidatepicker.min.js
│   │   └── sweetalert2.min.js
│   └── fonts/
│       └── vazir/
│           ├── vazir-regular.woff2
│           ├── vazir-bold.woff2
│           └── ...
```

## مزایا

✅ **کار آفلاین**: سایت بدون اینترنت کاملاً کار می‌کند  
✅ **سرعت بالاتر**: بارگذاری سریع‌تر به دلیل عدم نیاز به CDN  
✅ **امنیت بیشتر**: کنترل کامل روی نسخه‌های کتابخانه‌ها  
✅ **پایداری**: وابستگی به سرویس‌های خارجی حذف می‌شود  

## عیب‌یابی

### اگر فونت‌ها کار نکردند:
1. بررسی کنید که فایل‌های فونت در مسیر درست قرار دارند
2. مسیرهای فونت در vazir-font.css را بررسی کنید

### اگر JavaScript کار نکرد:
1. کنسول مرورگر را برای خطاها بررسی کنید
2. ترتیب بارگذاری اسکریپت‌ها را بررسی کنید

### اگر CSS استایل‌ها اعمال نشدند:
1. مسیر فایل‌های CSS را بررسی کنید
2. کش مرورگر را پاک کنید

## به‌روزرسانی کتابخانه‌ها

برای به‌روزرسانی کتابخانه‌ها در آینده:

1. شماره نسخه‌ها را در `download-assets.php` به‌روز کنید
2. اسکریپت را دوباره اجرا کنید
3. `npm run build` را اجرا کنید

---

**نکته**: همیشه قبل از اعمال تغییرات در production، در محیط development تست کنید.
