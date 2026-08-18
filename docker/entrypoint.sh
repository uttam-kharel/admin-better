#!/bin/sh
set -eu

# =============================================================================
#  Container entrypoint for the Vercel Docker deployment.
#
#  1. Fill ephemeral-runtime defaults (Vercel env vars always win).
#  2. Create the /tmp directories Laravel, Blade and Livewire write to.
#  3. Verify the app can boot (APP_KEY) and print a config report to the logs.
#  4. Bake Laravel config at boot so Vercel env vars are honored.
#  5. exec FrankenPHP (worker mode opt-in via FRANKENPHP_CONFIG).
# =============================================================================

# -----------------------------------------------------------------------------
# 1. Ephemeral-runtime defaults
# -----------------------------------------------------------------------------
# The container filesystem is not persistent on Vercel, so keep every
# file-backed cache under /tmp and log to stderr (shows up in Function Logs).
# This mirrors api/index.php from the old serverless runtime — every variable
# it sets is set here too, so the container behaves identically. Each line is
# guarded (`:=` only when unset/empty), so an explicit env var set in the
# Vercel dashboard always wins.
: "${APP_ENV:=production}"
: "${APP_DEBUG:=false}"
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

export APP_ENV APP_DEBUG \
       APP_CONFIG_CACHE APP_ROUTES_CACHE APP_EVENTS_CACHE \
       APP_PACKAGES_CACHE APP_SERVICES_CACHE VIEW_COMPILED_PATH \
       LOG_CHANNEL SESSION_DRIVER CACHE_STORE QUEUE_CONNECTION \
       LIVEWIRE_TEMPORARY_FILE_UPLOAD_DISK

# -----------------------------------------------------------------------------
# 2. /tmp directories used at runtime
# -----------------------------------------------------------------------------
# Blade compiles views into VIEW_COMPILED_PATH and Livewire stages uploads in
# sys_get_temp_dir()/livewire-tmp — neither exists by default, so create them
# or the first request / upload would fail.
mkdir -p "${VIEW_COMPILED_PATH}" /tmp/livewire-tmp

# If sessions are file-backed (SESSION_DRIVER=file), point them at /tmp too —
# storage/framework/sessions is not persistent here. Only applied when the
# driver is actually file, so cookie/database sessions are untouched.
if [ "${SESSION_DRIVER:-}" = "file" ]; then
    : "${SESSION_PATH:=/tmp/sessions}"
    export SESSION_PATH
    mkdir -p "${SESSION_PATH}"
fi

# -----------------------------------------------------------------------------
# 3. Boot checks
# -----------------------------------------------------------------------------
# Laravel cannot boot without an encryption key (sessions, cookies, Livewire).
if [ -z "${APP_KEY:-}" ]; then
    echo "ERROR: APP_KEY is not set. Add it under Vercel -> Project -> Settings" >&2
    echo "       -> Environment Variables (Production scope), or run 'vercel env add'." >&2
    exit 1
fi

# Print which Vercel services (Neon / Blob / KV) are configured. Non-fatal:
# the app works with only APP_KEY, it just falls back to the committed SQLite
# content DB and local/array storage. Output lands in Vercel Function Logs.
echo "--- Vercel free-stack report (see also: php artisan vercel:setup) ---"
php artisan vercel:setup || true
echo "--- end report ---"

# -----------------------------------------------------------------------------
# 4. Bake config at boot
# -----------------------------------------------------------------------------
# config:cache runs at boot (not build time) so the env vars Vercel injects at
# runtime are honored. Failure is non-fatal — the app still runs uncached —
# but make it visible in the logs instead of swallowing it silently.
echo "Caching config (env: ${APP_ENV}, cache: ${CACHE_STORE}, session: ${SESSION_DRIVER})..."
if ! php artisan config:cache --no-interaction >/tmp/config-cache.log 2>&1; then
    echo "WARNING: php artisan config:cache failed — running with uncached config:" >&2
    cat /tmp/config-cache.log >&2 || true
fi

# -----------------------------------------------------------------------------
# 5. Start FrankenPHP
# -----------------------------------------------------------------------------
# Standard mode: one PHP process per request (like the old serverless runtime).
# For higher throughput you can enable worker mode — Laravel boots once and
# serves many requests:
#     FRANKENPHP_CONFIG="worker ./public/index.php"
# (set it in the Vercel dashboard, or uncomment below). Start without it;
# verify the app first, then flip it on and watch the Function Logs.
# : "${FRANKENPHP_CONFIG:=worker ./public/index.php}"
# export FRANKENPHP_CONFIG

exec "$@"
