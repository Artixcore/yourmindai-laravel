#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting Docker entrypoint...${NC}"

# Get database credentials from environment
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
REDIS_HOST=${REDIS_HOST:-redis}
REDIS_PORT=${REDIS_PORT:-6379}

# Wait for MySQL to be ready
echo -e "${YELLOW}Waiting for MySQL to be ready...${NC}"
MAX_WAIT=120
WAIT_COUNT=0

while ! mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u root -p"${DB_ROOT_PASSWORD:-yourmindai_root_password}" --silent 2>/dev/null; do
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        echo -e "${RED}MySQL is taking longer than expected. Continuing anyway...${NC}"
        break
    fi
    echo "Waiting for MySQL... ($WAIT_COUNT/$MAX_WAIT seconds)"
    sleep 2
    WAIT_COUNT=$((WAIT_COUNT + 2))
done

echo -e "${GREEN}MySQL is ready!${NC}"

# Wait for Redis to be ready
echo -e "${YELLOW}Waiting for Redis to be ready...${NC}"
MAX_WAIT=60
WAIT_COUNT=0

while ! redis-cli -h "$REDIS_HOST" -p "$REDIS_PORT" ping 2>/dev/null | grep -q PONG; do
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        echo -e "${RED}Redis is taking longer than expected. Continuing anyway...${NC}"
        break
    fi
    echo "Waiting for Redis... ($WAIT_COUNT/$MAX_WAIT seconds)"
    sleep 2
    WAIT_COUNT=$((WAIT_COUNT + 2))
done

echo -e "${GREEN}Redis is ready!${NC}"

# Set proper permissions
echo -e "${YELLOW}Setting permissions...${NC}"
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Generate application key if not set
if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "" ]; then
    echo -e "${YELLOW}Generating application key...${NC}"
    php artisan key:generate --force 2>/dev/null || echo "Application key may already be set"
fi

# Run database migrations if AUTO_MIGRATE is set to true
if [ "${AUTO_MIGRATE:-false}" = "true" ]; then
    echo -e "${YELLOW}Running database migrations...${NC}"
    php artisan migrate --force 2>/dev/null || {
        echo -e "${YELLOW}Migration failed or already run. Continuing...${NC}"
    }
fi

# Run database seeders if AUTO_SEED is set to true
if [ "${AUTO_SEED:-false}" = "true" ]; then
    echo -e "${YELLOW}Running database seeders...${NC}"
    php artisan db:seed --force 2>/dev/null || {
        echo -e "${YELLOW}Seeder failed or already run. Continuing...${NC}"
    }
fi

# Clear and cache Laravel config for production
if [ "${APP_ENV:-local}" = "production" ]; then
    echo -e "${YELLOW}Optimizing Laravel for production...${NC}"
    php artisan config:clear 2>/dev/null || true
    php artisan route:clear 2>/dev/null || true
    php artisan view:clear 2>/dev/null || true
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
fi

echo -e "${GREEN}Entrypoint setup complete!${NC}"

# Execute the main command (php-fpm)
exec "$@"
