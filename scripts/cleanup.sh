#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$SCRIPT_DIR"

echo -e "${GREEN}Starting cleanup process...${NC}"

# Backup logs if BACKUP_LOGS is set
BACKUP_LOGS=${BACKUP_LOGS:-false}
BACKUP_DIR="$SCRIPT_DIR/storage/logs/backup_$(date +%Y%m%d_%H%M%S)"

if [ "$BACKUP_LOGS" = "true" ]; then
    echo -e "${YELLOW}Backing up logs...${NC}"
    mkdir -p "$BACKUP_DIR"
    cp -r storage/logs/*.log "$BACKUP_DIR/" 2>/dev/null || true
    echo -e "${GREEN}Logs backed up to $BACKUP_DIR${NC}"
fi

# Remove development files
echo -e "${YELLOW}Removing development files...${NC}"
rm -rf tests/ .phpunit.cache .editorconfig .gitattributes .vscode/ .idea/ *.swp *.swo 2>/dev/null || true

# Remove node_modules (will be reinstalled if needed)
if [ "${CLEAN_NODE_MODULES:-false}" = "true" ]; then
    echo -e "${YELLOW}Removing node_modules...${NC}"
    rm -rf node_modules/ 2>/dev/null || true
fi

# Clear Laravel caches
echo -e "${YELLOW}Clearing Laravel caches...${NC}"
php artisan config:clear 2>/dev/null || true
php artisan route:clear 2>/dev/null || true
php artisan view:clear 2>/dev/null || true
php artisan cache:clear 2>/dev/null || true
php artisan event:clear 2>/dev/null || true

# Remove cache files
echo -e "${YELLOW}Removing cache files...${NC}"
rm -rf storage/framework/cache/* 2>/dev/null || true
rm -rf storage/framework/sessions/* 2>/dev/null || true
rm -rf storage/framework/views/* 2>/dev/null || true
rm -rf bootstrap/cache/*.php 2>/dev/null || true

# Clean log files (optional)
if [ "${CLEAN_LOGS:-false}" = "true" ]; then
    echo -e "${YELLOW}Cleaning log files...${NC}"
    find storage/logs -name "*.log" -type f -mtime +7 -delete 2>/dev/null || true
    find storage/logs -name "laravel-*.log" -type f -size +100M -delete 2>/dev/null || true
fi

# Remove temporary files
echo -e "${YELLOW}Removing temporary files...${NC}"
rm -rf /tmp/laravel-* 2>/dev/null || true
rm -rf /var/tmp/laravel-* 2>/dev/null || true
find . -name "*.tmp" -type f -delete 2>/dev/null || true
find . -name ".DS_Store" -type f -delete 2>/dev/null || true

# Clean Composer cache (optional)
if [ "${CLEAN_COMPOSER_CACHE:-false}" = "true" ]; then
    echo -e "${YELLOW}Cleaning Composer cache...${NC}"
    composer clear-cache 2>/dev/null || true
    rm -rf ~/.composer/cache/* 2>/dev/null || true
fi

# Clean npm cache (optional)
if [ "${CLEAN_NPM_CACHE:-false}" = "true" ]; then
    echo -e "${YELLOW}Cleaning npm cache...${NC}"
    npm cache clean --force 2>/dev/null || true
fi

# Clean Docker build cache (optional, requires docker command)
if [ "${CLEAN_DOCKER_CACHE:-false}" = "true" ] && command -v docker &> /dev/null; then
    echo -e "${YELLOW}Cleaning Docker build cache...${NC}"
    docker builder prune -f 2>/dev/null || true
fi

# Remove .git directory in production (optional)
if [ "${CLEAN_GIT:-false}" = "true" ] && [ "${APP_ENV:-local}" = "production" ]; then
    echo -e "${YELLOW}Removing .git directory (production mode)...${NC}"
    rm -rf .git/ 2>/dev/null || true
fi

# Remove documentation files (optional)
if [ "${CLEAN_DOCS:-false}" = "true" ]; then
    echo -e "${YELLOW}Removing documentation files...${NC}"
    find . -name "*.md" -not -path "./vendor/*" -not -name "README.md" -delete 2>/dev/null || true
fi

# Optimize Laravel for production
if [ "${APP_ENV:-local}" = "production" ]; then
    echo -e "${YELLOW}Optimizing Laravel for production...${NC}"
    php artisan config:cache 2>/dev/null || true
    php artisan route:cache 2>/dev/null || true
    php artisan view:cache 2>/dev/null || true
    php artisan event:cache 2>/dev/null || true
fi

# Calculate space saved
echo -e "${GREEN}Cleanup complete!${NC}"

# Show disk usage
echo -e "${YELLOW}Current disk usage:${NC}"
du -sh . 2>/dev/null || true
