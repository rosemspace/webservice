<?php

return [
    // Need to create server requests and responses
    Rosem\Component\Http\Provider\MessageFactoryProvider::class,
    // Need to emit a response to a client
    Rosem\Component\Http\Provider\EmitterProvider::class,
    // Need to allow middleware usage
    Rosem\Component\Http\Provider\MiddlewareProvider::class,
    // FIXME: session provider
    Rosem\Component\Authentication\Provider\UserFactoryProvider::class,
    Rosem\Component\Authentication\Provider\AuthenticationProvider::class, // add session attr to the request
    Rosem\Component\Authentication\Provider\HttpAuthenticationProvider::class, // add session attr to the request
    // use session on admin route, add route and handler middleware
    Rosem\Component\Route\Provider\RouteServiceProvider::class,
    // FIXME: auth provider
    Rosem\Component\Template\TemplateServiceProvider::class,
    Rosem\Component\GraphQL\GraphQLServiceProvider::class,
    Rosem\Component\Access\Provider\AccessServiceProvider::class,
    Rosem\Component\Admin\AdminServiceProvider::class, // add admin route
    Rosem\Component\App\AppServiceProvider::class, // add any route
];
