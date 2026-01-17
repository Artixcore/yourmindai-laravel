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

echo -e "${GREEN}[4/10]${NC} Installing Docker Compose..."
if ! command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE_VERSION=$(curl -s https://api.github.com/repos/docker/compose/releases/latest | grep 'tag_name' | cut -d\" -f4)
    curl -L "https://github.com/docker/compose/releases/download/${DOCKER_COMPOSE_VERSION}/docker-compose-$(uname -s)-$(uname -m)" -o /usr/local/bin/docker-compose
    chmod +x /usr/local/bin/docker-compose
    ln -sf /usr/local/bin/docker-compose /usr/bin/docker-compose
else
    echo "Docker Compose is already installed"
fi

echo -e "${GREEN}[5/10]${NC} Configuring Git repository..."
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

echo -e "${GREEN}[6/10]${NC} Setting up environment file..."
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
        docker run --rm -v "$SCRIPT_DIR:/app" -w /app php:8.2-cli php -r "echo 'APP_KEY=' . 'base64:' . base64_encode(random_bytes(32)) . PHP_EOL;" >> .env.tmp
        APP_KEY=$(docker run --rm php:8.2-cli php -r "echo 'base64:' . base64_encode(random_bytes(32));")
        sed -i "s|APP_KEY=.*|APP_KEY=$APP_KEY|" .env || echo "APP_KEY=$APP_KEY" >> .env
    fi
    
    # Generate JWT_SECRET if not set
    if grep -q "JWT_SECRET=$" .env || ! grep -q "JWT_SECRET=" .env; then
        echo "Generating JWT_SECRET..."
        JWT_SECRET=$(openssl rand -base64 64)
        sed -i "s|JWT_SECRET=.*|JWT_SECRET=$JWT_SECRET|" .env || echo "JWT_SECRET=$JWT_SECRET" >> .env
    fi
    
    echo -e "${YELLOW}Please edit .env file and set the following:${NC}"
    echo "  - OPENAI_API_KEY (required)"
    echo "  - DB_PASSWORD (change default password)"
    echo "  - DB_ROOT_PASSWORD (change default password)"
    echo ""
    read -p "Press Enter to continue after editing .env (or Ctrl+C to exit and edit now)..."
else
    echo ".env file already exists"
fi

echo -e "${GREEN}[7/10]${NC} Installing Certbot for SSL..."
apt-get install -y -qq certbot python3-certbot-nginx

echo -e "${GREEN}[8/10]${NC} Setting up SSL certificates..."
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

echo -e "${GREEN}[9/10]${NC} Building and starting Docker containers..."
docker-compose down 2>/dev/null || true
docker-compose build --no-cache
docker-compose up -d

echo -e "${GREEN}[10/10]${NC} Setting up Laravel application..."
# Wait for MySQL to be ready
echo "Waiting for MySQL to be ready..."
sleep 10

# Run migrations and seeders
docker-compose exec -T php-fpm php artisan migrate --force || echo -e "${YELLOW}Migration failed. Check database connection.${NC}"
docker-compose exec -T php-fpm php artisan db:seed --force || echo -e "${YELLOW}Seeder failed. Check database connection.${NC}"

# Optimize Laravel
docker-compose exec -T php-fpm php artisan config:cache
docker-compose exec -T php-fpm php artisan route:cache
docker-compose exec -T php-fpm php artisan view:cache

# Set permissions
docker-compose exec -T php-fpm chown -R www-data:www-data /var/www/html/storage
docker-compose exec -T php-fpm chown -R www-data:www-data /var/www/html/bootstrap/cache
docker-compose exec -T php-fpm chmod -R 775 /var/www/html/storage
docker-compose exec -T php-fpm chmod -R 775 /var/www/html/bootstrap/cache

echo ""
echo -e "${GREEN}[11/11]${NC} Setting up auto-deployment..."
# Make deploy script executable
chmod +x deploy.sh
chmod +x scripts/*.sh 2>/dev/null || true

# Setup cron job for git pull
./scripts/setup-cron.sh

# Setup webhook endpoint
./scripts/setup-webhook.sh || echo -e "${YELLOW}Webhook setup skipped.${NC}"

echo ""
echo -e "${GREEN}[12/12]${NC} Configuring firewall..."
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
echo "Your application should be available at:"
echo "  - https://yourmindaid.com"
echo "  - https://yourmindaid.com/api"
echo ""
echo "Next steps:"
echo "  1. Verify DNS is pointing to this server"
echo "  2. If SSL wasn't set up, run: ./scripts/setup-ssl.sh"
echo "  3. Test the API: curl https://yourmindaid.com/api/health"
echo "  4. Configure GitHub/GitLab webhook: https://yourmindaid.com/api/webhook/deploy"
echo ""
echo "Useful commands:"
echo "  - View logs: docker-compose logs -f"
echo "  - Restart: docker-compose restart"
echo "  - Stop: docker-compose down"
echo "  - Deploy: ./deploy.sh"
echo ""
