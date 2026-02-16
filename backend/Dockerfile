FROM php:8.3-fpm

# System deps
RUN echo "===== Installing system dependencies =====" \
    && apt-get update && apt-get install -y \
        git unzip libzip-dev libpng-dev libonig-dev curl supervisor \
    && echo "===== Installing Redis PHP extension =====" \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && echo "===== Installing PHP extensions =====" \
    && docker-php-ext-install pdo pdo_mysql zip bcmath gd \
    && echo "===== Cleaning up =====" \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
RUN echo "===== Copying Composer binary ====="
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copy all files
RUN echo "===== Copying project files ====="
COPY . ./

# Composer install with output
RUN echo "===== Running composer install =====" \
    && composer install \
        --optimize-autoloader \
        --no-interaction \
        --prefer-dist \
        --verbose

# Publish / artisan commands with output
#RUN echo "===== Running Artisan commands =====" \
#    && php artisan storage:link || echo "storage:link failed" \
#    && php artisan optimize || echo "optimize failed" \
#    && php artisan vendor:publish --all || echo "vendor:publish failed" \
#    && php artisan scribe:generate || echo "scribe generate failed"

# Permissions
RUN echo "===== Setting permissions =====" \
    && chown -R www-data:www-data /var/www \
    && chmod -R 775 storage bootstrap/cache

# Supervisor
RUN echo "===== Copying Supervisor config ====="
COPY ../docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8000

COPY ../docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["entrypoint.sh"]

CMD echo "===== Starting Supervisor =====" && /usr/bin/supervisord -n
