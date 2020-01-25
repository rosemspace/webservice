<?php

return [
    // Need to create server requests and responses
    Rosem\Component\Http\Message\MessageFactoryProvider::class,
    // Need to emit a response to a client
    Rosem\Component\Http\Server\EmitterProvider::class,
    // Need to allow middlewares usage
    Rosem\Component\Http\Server\MiddlewareServiceProvider::class,
    // FIXME: session provider
    Rosem\Component\Authentication\UserFactoryServiceProvider::class,
    Rosem\Component\Authentication\AuthenticationServiceProvider::class, // add session attr to the request
    Rosem\Component\Authentication\HttpAuthenticationServiceProvider::class, // add session attr to the request
    // use session on admin route, add route and handler middleware
    Rosem\Component\Route\RouteServiceProvider::class,
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
