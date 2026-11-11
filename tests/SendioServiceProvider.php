<?php

use Sendio\Client;
use Sendio\Laravel\Exceptions\ApiKeyIsMissing;
use Sendio\Laravel\SendioServiceProvider;

it('requires an API key', function () {
    app()->get('sendio');
})->throws(ApiKeyIsMissing::class);

it('can bind the Sendio Client using the service config', function () {
    $app = app();
    $app->get('config')->set('services', [
        'sendio' => [
            'key' => 'mx_test',
        ],
    ]);

    $sendio = $app->get('sendio');

    expect($sendio)->toBeInstanceOf(Client::class);
});

it('uses the sendio.base_url config value as the API base URL', function () {
    $app = app();
    $app->get('config')->set('sendio.api_key', 'mx_test');
    $app->get('config')->set('sendio.base_url', 'https://api.sendio.run');

    $sendio = $app->get('sendio');

    $transporter = (new ReflectionProperty(Client::class, 'transporter'))->getValue($sendio);
    $baseUri = (new ReflectionProperty($transporter, 'baseUri'))->getValue($transporter);

    expect($baseUri->toString())->toBe('https://api.sendio.run/');
});

it('defaults to https://api.send.io when no base URL is configured', function () {
    $app = app();
    $app->get('config')->set('sendio.api_key', 'mx_test');

    $sendio = $app->get('sendio');

    $transporter = (new ReflectionProperty(Client::class, 'transporter'))->getValue($sendio);
    $baseUri = (new ReflectionProperty($transporter, 'baseUri'))->getValue($transporter);

    expect($baseUri->toString())->toBe('https://api.send.io/');
});

it('provides', function () {
    $provider = app()->resolveProvider(SendioServiceProvider::class);

    $provides = $provider->provides();

    expect($provides)->toBe([
        Client::class,
    ]);
});
