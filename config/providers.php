<?php

return [
    Rosem\Component\Http\Provider\KernelServiceProvider::class,
    // Need to write an HTML into a response
    Rosem\Component\Template\TemplateServiceProvider::class,
    // FIXME: session provider
    Rosem\Component\Authentication\Provider\UserFactoryProvider::class,
    Rosem\Component\Authentication\Provider\AuthenticationProvider::class, // add session attr to the request
    Rosem\Component\Authentication\Provider\HttpAuthenticationProvider::class, // add session attr to the request
    // Use session on admin route, add route and handler middleware
    Rosem\Component\Route\Provider\RouteServiceProvider::class,
    // FIXME: auth provider
    Rosem\Component\GraphQL\GraphQLServiceProvider::class,
    Rosem\Component\Access\Provider\AccessServiceProvider::class,
    Rosem\Component\Admin\AdminServiceProvider::class, // add admin route
    Rosem\Component\App\AppServiceProvider::class, // add any route
];
