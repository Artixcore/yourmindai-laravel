#!/bin/bash

# Script to resolve git pull conflicts on EC2
# This script stashes local changes, pulls remote updates, and reapplies the stash

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Resolving Git Pull Conflicts${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""

# Get the directory where the script is located
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && cd .. && pwd )"
cd "$SCRIPT_DIR"

echo -e "${GREEN}[1/5]${NC} Checking git status..."
git status --short

echo ""
echo -e "${GREEN}[2/5]${NC} Stashing local changes..."
if git stash save "Local EC2 changes before pull - $(date +%Y-%m-%d_%H-%M-%S)"; then
    echo -e "${GREEN}✓ Local changes stashed successfully${NC}"
else
    echo -e "${YELLOW}⚠ No changes to stash or stash failed${NC}"
fi

echo ""
echo -e "${GREEN}[3/5]${NC} Pulling remote changes..."
if git pull origin main; then
    echo -e "${GREEN}✓ Remote changes pulled successfully${NC}"
else
    echo -e "${RED}✗ Failed to pull remote changes${NC}"
    echo -e "${YELLOW}Restoring stashed changes...${NC}"
    git stash pop 2>/dev/null || true
    exit 1
fi

echo ""
echo -e "${GREEN}[4/5]${NC} Reapplying stashed changes..."
if git stash pop; then
    echo -e "${GREEN}✓ Stashed changes reapplied successfully${NC}"
    echo ""
    echo -e "${GREEN}[5/5]${NC} Checking for conflicts..."
    
    # Check if there are any merge conflicts
    if git diff --check || git diff --cached --check; then
        echo -e "${YELLOW}⚠ Potential conflicts detected. Please review the files.${NC}"
    fi
    
    # Check git status
    echo ""
    echo "Current git status:"
    git status --short
    
    CONFLICTS=$(git diff --name-only --diff-filter=U 2>/dev/null || echo "")
    if [ ! -z "$CONFLICTS" ]; then
        echo ""
        echo -e "${RED}⚠ Merge conflicts detected in the following files:${NC}"
        echo "$CONFLICTS"
        echo ""
        echo -e "${YELLOW}Please resolve conflicts manually:${NC}"
        echo "  1. Edit the conflicted files"
        echo "  2. Remove conflict markers (<<<<<<, ======, >>>>>>)"
        echo "  3. Run: git add <resolved-files>"
        echo "  4. Run: git stash drop (to remove the stash)"
    else
        echo ""
        echo -e "${GREEN}✓ No conflicts detected!${NC}"
        echo -e "${GREEN}All changes merged successfully.${NC}"
    fi
else
    echo -e "${YELLOW}⚠ No stash to apply or conflicts occurred${NC}"
    echo ""
    echo "Checking for conflicts..."
    CONFLICTS=$(git diff --name-only --diff-filter=U 2>/dev/null || echo "")
    if [ ! -z "$CONFLICTS" ]; then
        echo -e "${RED}⚠ Merge conflicts detected in the following files:${NC}"
        echo "$CONFLICTS"
        echo ""
        echo -e "${YELLOW}Please resolve conflicts manually:${NC}"
        echo "  1. Edit the conflicted files"
        echo "  2. Remove conflict markers (<<<<<<, ======, >>>>>>)"
        echo "  3. Run: git add <resolved-files>"
    else
        echo -e "${GREEN}✓ No conflicts - stash was empty or already applied${NC}"
    fi
fi

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}Process Complete!${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "Next steps:"
echo "  - Review any conflicted files if conflicts were detected"
echo "  - Test your application to ensure everything works"
echo "  - If everything is good, you can commit your merged changes"
echo ""
