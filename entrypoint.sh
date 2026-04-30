#!/bin/sh

echo "🚀 Starting Laravel container..."

# Wait a bit for DB (important for cloud DB like DigitalOcean)
sleep 5

# Clear and cache config
php artisan config:clear
php artisan cache:clear
php artisan config:cache

# Run migrations
php artisan migrate --force

# Optional: seed
# php artisan db:seed --force

# Start Apache
exec apache2-foreground