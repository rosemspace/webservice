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

ini_set('display_errors', true);
ini_set('display_startup_errors', true);
error_reporting(E_ALL);

Rosem\Kernel\App::launch();
