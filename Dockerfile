# Gunakan image PHP 8.4 Fpm Alpine sesuai log Jenkins Anda
FROM php:8.4-fpm-alpine3.20

# Install dependensi sistem yang dibutuhkan
RUN apk update --repository=https://dl-cdn.alpinelinux.org/alpine/v3.20/main \
    && apk update --repository=https://dl-cdn.alpinelinux.org/alpine/v3.20/community \
    && apk add --no-cache \
    nginx \
    supervisor \
    curl \
    unzip \
    git \
    nodejs \
    npm \
    icu-libs \
    libpng \
    libjpeg-turbo \
    freetype \
    oniguruma \
    libzip \
    zlib \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    build-base

# Install ekstensi PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    intl

# Copy Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# --- BAGIAN KRUSIAL UNTUK FIX ERROR VENDOR ---

# 1. Copy file composer terlebih dahulu agar caching efisien
COPY composer.json composer.lock ./

# 2. Jalankan composer install (Akan mendownload folder vendor)
# Kita tambahkan --no-scripts untuk menghindari error jika ada script yang butuh database saat build
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# 3. Copy seluruh kode aplikasi ke dalam container
COPY . .

# 4. Set permissions agar web server bisa membaca file
RUN chown -R www-data:www-data /var/www/html

# ---------------------------------------------

# Copy konfigurasi Nginx dan Supervisor sesuai path di log Jenkins Anda
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Expose port 80
EXPOSE 80

# Jalankan Supervisor untuk mengelola Nginx dan PHP-FPM secara bersamaan
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]