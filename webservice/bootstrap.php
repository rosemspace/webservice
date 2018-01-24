<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| The first thing we will do is create a new Rosem application instance
| which serves as the "glue" for all the components of Rosem, and is
| the IoC container for the system binding all of the various parts.
|
*/

try {
    $app = Rosem\Kernel\AppFactory::create();
    $app->loadConfig(__DIR__ . '/config/app.php'); // TODO: remove
    $app->addServiceProvidersFromFile(__DIR__ . '/config/service_providers.php');
//    $app->addMiddlewares(__DIR__ . '/config/middlewares.php');
    $app->addMiddleware(new \Rosem\Kernel\Middleware\ViewMiddleware()); // x
//    $app->addMiddleware(new \Rosem\Kernel\Middleware\ControllerMiddleware());
//    $app->addMiddleware(new \Rosem\Kernel\Middleware\AuthMiddleware());
    $app->addMiddleware($app->bindForce(\Rosem\Kernel\Middleware\RouteMiddleware::class)->make());
    $app->boot(__DIR__ . '/config/app.php');
} catch (\Exception $e) {
    echo $e->getMessage();
}
