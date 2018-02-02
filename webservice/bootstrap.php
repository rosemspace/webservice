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

//try {
    $app = Rosem\App\AppFactory::create();
    $app->loadServiceProviders(__DIR__ . '/config/service_providers.php');
    $app->loadMiddlewares(__DIR__ . '/config/middlewares.php');
    $app->boot(__DIR__ . '/config/app.php');
//} catch (\Exception $e) {
//    echo $e->getMessage();
//}
