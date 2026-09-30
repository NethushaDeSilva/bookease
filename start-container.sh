#!/bin/bash
set -euo pipefail

# Never seed demo accounts when deploying the real application.
php artisan config:clear
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile
