<?php

namespace Sendio\Laravel;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Sendio;
use Sendio\Client;
use Sendio\Contracts\Client as ClientContract;
use Sendio\Laravel\Exceptions\ApiKeyIsMissing;
use Sendio\Laravel\Transport\SendioTransportFactory;

class SendioServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerPublishing();

        Mail::extend('sendio', function (array $config = []) {
            return new SendioTransportFactory($this->app['sendio'], $config['options'] ?? []);
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->configure();
        $this->bindSendioClient();
    }

    /**
     * Setup the configuration for send.io.
     */
    protected function configure(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/sendio.php', 'sendio'
        );
    }

    /**
     * Bind the send.io Client.
     */
    protected function bindSendioClient(): void
    {
        $this->app->singleton(ClientContract::class, static function (): Client {
            $apiKey = config('sendio.api_key') ?? config('services.sendio.key');

            if (! is_string($apiKey)) {
                throw ApiKeyIsMissing::create();
            }

            $baseUrl = config('sendio.base_url') ?? config('services.sendio.base_url');

            return Sendio::client($apiKey, is_string($baseUrl) && $baseUrl !== '' ? $baseUrl : null);
        });

        $this->app->alias(ClientContract::class, 'sendio');
        $this->app->alias(ClientContract::class, Client::class);
    }

    /**
     * Register the package routes.
     */
    protected function registerRoutes(): void
    {
        if (! config('sendio.routes')) {
            return;
        }

        Route::group([
            'domain' => config('sendio.domain', null),
            'namespace' => 'Sendio\Laravel\Http\Controllers',
            'prefix' => config('sendio.path'),
            'as' => 'sendio.',
            'middleware' => config('sendio.middleware', []),
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }

    /**
     * Register the package's publishable assets.
     */
    protected function registerPublishing(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/sendio.php' => $this->app->configPath('sendio.php'),
            ], 'sendio-config');
        }
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            Client::class,
        ];
    }
}
