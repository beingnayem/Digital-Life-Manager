#!/bin/sh

echo "🚀 Starting Laravel container..."

# Wait for DB (important for DigitalOcean / cloud DB)
sleep 5

echo "🔧 Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure log file exists
mkdir -p /var/www/html/storage/logs
touch /var/www/html/storage/logs/laravel.log
chmod 777 /var/www/html/storage/logs/laravel.log

echo "🧹 Clearing cache..."
php artisan config:clear
php artisan cache:clear

echo "⚙️ Caching config..."
php artisan config:cache

echo "🛢 Running migrations..."
php artisan migrate --force

echo "🌐 Starting Apache..."
exec apache2-foreground