#!/bin/sh
set -e

echo "🚀 Starting ACME Donation Platform..."

# Fix permissions for Laravel directories (needed for Railway/mounted volumes)
echo "📁 Setting permissions for storage and bootstrap/cache..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Ensure all required directories exist
echo "📂 Creating required directories..."
mkdir -p /var/www/html/storage/framework/{sessions,views,cache}
mkdir -p /var/www/html/storage/logs
mkdir -p /var/www/html/bootstrap/cache

# Run Laravel migrations
echo "🗄️  Running database migrations..."
php artisan migrate --force

# Cache Laravel configuration
echo "⚡ Caching Laravel configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Initialization complete!"

# Start Apache
echo "🌐 Starting Apache server..."
exec apache2-foreground
