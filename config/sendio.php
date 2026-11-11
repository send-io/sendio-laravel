<?php

return [

    /*
    |--------------------------------------------------------------------------
    | send.io API Key
    |--------------------------------------------------------------------------
    |
    | The send.io API key gives you access to the send.io API. The "api_key"
    | is used to authenticate every request, including mail sent through
    | the "sendio" mailer.
    |
    */

    'api_key' => env('SENDIO_API_KEY'),

    /*
    |--------------------------------------------------------------------------
    | send.io Base URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the send.io API. Leave it empty to use the default,
    | https://api.send.io. Set it to point the client at another endpoint,
    | for example a test environment. Read from config, so it keeps working
    | after "php artisan config:cache".
    |
    */

    'base_url' => env('SENDIO_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | send.io Routes
    |--------------------------------------------------------------------------
    |
    | This option determines whether the package registers its routes, such
    | as the webhook handler. You may disable this when handling incoming
    | webhooks yourself or when the routes are not needed at all.
    |
    */

    'routes' => env('SENDIO_ROUTES', true),

    /*
    |--------------------------------------------------------------------------
    | send.io Domain
    |--------------------------------------------------------------------------
    |
    | This is the subdomain where the package routes will be accessible from.
    | If the setting is null, the package will reside under the same domain as your
    | application. Otherwise, this value will be used as the subdomain.
    |
    */

    'domain' => env('SENDIO_DOMAIN', null),

    /*
    |--------------------------------------------------------------------------
    | send.io Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI path where the package routes, such as the webhook
    | handler, will be available from. You are free to tweak the path to your
    | preference and application design.
    |
    */

    'path' => env('SENDIO_PATH', 'sendio'),

    /*
    |--------------------------------------------------------------------------
    | send.io Webhooks
    |--------------------------------------------------------------------------
    |
    | Your send.io webhook secret is used to prevent unauthorized requests to
    | your send.io webhook handling controllers. The tolerance setting will
    | check the drift between the current time and the signed request's.
    |
    */

    'webhook' => [
        'secret' => env('SENDIO_WEBHOOK_SECRET'),
        'tolerance' => env('SENDIO_WEBHOOK_TOLERANCE', 300),
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | The list of middlewares that will be assigned to the webhook route.
    |
    */

    'middleware' => []

];
