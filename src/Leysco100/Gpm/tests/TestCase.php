<?php
namespace Leysco\GatePassManagementModule\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Leysco\GatePassManagementModule\GatePassManagementModuleServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase

{

    use DatabaseMigrations;
    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            GatePassManagementModuleServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
