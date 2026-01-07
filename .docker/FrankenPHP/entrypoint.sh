#!/bin/sh
set -e

# Run standard Laravel optimizations
php artisan package:discover --ansi
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Start Octane
exec php artisan octane:frankenphp --host=0.0.0.0 --port=8000
