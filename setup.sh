#!/bin/bash

set -e

echo "=========================================="
echo "  YourMindAI - AWS EC2 Setup Script"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo -e "${GREEN}[1/10]${NC} Updating system packages..."
apt-get update -qq
apt-get upgrade -y -qq

echo -e "${GREEN}[2/10]${NC} Installing required packages..."
apt-get install -y -qq \
    curl \
    wget \
    git \
    unzip \
    software-properties-common \
    apt-transport-https \
    ca-certificates \
    gnupg \
    lsb-release

echo -e "${GREEN}[3/10]${NC} Installing Docker..."
if ! command -v docker &> /dev/null; then
    # Remove old versions
    apt-get remove -y docker docker-engine docker.io containerd runc 2>/dev/null || true
    
    # Install Docker
    curl -fsSL https://get.docker.com -o get-docker.sh
    sh get-docker.sh
    rm get-docker.sh
    
    # Start and enable Docker
    systemctl start docker
    systemctl enable docker
else
    echo "Docker is already installed"
fi

echo -e "${GREEN}[4/12]${NC} Installing Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE_VERSION=$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep 'tag_name' | cut -d\" -f4)
    curl -L "https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    ln -sf /usr/local/bin/docker-compose /usr/bin/docker-compose
else
    echo "Docker Compose is already installed"
fi

echo -e "${GREEN}[5/12]${NC} Installing Node.js and npm..."
if ! command -v node &> /dev/null; then
    curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
    apt-get install -y -qq nodejs
else
    echo "Node.js is already installed ($(node --version))"
fi

# Verify npm is available
if ! command -v npm &> /dev/null; then
    echo -e "${RED}Error: npm not found after Node.js installation${NC}"
    exit 1
fi

echo -e "${GREEN}[6/12]${NC} Configuring Git repository..."
if [ ! -d ".git" ]; then
    echo -e "${YELLOW}This directory is not a git repository.${NC}"
    read -p "Enter your git repository URL (or press Enter to skip): " GIT_REPO
    if [ ! -z "$GIT_REPO" ]; then
        git clone "$GIT_REPO" .
    else
        echo -e "${YELLOW}Git repository setup skipped. Make sure to initialize git or clone the repository manually.${NC}"
    fi
else
    echo "Git repository found. Pulling latest changes..."
    git pull origin main || git pull origin master || true
fi

echo -e "${GREEN}[7/12]${NC} Setting up environment file..."
if [ ! -f ".env" ]; then
    if [ -f ".env.production.example" ]; then
        cp .env.production.example .env
    elif [ -f ".env.example" ]; then
        cp .env.example .env
    else
        echo -e "${YELLOW}No .env.example found. Creating basic .env file...${NC}"
        cat > .env << EOF
APP_NAME=YourMindAI
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://yourmindaid.com

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=yourmindai
DB_USERNAME=yourmindai
DB_PASSWORD=yourmindai_password
DB_ROOT_PASSWORD=yourmindai_root_password

CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

JWT_SECRET=
JWT_TTL=604800

CORS_ORIGIN=https://yourmindaid.com

