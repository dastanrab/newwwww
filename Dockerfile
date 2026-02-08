FROM php:8.3-fpm

# System deps
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev curl supervisor \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo pdo_mysql zip bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# فقط composer files اول (برای cache بهتر)
COPY composer.json composer.lock ./
COPY . .

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist

# حالا کل پروژه
COPY . .

# Publish اگر لازم بود
RUN php artisan storage:link || true \
    && php artisan optimize || true \
    && php artisan vendor:publish --all --force || true

# Permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Supervisor
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000

CMD ["/usr/bin/supervisord", "-n"]
