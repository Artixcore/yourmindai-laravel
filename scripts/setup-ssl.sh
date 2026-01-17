#!/bin/bash

set -e

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

# Check if certbot is installed
if ! command -v certbot &> /dev/null; then
    echo "Installing Certbot..."
    apt-get update -qq
    apt-get install -y -qq certbot python3-certbot-nginx
fi

# Check if DNS is configured
echo "Checking DNS configuration..."
if ! dig +short yourmindaid.com | grep -qE '^[0-9]+\.[0-9]+\.[0-9]+\.[0-9]+$'; then
    echo -e "${YELLOW}Warning: DNS for yourmindaid.com may not be configured correctly.${NC}"
    echo "Please ensure yourmindaid.com points to this server's IP address."
    read -p "Continue anyway? (y/n): " CONTINUE
    if [ "$CONTINUE" != "y" ] && [ "$CONTINUE" != "Y" ]; then
        exit 1
    fi
fi

# Start nginx container for validation
echo "Starting nginx container for certificate validation..."
docker-compose up -d nginx
sleep 5

# Create certbot webroot directory
mkdir -p /var/www/certbot

# Obtain certificates
echo "Obtaining SSL certificates..."
certbot certonly --webroot \
    --webroot-path=/var/www/certbot \
    --email admin@yourmindaid.com \
    --agree-tos \
    --no-eff-email \
    -d yourmindaid.com \
    -d www.yourmindaid.com \
    --non-interactive

if [ $? -eq 0 ]; then
    echo -e "${GREEN}SSL certificates obtained successfully!${NC}"
    
    # Reload nginx
    echo "Reloading nginx..."
    docker-compose exec nginx nginx -s reload || docker-compose restart nginx
    
    # Setup auto-renewal
    echo "Setting up certificate auto-renewal..."
    (crontab -l 2>/dev/null | grep -v "certbot renew"; echo "0 3 * * * certbot renew --quiet --deploy-hook 'cd $SCRIPT_DIR && docker-compose exec nginx nginx -s reload'") | crontab -
    
    echo -e "${GREEN}SSL setup complete! Certificates will auto-renew.${NC}"
else
    echo -e "${RED}Failed to obtain SSL certificates.${NC}"
    echo "Make sure:"
    echo "  1. DNS is pointing to this server"
    echo "  2. Port 80 is open and accessible"
    echo "  3. Nginx container is running"
    exit 1
fi
