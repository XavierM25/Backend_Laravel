#!/bin/bash

# Generate application key if not exists
if [ ! -f .env ]; then
    if [ -f .env.render ]; then
        cp .env.render .env
    elif [ -f .env.production ]; then
        cp .env.production .env
    else
        cp .env.example .env
    fi
fi

# Generate application key
php artisan key:generate --force

# Run production setup script
if [ -f setup-production.sh ]; then
    chmod +x setup-production.sh
    ./setup-production.sh
else
    # Fallback configuration
    php artisan config:clear
    php artisan config:cache
    php artisan route:clear
    php artisan route:cache
    php artisan view:clear
    php artisan view:cache
    php artisan migrate --force
fi

# Start Apache
apache2-foreground
