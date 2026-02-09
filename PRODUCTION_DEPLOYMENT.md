# 🚀 Production Deployment Guide - Optimized Version

## 📋 پیش‌نیازهای پروداکشن

### 1. تنظیمات محیط (Environment)
```env
# Production Environment
APP_ENV=production
APP_DEBUG=false

# Cache Configuration (بسیار مهم)
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=your_db_host
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Performance Settings
UNITS_CACHE_TTL=60
RESIDENT_CACHE_TTL=30
NOTES_CACHE_TTL=15
USER_CACHE_TTL=45

# Livewire Optimization
LIVEWIRE_LAZY_LOADING=true
LIVEWIRE_DEBOUNCE_DELAY=300
LIVEWIRE_RENDER_ON_REQUEST=true

# Frontend Optimization
CLIENT_SIDE_CACHE=true
CLIENT_CACHE_TTL=300
DEBOUNCE_INLINE_EDIT=500
DEBOUNCE_PHONE_FORMAT=150
LAZY_LOAD_MODALS=true

# Security
RATE_LIMITING_ENABLED=true
RATE_LIMIT_REQUESTS=60
RATE_LIMIT_BURST=10

# Optimization
AUTO_OPTIMIZE=true
ENABLE_COMPRESSION=true
HTTP_CACHE_ENABLED=true
HTTP_CACHE_MAX_AGE=3600

# Monitoring
PERFORMANCE_MONITORING=true
LOG_SLOW_QUERIES=true
SLOW_QUERY_THRESHOLD=100
```

### 2. نصب و راه‌اندازی Redis
```bash
# Ubuntu/Debian
sudo apt-get update
sudo apt-get install redis-server
sudo systemctl start redis
sudo systemctl enable redis

# CentOS/RHEL
sudo yum install redis
sudo systemctl start redis
sudo systemctl enable redis

# تست Redis
redis-cli ping
# باید پاسخ "PONG" بدهد
```

### 3. تنظیمات PHP برای پروداکشن
```ini
# php.ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=10000
opcache.revalidate_freq=0
opcache.validate_timestamps=0
opcache.save_comments=1
opcache.load_comments=1
opcache.enable_file_override=1

# Memory و Execution Time
memory_limit=512M
max_execution_time=300
max_input_time=300

# Post Size
post_max_size=50M
upload_max_filesize=50M
```

### 4. تنظیمات وب سرور

#### Apache (.htaccess)
```apache
# Enable compression
<IfModule mod_deflate.c>
    AddOutputFilterByType DEFLATE text/plain
    AddOutputFilterByType DEFLATE text/html
    AddOutputFilterByType DEFLATE text/xml
    AddOutputFilterByType DEFLATE text/css
    AddOutputFilterByType DEFLATE application/xml
    AddOutputFilterByType DEFLATE application/xhtml+xml
    AddOutputFilterByType DEFLATE application/rss+xml
    AddOutputFilterByType DEFLATE application/javascript
    AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>

# Enable caching
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
    ExpiresByType image/png "access plus 1 month"
    ExpiresByType image/jpg "access plus 1 month"
    ExpiresByType image/jpeg "access plus 1 month"
    ExpiresByType image/gif "access plus 1 month"
    ExpiresByType image/ico "access plus 1 month"
    ExpiresByType image/icon "access plus 1 month"
</IfModule>
```

#### Nginx
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/your/project/public;
    index index.php index.html;

    # Enable gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Cache static files
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # PHP handling
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Laravel pretty URLs
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## 🚀 مراحل دیپلوی

### 1. آماده‌سازی پروژه
```bash
# Pull latest code
git pull origin main

# Install dependencies (no-dev)
composer install --optimize-autoloader --no-dev

# Install frontend dependencies
npm install
npm run production

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Create optimized caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

# Link storage
php artisan storage:link
```

