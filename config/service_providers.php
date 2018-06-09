<?php

return [
    Rosem\Authentication\Provider\BearerAuthenticationProvider::class,
    Rosem\Route\RouteServiceProvider::class,
    Rosem\Admin\Provider\AdminServiceProvider::class,
    Rosem\App\AppServiceProvider::class,
    Rosem\GraphQL\GraphQLServiceProvider::class,
    Rosem\Doctrine\Provider\ORMServiceProvider::class,
//    Rosem\Atlas\Provider\AtlasServiceProvider::class,
    Rosem\Access\Provider\AccessServiceProvider::class,
];
