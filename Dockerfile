# ==============================
# BASE IMAGE (PINNED & STABLE)
# ==============================
FROM php:8.4-fpm-alpine3.20

# ==============================
# FIX DNS (CRITICAL FOR WINDOWS CI)
# ==============================
RUN echo "nameserver 8.8.8.8" > /etc/resolv.conf \
 && echo "nameserver 1.1.1.1" >> /etc/resolv.conf

# ==============================
# SYSTEM & BUILD DEPS
# ==============================
RUN apk add --no-cache \
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

# ==============================
# PHP EXTENSIONS
# ==============================
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    gd \
    intl

# ==============================
# COMPOSER
# ==============================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ==============================
# WORKDIR
# ==============================
WORKDIR /var/www/html

# ==============================
# APP SOURCE
# ==============================
COPY . .

RUN chown -R www-data:www-data /var/www/html

# ==============================
# NGINX & SUPERVISOR
# ==============================
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# ==============================
# PORT
# ==============================
EXPOSE 80

# ==============================
# START
# ==============================
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
