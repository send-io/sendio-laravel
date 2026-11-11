#!/usr/bin/env bash
#
# Live smoke test for sendio/sendio-laravel in a real, fresh Laravel app (Docker).
# Not part of the unit tests.
#
#   SENDIO_API_KEY=mx_... SENDIO_BASE_URL=https://api.send.io sdks/laravel/examples/smoke.sh [app-dir]
#
# It creates a Laravel app in [app-dir] (default: sdks/laravel/build/app), installs
# sendio/sendio-laravel from this directory and sendio/sendio-php from ../php through
# composer path repositories, sets MAIL_MAILER=sendio, runs `php artisan config:cache`
# and sends one Mail::raw message, then fetches it back by id through the Sendio facade.
#
# Optional: SMOKE_FROM (default ada@compat-sdk.sendio-test.dev), SMOKE_TO (default
# delivered@send.io), LARAVEL_VERSION (default ^12.0), COMPOSER_IMAGE (default composer:2).
set -euo pipefail

here="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php_sdk="$(cd "$here/../php" && pwd)"
app_dir="${1:-$here/build/app}"
mkdir -p "$app_dir"
app_dir="$(cd "$app_dir" && pwd)"

: "${SENDIO_API_KEY:?SENDIO_API_KEY is not set}"
export SENDIO_API_KEY
export SENDIO_BASE_URL="${SENDIO_BASE_URL:-}"
export SMOKE_FROM="${SMOKE_FROM:-ada@compat-sdk.sendio-test.dev}"
export SMOKE_TO="${SMOKE_TO:-delivered@send.io}"
export LARAVEL_VERSION="${LARAVEL_VERSION:-^12.0}"

docker run --rm \
  -e SENDIO_API_KEY -e SENDIO_BASE_URL -e SMOKE_FROM -e SMOKE_TO -e LARAVEL_VERSION \
  -v "$here:/sdks/laravel:ro" \
  -v "$php_sdk:/sdks/php:ro" \
  -v "$app_dir:/app" \
  -w /app \
  "${COMPOSER_IMAGE:-composer:2}" sh /sdks/laravel/examples/smoke/in-container.sh
