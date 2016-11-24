<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://riftbets.dev/';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        if ($app->environment('testing')) {
            $this->baseUrl = 'http://localhost:8000/';
        } else {
            $this->baseUrl = 'http://riftbets.dev/';
        }

        echo $this->baseUrl;

        return $app;
    }
}
