# ==============================
# BASE IMAGE
# ==============================
FROM php:8.4-fpm-alpine

# ==============================
# SYSTEM PACKAGES (STABLE)
# ==============================
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    unzip \
    git \
    nodejs \
    npm \
    icu \
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
    libzip-dev

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
# NGINX & SUPERVISOR CONFIG
# ==============================
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# ==============================
# EXPOSE
# ==============================
EXPOSE 80

# ==============================
# START
# ==============================
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
