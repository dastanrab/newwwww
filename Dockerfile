FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev curl supervisor \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && docker-php-ext-install pdo pdo_mysql zip bcmath gd \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# فقط فایل‌های composer + app رو کپی می‌کنیم برای build vendor
COPY composer.json composer.lock ./
COPY artisan ./
COPY app ./app
COPY bootstrap ./bootstrap

# composer install
RUN composer install --no-dev --optimize-autoloader \
    && chown -R www-data:www-data /var/www

# حالا پروژه کامل رو کپی می‌کنیم بدون overwrite vendor
COPY . ./


EXPOSE 9000

# Supervisor برای queue
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

CMD ["/usr/bin/supervisord", "-n"]
