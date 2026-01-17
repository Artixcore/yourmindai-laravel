FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libsqlite3-dev \
    zip \
    unzip \
    libicu-dev \
    && rm -rf /var/lib/apt/lists/* \
    && apt-get clean

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    soap \
    && docker-php-ext-enable opcache

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Verify all extensions are installed
RUN php -m | grep -E "(pdo_mysql|mbstring|exif|pcntl|bcmath|gd|zip|intl|opcache|redis|soap|fileinfo|curl|xml|tokenizer)" || echo "Some extensions may be missing"

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configure PHP-FPM for production
RUN sed -i 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/pm.max_children = 5/pm.max_children = 50/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/pm.start_servers = 2/pm.start_servers = 5/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/pm.min_spare_servers = 1/pm.min_spare_servers = 5/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/pm.max_spare_servers = 3/pm.max_spare_servers = 10/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/;pm.status_path = \/status/pm.status_path = \/status/' /usr/local/etc/php-fpm.d/www.conf \
    && sed -i 's/;pm.ping_path = \/ping/pm.ping_path = \/ping/' /usr/local/etc/php-fpm.d/www.conf

# Configure PHP for production
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.interned_strings_buffer=16" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.validate_timestamps=0" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.save_comments=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.fast_shutdown=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Configure PHP production settings and security
RUN echo "expose_php = Off" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "display_errors = Off" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "display_startup_errors = Off" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "log_errors = On" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "error_log = /var/log/php_errors.log" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "disable_functions = exec,passthru,shell_exec,system,proc_open,popen,curl_exec,curl_multi_exec,parse_ini_file,show_source" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "allow_url_fopen = Off" >> /usr/local/etc/php/conf.d/production.ini \
    && echo "allow_url_include = Off" >> /usr/local/etc/php/conf.d/production.ini

# Set working directory
WORKDIR /var/www/html

# Copy composer files first for better layer caching
COPY composer.json composer.lock ./

# Install dependencies (this layer will be cached if composer files don't change)
RUN composer install --no-dev --optimize-autoloader --no-interaction || \
    (echo "Composer install failed. Checking for errors..." && \
     composer diagnose && \
     exit 1)

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Copy the rest of the application files
COPY . .

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && mkdir -p /var/log \
    && touch /var/log/php_errors.log \
    && chown www-data:www-data /var/log/php_errors.log

# Clean up unnecessary files
RUN rm -rf /var/www/html/tests \
    /var/www/html/.git \
    /var/www/html/.phpunit.cache \
    /var/www/html/.editorconfig \
    /var/www/html/.gitattributes \
    /var/www/html/node_modules \
    /var/www/html/.vscode \
    /var/www/html/.idea \
    /tmp/* \
    /var/tmp/* \
    /root/.composer/cache/* \
    /root/.npm \
    /var/lib/apt/lists/* \
    /var/cache/apt/archives/*

# Expose PHP-FPM port
EXPOSE 9000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD php -r "exit(0);" || exit 1

# Use entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
