#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Setting up webhook endpoint for automatic deployment...${NC}"
# before we do anything, we need to check if the webhook is already set up.
# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$SCRIPT_DIR"

# Check if webhook route exists in Laravel
if grep -q "webhook/deploy" routes/api.php 2>/dev/null; then
    echo -e "${GREEN}Webhook route already exists in routes/api.php${NC}"
else
    echo -e "${YELLOW}Webhook route will be handled by the webhook.php endpoint.${NC}"
fi

# Generate webhook secret if not exists
if [ ! -f ".env" ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    exit 1
fi

if ! grep -q "WEBHOOK_SECRET=" .env || grep -q "WEBHOOK_SECRET=$" .env; then
    WEBHOOK_SECRET=$(openssl rand -hex 32)
    if grep -q "WEBHOOK_SECRET=" .env; then
        sed -i "s|WEBHOOK_SECRET=.*|WEBHOOK_SECRET=$WEBHOOK_SECRET|" .env
    else
        echo "WEBHOOK_SECRET=$WEBHOOK_SECRET" >> .env
    fi
    echo -e "${GREEN}Webhook secret generated and added to .env${NC}"
    echo -e "${YELLOW}Webhook Secret: $WEBHOOK_SECRET${NC}"
    echo "Use this secret when configuring your GitHub/GitLab webhook."
else
    WEBHOOK_SECRET=$(grep "WEBHOOK_SECRET=" .env | cut -d '=' -f2)
    echo -e "${GREEN}Using existing webhook secret from .env${NC}"
fi

echo ""
echo -e "${GREEN}Webhook setup complete!${NC}"
echo ""
echo "Webhook URL: https://yourmindaid.com/api/webhook/deploy"
echo "Webhook Secret: $WEBHOOK_SECRET"
echo ""
echo "To configure GitHub webhook:"
echo "  1. Go to your repository Settings > Webhooks"
echo "  2. Add webhook"
echo "  3. Payload URL: https://yourmindaid.com/api/webhook/deploy"
echo "  4. Content type: application/json"
echo "  5. Secret: $WEBHOOK_SECRET"
echo "  6. Events: Just the push event"
echo "  7. Active: Yes"
echo ""
