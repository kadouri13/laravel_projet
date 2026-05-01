#!/bin/sh

# Exit on fail
set -e

echo "Starting deployment setup..."

# Run migrations
echo "Running database migrations..."
php artisan migrate --force

# Cache configuration, routes, and views for performance
echo "Caching configuration and routes..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Setup complete. Starting Apache..."

# Execute the default command provided by the apache image
exec apache2-foreground
