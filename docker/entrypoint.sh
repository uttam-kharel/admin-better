#!/bin/sh
set -eu

# =============================================================================
#  Container entrypoint for the Vercel Docker deployment.
#
#  1. Fill ephemeral-runtime defaults (Vercel env vars always win).
#  2. Verify the app can boot (APP_KEY).
#  3. Bake Laravel config at boot so Vercel env vars are honored.
#  4. exec FrankenPHP.
# =============================================================================

# The container filesystem is not persistent on Vercel, so keep every
# file-backed cache under /tmp and log to stderr (shows up in Function Logs).
# Mirrors api/index.php from the old serverless runtime.
: "${APP_CONFIG_CACHE:=/tmp/config.php}"
: "${APP_ROUTES_CACHE:=/tmp/routes.php}"
: "${APP_EVENTS_CACHE:=/tmp/events.php}"
: "${APP_PACKAGES_CACHE:=/tmp/packages.php}"
: "${APP_SERVICES_CACHE:=/tmp/services.php}"
: "${VIEW_COMPILED_PATH:=/tmp/views}"
: "${LOG_CHANNEL:=stderr}"
: "${SESSION_DRIVER:=cookie}"
: "${CACHE_STORE:=array}"
: "${QUEUE_CONNECTION:=sync}"
: "${LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK:=livewire-tmp}"

export APP_CONFIG_CACHE APP_ROUTES_CACHE APP_EVENTS_CACHE APP_PACKAGES_CACHE \
       APP_SERVICES_CACHE VIEW_COMPILED_PATH LOG_CHANNEL SESSION_DRIVER \
       CACHE_STORE QUEUE_CONNECTION LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK

# Laravel cannot boot without an encryption key.
if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is not set. Add it under Vercel -> Project -> Settings" >&2
    echo "       -> Environment Variables (Production scope), or run 'vercel env add'." >&2
    exit 1
fi

# Bake config at boot (not at build time) so the env vars Vercel injects at
# runtime are honored. Failures are non-fatal — the app still runs uncached.
php artisan config:cache --no-interaction >/dev/null 2>&1 || true

exec "$@"
