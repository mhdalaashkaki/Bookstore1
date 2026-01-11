#!/usr/bin/env bash

# الخروج في حال حدوث أي خطأ
set -e

# التخزين المؤقت للإعدادات والراوتس لتحسين الأداء
echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# تشغيل التهجيرات (Migrations) تلقائياً عند كل رفع (اختياري لكن مستحسن)
# تأكد أن قاعدة البيانات فارغة في أول مرة أو أن التهجيرات آمنة للتشغيل المتكرر
echo "Running migrations..."
php artisan migrate --force

# تشغيل PHP-FPM في الخلفية
echo "Starting PHP-FPM..."
php-fpm -D

# تشغيل Nginx في الواجهة الأمامية
echo "Starting Nginx..."
nginx -g "daemon off;"