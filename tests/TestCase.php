<?php

namespace Tests;

use Laravel\Lumen\Testing\TestCase as TC;

abstract class TestCase extends TC
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }
}
