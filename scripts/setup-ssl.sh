#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Setting up SSL certificates with Let's Encrypt...${NC}"

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}Please run as root (use sudo)${NC}"
    exit 1
fi

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$SCRIPT_DIR"

# Create certbot webroot directory early (before nginx starts)
echo "Creating certbot directories..."
mkdir -p /var/www/certbot
chmod 755 /var/www/certbot

# Check if certbot is installed
if ! command -v certbot &> /dev/null; then
    echo "Installing Certbot..."
    apt-get update -qq
    apt-get install -y -qq certbot python3-certbot-nginx || {
        echo -e "${YELLOW}Failed to install Certbot. Continuing anyway...${NC}"
    }
fi

# Check if DNS is configured
echo "Checking DNS configuration..."
if ! dig +short yourmindaid.com | grep -qE '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$'; then
    echo -e "${YELLOW}Warning: DNS for yourmindaid.com may not be configured correctly.${NC}"
    echo "Please ensure yourmindaid.com points to this server's IP address."
    read -p "Continue anyway? (y/n): " CONTINUE
    if [ "$CONTINUE" != "y" ] && [ "$CONTINUE" != "Y" ]; then
        echo -e "${YELLOW}SSL setup skipped. You can run this script later after DNS is configured.${NC}"
        exit 0
    fi
fi

# Start nginx container for validation
echo "Starting nginx container for certificate validation..."
docker-compose up -d nginx || {
    echo -e "${YELLOW}Failed to start nginx container. SSL setup will be skipped.${NC}"
    echo "Make sure Docker containers are running: docker-compose up -d"
    exit 0
}

# Wait for nginx to be ready
echo "Waiting for nginx to be ready..."
MAX_WAIT=30
WAIT_COUNT=0
while ! docker-compose exec -T nginx nginx -t 2>/dev/null; do
    if [ $WAIT_COUNT -ge $MAX_WAIT ]; then
        echo -e "${YELLOW}Nginx is taking longer than expected. Continuing anyway...${NC}"
        break
    fi
    sleep 1
    WAIT_COUNT=$((WAIT_COUNT + 1))
done

# Obtain certificates
echo "Obtaining SSL certificates..."
if certbot certonly --webroot \
    --webroot-path=/var/www/certbot \
    --email admin@yourmindaid.com \
    --agree-tos \
    --no-eff-email \
    -d yourmindaid.com \
    -d www.yourmindaid.com \
    --non-interactive 2>&1; then
    
    echo -e "${GREEN}SSL certificates obtained successfully!${NC}"
    
    # Reload nginx
    echo "Reloading nginx..."
    docker-compose exec nginx nginx -s reload 2>/dev/null || docker-compose restart nginx || true
    
    # Setup auto-renewal
    echo "Setting up certificate auto-renewal..."
    (crontab -l 2>/dev/null | grep -v "certbot renew"; echo "0 3 * * * certbot renew --quiet --deploy-hook 'cd $SCRIPT_DIR && docker-compose exec nginx nginx -s reload'") | crontab - 2>/dev/null || true
    
    echo -e "${GREEN}SSL setup complete! Certificates will auto-renew.${NC}"
else
    echo -e "${YELLOW}Failed to obtain SSL certificates. This is not critical - you can set up SSL later.${NC}"
    echo "Common issues:"
    echo "  1. DNS is not pointing to this server"
    echo "  2. Port 80 is not open or accessible"
    echo "  3. Nginx container is not running properly"
    echo ""
    echo "You can run this script again later: ./scripts/setup-ssl.sh"
    exit 0
fi
