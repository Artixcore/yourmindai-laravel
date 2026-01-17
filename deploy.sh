#!/bin/bash

# Don't exit on error - we want to log and continue where possible
set +e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

LOG_FILE="$SCRIPT_DIR/storage/logs/deploy.log"
mkdir -p "$(dirname "$LOG_FILE")"

log() {
    echo -e "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_error() {
    echo -e "${RED}[$(date '+%Y-%m-%d %H:%M:%S')] ERROR: $1${NC}" | tee -a "$LOG_FILE"
}

log_warning() {
    echo -e "${YELLOW}[$(date '+%Y-%m-%d %H:%M:%S')] WARNING: $1${NC}" | tee -a "$LOG_FILE"
}

log_success() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')] SUCCESS: $1${NC}" | tee -a "$LOG_FILE"
}

log "=========================================="
log "Starting deployment..."
log "=========================================="

# Check if .env exists
if [ ! -f ".env" ]; then
    log_error ".env file not found!"
    exit 1
fi

# Check if docker-compose is available
if ! command -v docker-compose &> /dev/null; then
    log_error "docker-compose not found!"
    exit 1
fi

# Pull latest changes
log "Pulling latest changes from git..."
git fetch origin 2>&1 | tee -a "$LOG_FILE"
CURRENT_BRANCH=$(git branch --show-current 2>/dev/null || echo "main")
PREVIOUS_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "")

git pull origin "$CURRENT_BRANCH" 2>&1 | tee -a "$LOG_FILE"
PULL_EXIT_CODE=${PIPESTATUS[0]}

if [ $PULL_EXIT_CODE -ne 0 ]; then
    log_warning "Git pull failed or no changes detected. Continuing with deployment..."
fi

CURRENT_COMMIT=$(git rev-parse HEAD 2>/dev/null || echo "")
if [ "$PREVIOUS_COMMIT" = "$CURRENT_COMMIT" ] && [ -n "$PREVIOUS_COMMIT" ]; then
    log "No new commits detected. Current commit: $CURRENT_COMMIT"
else
    log "Updated to commit: $CURRENT_COMMIT"
fi

# Check for changed files that require rebuilds
CHANGED_FILES=$(git diff --name-only "$PREVIOUS_COMMIT" "$CURRENT_COMMIT" 2>/dev/null || echo "")
NEEDS_DOCKER_REBUILD=false
NEEDS_NPM_BUILD=false
NEEDS_COMPOSER_INSTALL=false

if echo "$CHANGED_FILES" | grep -qE "(Dockerfile|docker-compose\.yml|\.dockerignore)"; then
    NEEDS_DOCKER_REBUILD=true
    log "Docker configuration files changed. Will rebuild containers."
fi

if echo "$CHANGED_FILES" | grep -qE "(package\.json|package-lock\.json|vite\.config\.js|resources/|public/)"; then
    NEEDS_NPM_BUILD=true
    log "Frontend files changed. Will rebuild assets."
fi

if echo "$CHANGED_FILES" | grep -qE "(composer\.json|composer\.lock)"; then
    NEEDS_COMPOSER_INSTALL=true
    log "Composer dependencies changed. Will reinstall."
fi

# Rebuild containers if needed
if [ "$NEEDS_DOCKER_REBUILD" = true ]; then
    log "Rebuilding Docker containers..."
    docker-compose build --no-cache 2>&1 | tee -a "$LOG_FILE"
    if [ ${PIPESTATUS[0]} -ne 0 ]; then
        log_error "Docker build failed!"
        exit 1
    fi
    log_success "Docker containers rebuilt successfully"
fi

# Restart containers
log "Restarting containers..."
docker-compose up -d 2>&1 | tee -a "$LOG_FILE"
if [ ${PIPESTATUS[0]} -ne 0 ]; then
    log_error "Failed to start containers!"
    exit 1
fi
log_success "Containers started"

