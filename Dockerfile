# استخدام صورة PHP 8.2 مع FPM
FROM php:8.2-fpm

# تثبيت متطلبات النظام الضرورية لـ Laravel و PostgreSQL
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libpq-dev \
    nginx

# تنظيف الكاش لتقليل حجم الصورة
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# تثبيت إضافات PHP المطلوبة (خاصة pdo_pgsql لـ Neon)
RUN docker-php-ext-install pdo_pgsql mbstring exif pcntl bcmath gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# إعداد مجلد العمل
WORKDIR /var/www

# نسخ ملفات المشروع
COPY . /var/www

# تثبيت حزم Composer (بدون الحزم التطويرية)
RUN composer install --optimize-autoloader --no-dev

# إعداد صلاحيات المجلدات
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

# نسخ إعدادات Nginx (سننشئه في الخطوة التالية)
COPY nginx.conf /etc/nginx/sites-available/default

# نسخ سكربت التشغيل (سننشئه لاحقاً)
COPY start.sh /start.sh
RUN chmod +x /start.sh

# المنفذ الذي سيعمل عليه التطبيق
EXPOSE 8080

# أمر التشغيل عند بدء الحاوية
CMD ["/start.sh"]