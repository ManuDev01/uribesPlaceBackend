#!/usr/bin/env bash
set -e

php artisan optimize

exec php artisan serve --host=0.0.0.0 --port="${PORT:-8000}"
