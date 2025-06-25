#!/bin/bash

# Deploy Commands for Laravel Cloud
# This script ensures proper deployment with cache clearing and route optimization

echo "🚀 Starting deployment process..."

# 1. Run migrations
echo "📊 Running database migrations..."
php artisan migrate --force

# 2. Clear all caches
echo "🧹 Clearing all caches..."
php artisan route:clear
php artisan config:clear
php artisan view:clear
php artisan cache:clear

# 3. Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 4. Verify routes are working
echo "🔍 Verifying critical routes..."
php artisan route:list --name="api.dashboard.patients" || echo "⚠️  Patient stats route not found"

# 5. Run our custom cache clearing command
echo "🔧 Running custom cache clearing..."
php artisan route:clear-all --force

echo "✅ Deployment completed successfully!"

# Optional: Show route summary
echo "📋 Route summary:"
php artisan route:list --compact | grep -E "(patients|dashboard)" | head -10 