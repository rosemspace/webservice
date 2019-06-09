<?php

return [
    Rosem\Component\Http\Message\MessageFactoryProvider::class,
    Rosem\Component\Http\Server\EmitterProvider::class,
    Rosem\Component\Http\Server\MiddlewareServiceProvider::class,
    // FIXME: session provider
    Rosem\Component\Authentication\UserFactoryServiceProvider::class,
    Rosem\Component\Authentication\AuthenticationServiceProvider::class, // add session attr to the request
    Rosem\Component\Route\RouteServiceProvider::class, // use session on admin route, add route and handler middleware
    // FIXME: auth provider
    Rosem\Component\Admin\AdminServiceProvider::class, // add admin route
    Rosem\Component\Template\TemplateServiceProvider::class,
    Rosem\Component\App\AppServiceProvider::class, // add any route
    Rosem\Component\GraphQL\GraphQLServiceProvider::class,
    Rosem\Component\Access\Provider\AccessServiceProvider::class,
    Rosem\Component\Doctrine\ORMServiceProvider::class,
    //    Rosem\Doctrine\Provider\ORMServiceProvider::class,
    //    Rosem\Atlas\Provider\AtlasServiceProvider::class,
];
