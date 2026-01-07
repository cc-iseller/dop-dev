# Menggunakan PHP 8.4 FPM Alpine sesuai dengan log build Jenkins Anda
FROM php:8.4-fpm-alpine3.20

# 1. Install sistem dependensi menggunakan 'apk' (bukan apt-get)
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

# 2. Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    intl

# 3. Ambil Composer terbaru
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4. Set direktori kerja
WORKDIR /var/www/html

# 5. Copy seluruh file project ke dalam container
COPY . .

# 6. Jalankan Composer Install
# Ini akan memperbaiki error "Failed to open stream: No such file or directory"
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 7. Set permission agar Laravel bisa menulis ke folder storage dan cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# 8. Copy konfigurasi server (Nginx & Supervisor)
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# 9. Expose port 80
EXPOSE 80

# 10. Jalankan aplikasi menggunakan Supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]