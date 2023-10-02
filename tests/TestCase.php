<?php

namespace ALajusticia\Localized\Tests;

use ALajusticia\Localized\LocalizedServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Load test-specific migrations
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Load package migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        $this->artisan('migrate')->run();
    }

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            LocalizedServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }
}
