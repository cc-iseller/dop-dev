FROM php:8.4-fpm-alpine3.20

# Install dependensi sistem
# Tambahkan $PHPIZE_DEPS untuk kebutuhan kompilasi ekstensi PHP
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

# Install ekstensi PHP
# Kita jalankan configure dan install dalam satu layer
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    intl

# Copy Composer dari image resmi
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy file composer saja dulu (Optimasi Cache)
COPY composer.json composer.lock ./

# Install vendor
RUN composer install --no-interaction --optimize-autoloader --no-dev --no-scripts

# Copy sisa file project
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html

# Konfigurasi Nginx & Supervisor
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]