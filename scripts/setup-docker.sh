#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo "=========================================="
echo "  YourMindAI - Docker Setup"
echo "=========================================="
echo ""

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$SCRIPT_DIR"

# Check Docker installation
echo -e "${GREEN}[1/8]${NC} Checking Docker installation..."
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Docker is not installed. Please install Docker first.${NC}"
    echo "Run: bash scripts/setup-fresh-server.sh"
    exit 1
fi

if ! command -v docker-compose &> /dev/null; then
    echo -e "${RED}Docker Compose is not installed. Please install Docker Compose first.${NC}"
    echo "Run: bash scripts/setup-fresh-server.sh"
    exit 1
fi

echo -e "${GREEN}Docker and Docker Compose are installed${NC}"

# Check .env file
echo -e "${GREEN}[2/8]${NC} Checking environment configuration..."
if [ ! -f ".env" ]; then
    echo -e "${RED}.env file not found!${NC}"
    echo "Please create .env file from .env.example"
    exit 1
fi

# Source .env file for database credentials
source .env 2>/dev/null || true
DB_HOST=${DB_HOST:-mysql}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE:-yourmindai}
DB_USERNAME=${DB_USERNAME:-yourmindai}
DB_PASSWORD=${DB_PASSWORD:-yourmindai_password}
DB_ROOT_PASSWORD=${DB_ROOT_PASSWORD:-yourmindai_root_password}

# Create necessary directories
echo -e "${GREEN}[3/8]${NC} Creating necessary directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
mkdir -p nginx/ssl

# Set permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Build and start containers
echo -e "${GREEN}[4/8]${NC} Building and starting Docker containers..."
docker-compose down 2>/dev/null || true
docker-compose build --no-cache
docker-compose up -d

# Wait for services to be ready
echo -e "${GREEN}[5/8]${NC} Waiting for services to be ready..."
MAX_WAIT=120
WAIT_COUNT=0

while ! docker-compose exec -T mysql mysqladmin ping -h localhost -u root -p"$DB_ROOT_PASSWORD" --silent 2>/dev/null; do
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        echo -e "${YELLOW}MySQL is taking longer than expected. Continuing anyway...${NC}"
        break
    fi
    echo "Waiting for MySQL... ($WAIT_COUNT/$MAX_WAIT seconds)"
    sleep 2
    WAIT_COUNT=$((WAIT_COUNT + 2))
done

# Initialize database
echo -e "${GREEN}[6/8]${NC} Initializing database..."
docker-compose exec -T php-fpm bash scripts/init-database.sh || {
    echo -e "${YELLOW}Database initialization had issues. You may need to run it manually.${NC}"
}

# Set up Cloudflare Origin Certificates
echo -e "${GREEN}[7/8]${NC} Setting up SSL certificates..."
if [ -f "nginx/ssl/yourmindaid.com.pem" ] && [ -f "nginx/ssl/yourmindaid.com.key" ]; then
    echo -e "${GREEN}Cloudflare Origin certificates found!${NC}"
    chmod 600 nginx/ssl/yourmindaid.com.key 2>/dev/null || true
    chmod 644 nginx/ssl/yourmindaid.com.pem 2>/dev/null || true
    docker-compose restart nginx 2>/dev/null || true
else
    echo -e "${YELLOW}Cloudflare Origin certificates not found.${NC}"
    echo "To set up SSL:"
    echo "  1. Generate Origin Certificate in Cloudflare dashboard"
    echo "  2. Save certificate as: nginx/ssl/yourmindaid.com.pem"
    echo "  3. Save private key as: nginx/ssl/yourmindaid.com.key"
    echo "  4. Run: docker-compose restart nginx"
fi

# Optimize Laravel
echo -e "${GREEN}[8/8]${NC} Optimizing Laravel for production..."
docker-compose exec -T php-fpm php artisan config:cache 2>/dev/null || true
docker-compose exec -T php-fpm php artisan route:cache 2>/dev/null || true
docker-compose exec -T php-fpm php artisan view:cache 2>/dev/null || true

# Set final permissions
echo "Setting final permissions..."
docker-compose exec -T php-fpm chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true
docker-compose exec -T php-fpm chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>/dev/null || true

# Health check
echo ""
echo "Performing health check..."
sleep 5
HEALTH_CHECK=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/api/health 2>/dev/null || echo "000")
if [ "$HEALTH_CHECK" = "200" ]; then
    echo -e "${GREEN}✓ Health check passed!${NC}"
else
    echo -e "${YELLOW}⚠ Health check returned status: $HEALTH_CHECK${NC}"
    echo "  This is normal if the application is still starting up."
fi

echo ""
echo "=========================================="
echo -e "${GREEN}Docker Setup Complete!${NC}"
echo "=========================================="
echo ""
echo "Your application should be available at:"
echo "  - https://yourmindaid.com"
echo "  - https://yourmindaid.com/api"
echo ""
echo "Useful commands:"
echo "  - View logs: docker-compose logs -f"
echo "  - View specific service: docker-compose logs -f php-fpm"
echo "  - Restart: docker-compose restart"
echo "  - Stop: docker-compose down"
echo "  - Check status: docker-compose ps"
echo ""
