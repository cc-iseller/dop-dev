FROM php:8.4-fpm

#kontol
# System deps
RUN apt-get update && apt-get install -y \
    nginx \
    supervisor \
    git \
    unzip \
    curl \
    nodejs \
    npm \
    libpng-dev \
    libzip-dev \
    libonig-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

# PHP deps
RUN composer install --no-dev --optimize-autoloader

# Frontend
RUN npm install
RUN npm run build

# Permission
RUN chown -R www-data:www-data storage bootstrap/cache

# Nginx
COPY docker/default.conf /etc/nginx/conf.d/default.conf
RUN rm /etc/nginx/sites-enabled/default

# Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 80

CMD ["/usr/bin/supervisord"]
