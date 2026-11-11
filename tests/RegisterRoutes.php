<?php

namespace Sendio\Laravel\Tests;

use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\Attributes\DefineEnvironment;

class RegisterRoutes extends TestCase
{
    protected function disableRoutes($app): void
    {
        $app['config']->set('sendio.routes', false);
    }

    public function test_the_webhook_route_is_registered_by_default(): void
    {
        $this->assertTrue(Route::has('sendio.webhook'));

        // 400: the unsigned request reached the webhook signature middleware
        $this->post('sendio/webhook')->assertStatus(400);
    }

    #[DefineEnvironment('disableRoutes')]
    public function test_routes_are_not_registered_when_disabled(): void
    {
        $this->assertFalse(Route::has('sendio.webhook'));

        $this->post('sendio/webhook')->assertNotFound();
    }
}
