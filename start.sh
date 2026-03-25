#!/bin/sh
set -e

php artisan optimize

exec frankenphp run --config /app/Caddyfile
