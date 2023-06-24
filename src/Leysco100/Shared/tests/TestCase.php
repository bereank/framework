<?php

namespace Leysco\LS100SharedPackage\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Leysco\LS100SharedPackage\LS100SharedPackageServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase

{
    use RefreshDatabase;
    public function setUp(): void
    {
        parent::setUp();

    }

    protected function getPackageProviders($app)
    {
        return [
            LS100SharedPackageServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {

        $app['config']->set('app.env', 'testing');
        $app['config']->set('app.debug', true);

        $app['config']->set('database.connections.testing', [
            'driver' => 'mysql',
            'host' => env('DB_TEST_HOST', 'localhost'),
            'database' => env('DB_TEST_DATABASE', 'testing_db'),
            'username' => env('DB_TEST_USERNAME', 'root'),
            'password' => env('DB_TEST_PASSWORD', ''),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
        ]);
    }
}
