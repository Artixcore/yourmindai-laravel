FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Production optimizations
RUN php artisan config:cache || true \
    && php artisan route:cache || true \
    && php artisan view:cache || true

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache \
    && chmod +x /var/www/html/start.sh || true

# Expose port (DigitalOcean will set PORT env var)
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=10s --retries=3 \
  CMD curl -f http://localhost:${PORT:-8000}/api/health || exit 1

# Start Laravel server using PORT env var (DigitalOcean sets this automatically)
# Option 1: Use startup script (validates env vars, generates APP_KEY if needed)
# CMD ["/var/www/html/start.sh"]

# Option 2: Direct command (simpler, faster startup)
CMD sh -c "php artisan serve --host=0.0.0.0 --port=${PORT:-8000}"
