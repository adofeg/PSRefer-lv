#!/bin/bash
set -e

echo "🚀 Starting Plesk Deployment..."

# 1. Update Code
# git pull origin main (Uncomment if using git on server)

# 2. Install PHP Dependencies
echo "📦 Installing Composer Dependencies..."
composer install --no-dev --optimize-autoloader

# 3. Migrate Database
echo "🗄️ Running Migrations..."
php artisan migrate --force

# 4. Install & Build Frontend
echo "🎨 Building Frontend..."
npm install
npm run build

# 5. Clear Caches
echo "🧹 Clearing Caches..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment Complete!"
