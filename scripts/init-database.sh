#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Initializing database...${NC}"

# Get database credentials from environment or use defaults
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-yourmindai}
DB_USERNAME=${DB_USERNAME:-yourmindai}
DB_PASSWORD=${DB_PASSWORD:-yourmindai_password}
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD:-yourmindai_root_password}

# Wait for MySQL to be ready
echo -e "${YELLOW}Waiting for MySQL to be ready...${NC}"
MAX_WAIT=120
WAIT_COUNT=0

while ! mysqladmin ping -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_ROOT_PASSWORD" --silent 2>/dev/null; do
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        echo -e "${RED}MySQL is taking longer than expected. Exiting...${NC}"
        exit 1
    fi
    echo "Waiting for MySQL... ($WAIT_COUNT/$MAX_WAIT seconds)"
    sleep 2
    WAIT_COUNT=$((WAIT_COUNT + 2))
done

echo -e "${GREEN}MySQL is ready!${NC}"

# Create database if it doesn't exist
echo -e "${YELLOW}Ensuring database exists...${NC}"
mysql -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_ROOT_PASSWORD" -e "CREATE DATABASE IF NOT EXISTS \`$DB_DATABASE\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || {
    echo -e "${YELLOW}Database may already exist or user may not have CREATE privileges. Continuing...${NC}"
}

# Grant privileges (in case user doesn't have them)
echo -e "${YELLOW}Setting up database user permissions...${NC}"
mysql -h "$DB_HOST" -P "$DB_PORT" -u root -p"$DB_ROOT_PASSWORD" -e "GRANT ALL PRIVILEGES ON \`$DB_DATABASE\`.* TO '$DB_USERNAME'@'%'; FLUSH PRIVILEGES;" 2>/dev/null || {
    echo -e "${YELLOW}Could not grant privileges. User may already have them. Continuing...${NC}"
}

# Run migrations
echo -e "${YELLOW}Running database migrations...${NC}"
php artisan migrate --force || {
    echo -e "${RED}Migration failed!${NC}"
    exit 1
}

# Run seeders
echo -e "${YELLOW}Running database seeders...${NC}"
php artisan db:seed --force || {
    echo -e "${RED}Seeder failed!${NC}"
    exit 1
}

# Verify database setup
echo -e "${YELLOW}Verifying database setup...${NC}"
TABLE_COUNT=$(mysql -h "$DB_HOST" -P "$DB_PORT" -u "$DB_USERNAME" -p"$DB_PASSWORD" "$DB_DATABASE" -e "SHOW TABLES;" 2>/dev/null | wc -l)

if [ "$TABLE_COUNT" -gt 1 ]; then
    echo -e "${GREEN}Database initialized successfully! Found $((TABLE_COUNT - 1)) tables.${NC}"
else
    echo -e "${YELLOW}Warning: Database may not have been initialized correctly. Found $((TABLE_COUNT - 1)) tables.${NC}"
fi

echo -e "${GREEN}Database initialization complete!${NC}"
