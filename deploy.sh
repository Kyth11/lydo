#!/bin/bash

# Laravel Deployment Script for Hostinger
# This script prepares your Laravel application for deployment

set -e

echo "=========================================="
echo "Laravel Deployment Script for Hostinger"
echo "=========================================="
echo ""

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    echo "Please copy .env.production.example to .env and configure it."
    exit 1
fi

# Step 1: Install Composer dependencies
echo -e "${YELLOW}Step 1: Installing Composer dependencies...${NC}"
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✓ Composer dependencies installed${NC}"
echo ""

# Step 2: Install Node dependencies and build assets
echo -e "${YELLOW}Step 2: Building frontend assets...${NC}"
npm install
npm run build
echo -e "${GREEN}✓ Frontend assets built${NC}"
echo ""

# Step 3: Clear all caches
echo -e "${YELLOW}Step 3: Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✓ Caches cleared${NC}"
echo ""

# Step 4: Generate application key if not set
echo -e "${YELLOW}Step 4: Checking application key...${NC}"
if grep -q "APP_KEY=base64:" .env; then
    echo -e "${GREEN}✓ Application key already set${NC}"
else
    php artisan key:generate
    echo -e "${GREEN}✓ Application key generated${NC}"
fi
echo ""

# Step 5: Cache configuration
echo -e "${YELLOW}Step 5: Caching configuration...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓ Configuration cached${NC}"
echo ""

# Step 6: Optimize application
echo -e "${YELLOW}Step 6: Optimizing application...${NC}"
php artisan optimize
echo -e "${GREEN}✓ Application optimized${NC}"
echo ""

# Step 7: Create storage link if not exists
echo -e "${YELLOW}Step 7: Creating storage link...${NC}"
if [ ! -L public/storage ]; then
    php artisan storage:link
    echo -e "${GREEN}✓ Storage link created${NC}"
else
    echo -e "${GREEN}✓ Storage link already exists${NC}"
fi
echo ""

# Step 8: Set permissions
echo -e "${YELLOW}Step 8: Setting file permissions...${NC}"
chmod -R 775 storage bootstrap/cache
echo -e "${GREEN}✓ Permissions set${NC}"
echo ""

# Step 9: Create deployment package
echo -e "${YELLOW}Step 9: Creating deployment package...${NC}"
DEPLOY_DIR="deploy_package"
rm -rf $DEPLOY_DIR
mkdir -p $DEPLOY_DIR

# Copy files excluding unnecessary ones
rsync -av --exclude='node_modules' \
          --exclude='vendor' \
          --exclude='.git' \
          --exclude='.gitignore' \
          --exclude='tests' \
          --exclude='.phpunit.result.cache' \
          --exclude='.env' \
          --exclude='.env.backup' \
          --exclude='deploy_package' \
          --exclude='deploy.sh' \
          . $DEPLOY_DIR/

# Copy production env example
cp .env.production.example $DEPLOY_DIR/.env.production.example

echo -e "${GREEN}✓ Deployment package created in $DEPLOY_DIR/${NC}"
echo ""

# Step 10: Create archive
echo -e "${YELLOW}Step 10: Creating deployment archive...${NC}"
ARCHIVE_NAME="laravel-deploy-$(date +%Y%m%d-%H%M%S).tar.gz"
tar -czf $ARCHIVE_NAME -C $DEPLOY_DIR .
echo -e "${GREEN}✓ Archive created: $ARCHIVE_NAME${NC}"
echo ""

# Cleanup
echo -e "${YELLOW}Cleaning up...${NC}"
rm -rf $DEPLOY_DIR
echo -e "${GREEN}✓ Cleanup complete${NC}"
echo ""

echo "=========================================="
echo -e "${GREEN}Deployment preparation complete!${NC}"
echo "=========================================="
echo ""
echo "Next steps:"
echo "1. Upload $ARCHIVE_NAME to Hostinger"
echo "2. Extract the archive in public_html"
echo "3. SSH into Hostinger and run:"
echo "   cd public_html"
echo "   composer install --no-dev --optimize-autoloader"
echo "   php artisan key:generate"
echo "   php artisan migrate --force"
echo "   chmod -R 775 storage bootstrap/cache"
echo ""
echo "See DEPLOYMENT.md for detailed instructions."
