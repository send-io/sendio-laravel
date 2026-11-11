<?php

namespace Sendio\Laravel\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use Sendio\Laravel\SendioServiceProvider;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            SendioServiceProvider::class,
        ];
    }
}
