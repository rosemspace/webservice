<?php

namespace Rosem\Psr\App;

use Psr\Container\ContainerInterface;

interface AppInterface extends ContainerInterface
{
    /**
     * Use a middleware to process the request / response flow.
     *
     * @param string $middleware The middleware class
     * @param float  $priority   The priority of the middleware execution.
     *
     * @return void
     */
    public function use(string $middleware, float $priority = 0): void;

    /**
     * Boot the application with the specified config.
     *
     * @param array $config The config of the app
     *
     * @return void
     */
    public function boot(array $config): void;
}
