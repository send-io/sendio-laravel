#!/bin/sh
# Runs inside the composer image, from examples/smoke.sh. Working directory: the Laravel app.
set -eu

if [ ! -f artisan ]; then
  composer create-project --no-interaction --no-progress --no-scripts laravel/laravel . "$LARAVEL_VERSION"
  cp .env.example .env
  php artisan key:generate --no-interaction
fi

composer config repositories.sendio-php \
  '{"type":"path","url":"/sdks/php","options":{"symlink":false,"versions":{"sendio/sendio-php":"1.0.0"}}}'
composer config repositories.sendio-laravel \
  '{"type":"path","url":"/sdks/laravel","options":{"symlink":false,"versions":{"sendio/sendio-laravel":"1.0.0"}}}'
composer require --no-interaction --no-progress 'sendio/sendio-laravel:^1.0'

# Register the "sendio" mailer in config/mail.php and set .env values.
php /sdks/laravel/examples/smoke/configure.php

php artisan config:clear
php artisan config:cache

php artisan tinker /sdks/laravel/examples/smoke/send.php
