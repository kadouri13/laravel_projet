#!/bin/sh

# Exit on fail
set -e

echo "Starting deployment setup..."

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Run seeders
echo "Seeding the database..."
php artisan db:seed --force

# Cache configuration, routes, and views for performance
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setup complete. Configuring Apache port..."

# Set Apache to listen on the port provided by Render (or default to 80)
sed -i "s/80/${PORT:-80}/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

echo "Starting Apache..."

# Execute the default command provided by the apache image
exec apache2-foreground