OPENAI_API_KEY=
OPENAI_MODEL=gpt-4o-mini
EOF
    fi
    
    # Generate APP_KEY if not set
    if grep -q "APP_KEY=$" .env || ! grep -q "APP_KEY=" .env; then
        echo "Generating APP_KEY..."
        # Use openssl if available, otherwise use docker
        if command -v openssl &> /dev/null; then
            APP_KEY="base64:$(openssl rand -base64 32)"
        else
            APP_KEY=$(docker run --rm php:8.2-cli php -r "echo 'base64:' . base64_encode(random_bytes(32));")
        fi
        if grep -q "APP_KEY=" .env; then
            sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" .env
        else
            echo "APP_KEY=$APP_KEY" >> .env
        fi
    fi
    
    # Generate JWT_SECRET if not set
    if grep -q "JWT_SECRET=$" .env || ! grep -q "JWT_SECRET=" .env; then
        echo "Generating JWT_SECRET..."
        JWT_SECRET=$(openssl rand -base64 64)
        if grep -q "JWT_SECRET=" .env; then
            sed -i "s|JWT_SECRET=.*|JWT_SECRET=$JWT_SECRET|" .env
        else
            echo "JWT_SECRET=$JWT_SECRET" >> .env
        fi
    fi
    
    # Generate WEBHOOK_SECRET if not set
    if ! grep -q "WEBHOOK_SECRET=" .env || grep -q "WEBHOOK_SECRET=$" .env; then
        echo "Generating WEBHOOK_SECRET..."
        WEBHOOK_SECRET=$(openssl rand -hex 32)
        if grep -q "WEBHOOK_SECRET=" .env; then
            sed -i "s|WEBHOOK_SECRET=.*|WEBHOOK_SECRET=$WEBHOOK_SECRET|" .env
        else
            echo "WEBHOOK_SECRET=$WEBHOOK_SECRET" >> .env
        fi
    fi
    
    echo -e "${YELLOW}Please edit .env file and set the following:${NC}"
    echo "  - OPENAI_API_KEY (required)"
    echo "  - DB_PASSWORD (change default password)"
    echo "  - DB_ROOT_PASSWORD (change default password)"
    echo ""
    read -p "Press Enter to continue after editing .env (or Ctrl+C to exit and edit now)..."
else
    echo ".env file already exists"
    # Ensure required secrets are set even if .env exists
    if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
        echo "Generating missing APP_KEY..."
        APP_KEY="base64:$(openssl rand -base64 32)"
        if grep -q "APP_KEY=" .env; then
            sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" .env
        else
            echo "APP_KEY=$APP_KEY" >> .env
        fi
    fi
    if ! grep -q "JWT_SECRET=" .env || grep -q "JWT_SECRET=$" .env; then
        echo "Generating missing JWT_SECRET..."
        JWT_SECRET=$(openssl rand -base64 64)
        if grep -q "JWT_SECRET=" .env; then
            sed -i "s|JWT_SECRET=.*|JWT_SECRET=$JWT_SECRET|" .env
        else
            echo "JWT_SECRET=$JWT_SECRET" >> .env
        fi
    fi
    if ! grep -q "WEBHOOK_SECRET=" .env || grep -q "WEBHOOK_SECRET=$" .env; then
        echo "Generating missing WEBHOOK_SECRET..."
        WEBHOOK_SECRET=$(openssl rand -hex 32)
        if grep -q "WEBHOOK_SECRET=" .env; then
            sed -i "s|WEBHOOK_SECRET=.*|WEBHOOK_SECRET=$WEBHOOK_SECRET|" .env
        else
            echo "WEBHOOK_SECRET=$WEBHOOK_SECRET" >> .env
        fi
    fi
fi

echo -e "${GREEN}[8/12]${NC} Installing Certbot for SSL..."
apt-get install -y -qq certbot python3-certbot-nginx

echo -e "${GREEN}[9/12]${NC} Setting up SSL certificates..."
if [ ! -d "/etc/letsencrypt/live/yourmindaid.com" ]; then
    echo -e "${YELLOW}SSL certificates will be obtained after DNS is configured.${NC}"
    echo -e "${YELLOW}Make sure yourmindaid.com points to this server's IP address.${NC}"
    echo ""
    read -p "Has DNS been configured? (y/n): " DNS_CONFIGURED
    if [ "$DNS_CONFIGURED" = "y" ] || [ "$DNS_CONFIGURED" = "Y" ]; then
        # Start nginx temporarily for certbot validation
        docker-compose up -d nginx 2>/dev/null || true
        sleep 5
        
        certbot certonly --webroot \
            --webroot-path=/var/www/certbot \
            --email admin@yourmindaid.com \
            --agree-tos \
            --no-eff-email \
            -d yourmindaid.com \
            -d www.yourmindaid.com \
            --non-interactive || echo -e "${YELLOW}SSL certificate generation failed. You can run this later.${NC}"
    else
        echo -e "${YELLOW}Skipping SSL setup. Run scripts/setup-ssl.sh after DNS is configured.${NC}"
    fi
else
    echo "SSL certificates already exist"
fi

echo -e "${GREEN}[10/12]${NC} Creating necessary directories and setting permissions..."
# Create necessary directories
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache
mkdir -p nginx/ssl