### 2. تنظیم مجوزها
```bash
# Storage and cache directories
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Ownership (مهم برای سرور)
sudo chown -R www-data:www-data storage
sudo chown -R www-data:www-data bootstrap/cache
```

### 3. بهینه‌سازی دیتابیس
```bash
# Run the performance migration
php artisan migrate

# Optimize tables (MySQL)
mysql -u username -p database_name -e "OPTIMIZE TABLE units, rooms, beds, contracts, residents, notes;"

# Check indexes
mysql -u username -p database_name -e "SHOW INDEX FROM contracts;"
```

## 📊 مانیتورینگ و دیباگینگ

### 1. بررسی عملکرد
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check Redis status
redis-cli info memory

# Check system resources
top
htop
df -h
```

### 2. تست سرعت
```bash
# curl test
curl -w "@curl-format.txt" -o /dev/null -s "http://your-domain.com/tablelists"

# curl-format.txt content:
#      time_namelookup:  %{time_namelookup}\n
#         time_connect:  %{time_connect}\n
#      time_appconnect:  %{time_appconnect}\n
#     time_pretransfer:  %{time_pretransfer}\n
#        time_redirect:  %{time_redirect}\n
#   time_starttransfer:  %{time_starttransfer}\n
#                      ----------\n
#           time_total:  %{time_total}\n
```

### 3. بررسی هدرهای عملکرد
در browser developer tools، به تب Network بروید و هدرهای زیر را چک کنید:
- `X-Execution-Time`: زمان اجرای درخواست
- `X-Memory-Usage`: مصرف حافظه
- `X-Query-Count`: تعداد کوئری‌های دیتابیس

## 🔧 عیب‌یابی مشکلات رایج

### 1. صفحه همچنان کند است
```bash
# Check Redis connection
redis-cli ping

# Check cache driver
php artisan tinker
>>> echo config('cache.default');

# Clear Redis cache
redis-cli flushall

# Restart services
sudo systemctl restart nginx
sudo systemctl restart php8.1-fpm
sudo systemctl restart redis
```

### 2. مدال‌ها کند باز می‌شوند
- بررسی کنید که JavaScript optimizer در حال بارگذاری باشد
- کنسول browser را برای خطاهای JavaScript چک کنید
- مطمئن شوید که Livewire به درستی کار می‌کند

### 3. ویرایش‌های آنلاین کند هستند
- تنظیم `DEBOUNCE_INLINE_EDIT` را کاهش دهید
- بررسی کنید که transaction‌ها به درستی کار می‌کنند
- مانیتورینگ performance را فعال کنید

## 📈 انتظار عملکرد

با این بهینه‌سازی‌ها، باید مشاهده کنید:
- **زمان بارگذاری صفحه**: زیر 2 ثانیه
- **زمان باز شدن مدال**: زیر 500 میلی‌ثانیه
- **زمان ذخیره ویرایش آنلاین**: زیر 300 میلی‌ثانیه
- **مصرف حافظه**: زیر 64MB برای صفحه اصلی
- **تعداد کوئری‌ها**: زیر 20 کوئری برای صفحه اصلی

## 🔄 نگهداری

### هفتگی:
```bash
# Clear old cache entries
php artisan cache:clear

# Optimize database
php artisan db:show

# Check logs for errors
grep -i error storage/logs/laravel.log
```

### ماهانه:
```bash
# Update dependencies
composer update
npm update

# Backup database
mysqldump -u username -p database_name > backup.sql

# Check performance metrics
php artisan about
```

## 🆘 پشتیبانی

اگر پس از این بهینه‌سازی‌ها باز هم مشکلی داشتید:
1. لاگ‌های Laravel را چک کنید
2. هدرهای عملکرد را بررسی کنید
3. تنظیمات Redis را بررسی کنید
4. از مانیتورینگ performance استفاده کنید

این راهنما باید مشکل کندی عملکرد روی هاست را به طور کامل حل کند. 🎯
