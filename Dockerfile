# ==============================
# BASE IMAGE
# ==============================
FROM php:8.4-fpm-alpine AS base

# ==============================
# ENABLE COMMUNITY & EDGE
# ==============================
RUN echo "https://dl-cdn.alpinelinux.org/alpine/edge/community" >> /etc/apk/repositories \
 && echo "https://dl-cdn.alpinelinux.org/alpine/edge/main" >> /etc/apk/repositories

# ==============================
# SYSTEM DEPENDENCIES
# ==============================
RUN apk update && apk add --no-cache \
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
    zlib

# ==============================
# BUILD DEPENDENCIES
# ==============================
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libzip-dev \
    zlib-dev \
    pkgconf

# ==============================
# PHP EXTENSIONS
# ==============================
RUN docker-php-ext-configure gd \
      --with-freetype \
      --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
      pdo_mysql \
      mbstring \
      zip \
      gd \
      intl

# ==============================
# CLEAN BUILD DEPS
# ==============================
RUN apk del .build-deps && rm -rf /var/cache/apk/*

# ==============================
# COMPOSER
# ==============================
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ==============================
# WORKDIR
# ==============================
WORKDIR /var/www/html

# ==============================
# COPY APP
# ==============================
COPY . .

# ==============================
# PERMISSIONS
# ==============================
RUN chown -R www-data:www-data /var/www/html

# ==============================
# NGINX + SUPERVISOR CONFIG
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
CMD ["/usr/bin/supervisord","-c","/etc/supervisord.conf"]
