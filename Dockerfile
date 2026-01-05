# Gunakan tag yang spesifik untuk stabilitas
FROM php:8.4-fpm-alpine AS base

# ==============================
# System dependencies (Alpine menggunakan apk)
# ==============================
RUN apk add --no-cache \
    nginx \
    supervisor \
    unzip \
    curl \
    nodejs \
    npm \
    libpng-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring zip gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# ==============================
# Optimasi: Install PHP deps dulu (Layer Caching)
# ==============================
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-autoloader --no-scripts

# ==============================
# Optimasi: Install Node deps dulu
# ==============================
COPY package.json package-lock.json ./
RUN npm install

# ==============================
# Copy seluruh project & Build
# ==============================
COPY . .
RUN composer dump-cache --optimize
RUN npm run build

# ==============================
# Permissions & Config
# ==============================
RUN chown -R www-data:www-data storage bootstrap/cache

COPY docker/default.conf /etc/nginx/http.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]