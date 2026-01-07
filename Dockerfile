# Menggunakan PHP 8.4 fpm alpine sesuai dengan log build Anda
FROM php:8.4-fpm-alpine3.20

# Install dependensi sistem dan build tools yang dibutuhkan untuk ekstensi PHP
RUN apk update && apk add --no-cache \
    $PHPIZE_DEPS \
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

# Install ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    intl

# Copy Composer dari image resmi terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory ke standar web server
WORKDIR /var/www/html

# --- OPTIMASI CACHE COMPOSER ---
# Copy file composer terlebih dahulu agar tidak download ulang jika kode berubah
COPY composer.json composer.lock ./

# Install dependensi vendor (Mengatasi error: Failed to open stream)
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# --- COPY SOURCE CODE ---
# Copy seluruh file project ke dalam container
COPY . .

# --- FIX CACHE PATH ERROR ---
# Membuat struktur folder storage yang dibutuhkan Laravel (Mengatasi InvalidArgumentException)
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache

# Set permissions agar user www-data bisa menulis ke folder storage
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Copy konfigurasi Nginx dan Supervisor sesuai path Jenkins Anda
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Expose port 80 untuk Azure App Service
EXPOSE 80

# Jalankan Supervisor untuk mengelola Nginx dan PHP-FPM
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]