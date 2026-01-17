#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

LOG_FILE="$SCRIPT_DIR/storage/logs/deploy.log"
mkdir -p "$(dirname "$LOG_FILE")"

log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log "=========================================="
log "Starting deployment..."
log "=========================================="

# Check if .env exists
if [ ! -f ".env" ]; then
    log -e "${RED}Error: .env file not found!${NC}"
    exit 1
fi

# Pull latest changes
log "Pulling latest changes from git..."
git fetch origin
CURRENT_BRANCH=$(git branch --show-current)
git pull origin "$CURRENT_BRANCH" || {
    log -e "${YELLOW}Git pull failed or no changes detected.${NC}"
}

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    log -e "${RED}Error: docker-compose not found!${NC}"
    exit 1
fi

# Rebuild containers if Dockerfile or docker-compose.yml changed
if git diff --name-only HEAD@{1} HEAD | grep -qE "(Dockerfile|docker-compose.yml)"; then
    log "Docker configuration changed. Rebuilding containers..."
    docker-compose build --no-cache
fi

# Restart containers
log "Restarting containers..."
docker-compose up -d

# Wait for services to be ready
log "Waiting for services to be ready..."
sleep 5

# Run migrations
log "Running database migrations..."
docker-compose exec -T php-fpm php artisan migrate --force || {
    log -e "${YELLOW}Migration failed or no new migrations.${NC}"
}

# Clear and cache configs
log "Optimizing Laravel..."
docker-compose exec -T php-fpm php artisan config:clear || true
docker-compose exec -T php-fpm php artisan route:clear || true
docker-compose exec -T php-fpm php artisan view:clear || true
docker-compose exec -T php-fpm php artisan config:cache
docker-compose exec -T php-fpm php artisan route:cache
docker-compose exec -T php-fpm php artisan view:cache

# Set permissions
log "Setting permissions..."
docker-compose exec -T php-fpm chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
docker-compose exec -T php-fpm chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

# Health check
log "Performing health check..."
sleep 3
HEALTH_CHECK=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/api/health || echo "000")
if [ "$HEALTH_CHECK" = "200" ]; then
    log -e "${GREEN}Health check passed!${NC}"
else
    log -e "${YELLOW}Health check returned status: $HEALTH_CHECK${NC}"
fi

log "=========================================="
log -e "${GREEN}Deployment completed!${NC}"
log "=========================================="