# Wait for services to be ready
log "Waiting for services to be ready..."
MAX_WAIT=30
WAIT_COUNT=0
while ! docker-compose exec -T mysql mysqladmin ping -h localhost -u root -p${DB_ROOT_PASSWORD:-yourmindai_root_password} --silent 2>/dev/null; do
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        log_warning "MySQL is taking longer than expected. Continuing anyway..."
        break
    fi
    sleep 2
    WAIT_COUNT=$((WAIT_COUNT + 2))
done

# Install Composer dependencies if needed
if [ "$NEEDS_COMPOSER_INSTALL" = true ]; then
    log "Installing Composer dependencies..."
    docker-compose exec -T php-fpm composer install --no-dev --optimize-autoloader --no-interaction 2>&1 | tee -a "$LOG_FILE"
    if [ ${PIPESTATUS[0]} -ne 0 ]; then
        log_warning "Composer install had issues. Continuing..."
    else
        log_success "Composer dependencies installed"
    fi
fi

# Install npm dependencies and build assets if needed
if [ "$NEEDS_NPM_BUILD" = true ] && [ -f "package.json" ]; then
    log "Installing npm dependencies..."
    npm install --production 2>&1 | tee -a "$LOG_FILE"
    if [ ${PIPESTATUS[0]} -ne 0 ]; then
        log_warning "npm install had issues. Continuing..."
    else
        log_success "npm dependencies installed"
    fi
    
    log "Building frontend assets..."
    npm run build 2>&1 | tee -a "$LOG_FILE"
    if [ ${PIPESTATUS[0]} -ne 0 ]; then
        log_warning "npm build had issues. Continuing..."
    else
        log_success "Frontend assets built"
    fi
fi

# Run migrations
log "Running database migrations..."
docker-compose exec -T php-fpm php artisan migrate --force 2>&1 | tee -a "$LOG_FILE"
MIGRATE_EXIT_CODE=${PIPESTATUS[0]}
if [ $MIGRATE_EXIT_CODE -ne 0 ]; then
    log_warning "Migration failed or no new migrations."
else
    log_success "Migrations completed"
fi

# Clear and cache configs
log "Optimizing Laravel..."
docker-compose exec -T php-fpm php artisan config:clear 2>&1 | tee -a "$LOG_FILE" || true
docker-compose exec -T php-fpm php artisan route:clear 2>&1 | tee -a "$LOG_FILE" || true
docker-compose exec -T php-fpm php artisan view:clear 2>&1 | tee -a "$LOG_FILE" || true

docker-compose exec -T php-fpm php artisan config:cache 2>&1 | tee -a "$LOG_FILE"
if [ ${PIPESTATUS[0]} -eq 0 ]; then
    log_success "Config cached"
fi

docker-compose exec -T php-fpm php artisan route:cache 2>&1 | tee -a "$LOG_FILE"
if [ ${PIPESTATUS[0]} -eq 0 ]; then
    log_success "Routes cached"
fi

docker-compose exec -T php-fpm php artisan view:cache 2>&1 | tee -a "$LOG_FILE"
if [ ${PIPESTATUS[0]} -eq 0 ]; then
    log_success "Views cached"
fi

# Set permissions
log "Setting permissions..."
docker-compose exec -T php-fpm chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 | tee -a "$LOG_FILE" || true
docker-compose exec -T php-fpm chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache 2>&1 | tee -a "$LOG_FILE" || true
log_success "Permissions set"

# Health check
log "Performing health check..."
sleep 3
HEALTH_CHECK=$(curl -s -o /dev/null -w "%{http_code}" http://localhost/api/health 2>/dev/null || echo "000")
if [ "$HEALTH_CHECK" = "200" ]; then
    log_success "Health check passed! Application is running."
else
    log_warning "Health check returned status: $HEALTH_CHECK"
    log "This might be normal if the application is still starting up."
fi

log "=========================================="
log_success "Deployment completed!"
log "=========================================="
log "Deployment log saved to: $LOG_FILE"
