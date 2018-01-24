<?php

return [
    Rosem\Kernel\ServiceProvider\HttpFactoryServiceProvider::class,
    Rosem\Access\AccessServiceProvider::class,

    Rosem\Admin\ServiceProvider\AdminServiceProvider::class,
    // should be at bottom to provide correct order of routes
    Rosem\Kernel\ServiceProvider\RouteServiceProvider::class,
];