# Set initial permissions
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

echo -e "${GREEN}[11/12]${NC} Building and starting Docker containers..."
docker-compose down 2>/dev/null || true
docker-compose build --no-cache
docker-compose up -d

echo -e "${GREEN}[12/12]${NC} Setting up Laravel application..."
# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
MAX_WAIT=60
WAIT_COUNT=0
while ! docker-compose exec -T mysql mysqladmin ping -h localhost -u root -p${DB_ROOT_PASSWORD:-yourmindai_root_password} --silent 2>/dev/null; do
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        echo -e "${YELLOW}MySQL is taking longer than expected. Continuing anyway...${NC}"
        break
    fi
    echo "Waiting for MySQL... ($WAIT_COUNT/$MAX_WAIT seconds)"
    sleep 2
    WAIT_COUNT=$((WAIT_COUNT + 2))
done

# Install Composer dependencies (in case they weren't installed during build)
echo "Installing Composer dependencies..."
docker-compose exec -T php-fpm composer install --no-dev --optimize-autoloader --no-interaction || echo -e "${YELLOW}Composer install had issues. Continuing...${NC}"

# Install npm dependencies and build assets
echo "Installing npm dependencies and building assets..."
if [ -f "package.json" ]; then
    npm install --production || echo -e "${YELLOW}npm install had issues. Continuing...${NC}"
    npm run build || echo -e "${YELLOW}npm build had issues. Continuing...${NC}"
fi

# Generate application key if not set
echo "Ensuring application key is set..."
docker-compose exec -T php-fpm php artisan key:generate --force 2>/dev/null || echo "Application key already set"

# Run migrations and seeders
echo "Running database migrations..."
docker-compose exec -T php-fpm php artisan migrate --force || echo -e "${YELLOW}Migration failed. Check database connection.${NC}"

echo "Running database seeders..."
docker-compose exec -T php-fpm php artisan db:seed --force || echo -e "${YELLOW}Seeder failed. Check database connection.${NC}"

# Optimize Laravel
echo "Optimizing Laravel for production..."
docker-compose exec -T php-fpm php artisan config:cache || true
docker-compose exec -T php-fpm php artisan route:cache || true
docker-compose exec -T php-fpm php artisan view:cache || true

# Set permissions again after all operations
echo "Setting final permissions..."
docker-compose exec -T php-fpm chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache || true
docker-compose exec -T php-fpm chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache || true

echo ""
echo -e "${GREEN}[13/13]${NC} Setting up auto-deployment..."
# Make deploy script executable
chmod +x deploy.sh
chmod +x scripts/*.sh 2>/dev/null || true

# Setup webhook endpoint (primary method)
./scripts/setup-webhook.sh || echo -e "${YELLOW}Webhook setup skipped.${NC}"

# Setup optional cron job as backup (commented out by default since we use webhooks)
# Uncomment the line below if you want cron-based deployment as backup
# ./scripts/setup-cron.sh

echo ""
echo -e "${GREEN}[14/14]${NC} Configuring firewall..."
ufw --force enable || true
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force reload

echo ""
echo "=========================================="
echo -e "${GREEN}Setup Complete!${NC}"
echo "=========================================="
echo ""

# Health check
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
echo "Your application should be available at:"
echo "  - https://yourmindaid.com"
echo "  - https://yourmindaid.com/api"
echo ""
echo "Next steps:"
echo "  1. Verify DNS is pointing to this server"
echo "  2. If SSL wasn't set up, run: ./scripts/setup-ssl.sh"
echo "  3. Test the API: curl https://yourmindaid.com/api/health"
echo "  4. Configure GitHub/GitLab webhook: https://yourmindaid.com/api/webhook/deploy"
echo "     Webhook secret is in your .env file (WEBHOOK_SECRET)"
echo ""
echo "Useful commands:"
echo "  - View logs: docker-compose logs -f"
echo "  - View specific service: docker-compose logs -f php-fpm"
echo "  - Restart: docker-compose restart"
echo "  - Stop: docker-compose down"
echo "  - Deploy: ./deploy.sh"
echo "  - Check status: docker-compose ps"
echo ""
