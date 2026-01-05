FROM php:8.4-fpm

# ==============================
# System dependencies
# ==============================
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    unzip \
    curl \
    nodejs \
    npm \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# ==============================
# Install Composer (manual, no extra image pull)
# ==============================
RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin \
    --filename=composer

WORKDIR /var/www/html

# ==============================
# Copy project
# ==============================
COPY . .

# ==============================
# Install PHP deps (NO git clone)
# ==============================
RUN composer install \
    --no-dev \
    --prefer-dist \
    --no-interaction \
    --optimize-autoloader

# ==============================
# Frontend build (Livewire / Filament)
# ==============================
RUN npm install
RUN npm run build

# ==============================
# Permissions
# ==============================
RUN chown -R www-data:www-data storage bootstrap/cache

# ==============================
# Nginx config
# ==============================
COPY docker/default.conf /etc/nginx/conf.d/default.conf
RUN rm -f /etc/nginx/sites-enabled/default

# ==============================
# Supervisor
# ==============================
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
