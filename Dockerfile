# ==============================
# BASE IMAGE (PINNED)
# ==============================
FROM php:8.4-fpm-alpine3.20

# ==============================
# APK WITH EXPLICIT REPO (DNS SAFE)
# ==============================
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

COPY . .

RUN chown -R www-data:www-data /var/www/html

# ==============================
# NGINX & SUPERVISOR
# ==============================
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
