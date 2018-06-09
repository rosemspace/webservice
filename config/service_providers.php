<?php

return [
    // FIXME: session provider
    Rosem\Authentication\Provider\BearerAuthenticationProvider::class, // add session attr to the request
    Rosem\Route\RouteServiceProvider::class, // use session on admin route, add route and handler middleware
    // FIXME: auth provider
    Rosem\Admin\Provider\AdminServiceProvider::class, // add admin route
    Rosem\App\AppServiceProvider::class, // add any route
//    Rosem\GraphQL\GraphQLServiceProvider::class,
//    Rosem\Doctrine\Provider\ORMServiceProvider::class,
//    Rosem\Atlas\Provider\AtlasServiceProvider::class,
//    Rosem\Access\Provider\AccessServiceProvider::class,
];
