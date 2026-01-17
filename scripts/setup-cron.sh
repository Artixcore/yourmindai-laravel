#!/bin/bash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Setting up cron job for automatic git deployment...${NC}"

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
DEPLOY_SCRIPT="$SCRIPT_DIR/deploy.sh"

# Make deploy script executable
chmod +x "$DEPLOY_SCRIPT"

# Create cron job entry
CRON_JOB="*/5 * * * * cd $SCRIPT_DIR && $DEPLOY_SCRIPT >> $SCRIPT_DIR/storage/logs/cron-deploy.log 2>&1"

# Check if cron job already exists
if crontab -l 2>/dev/null | grep -q "$DEPLOY_SCRIPT"; then
    echo -e "${YELLOW}Cron job already exists. Updating...${NC}"
    # Remove existing entry
    crontab -l 2>/dev/null | grep -v "$DEPLOY_SCRIPT" | crontab -
fi

# Add new cron job
(crontab -l 2>/dev/null; echo "$CRON_JOB") | crontab -

echo -e "${GREEN}Cron job installed successfully!${NC}"
echo "The deployment script will run every 5 minutes."
echo "Logs will be written to: $SCRIPT_DIR/storage/logs/cron-deploy.log"
echo ""
echo "To view cron jobs: crontab -l"
echo "To remove cron job: crontab -e"
