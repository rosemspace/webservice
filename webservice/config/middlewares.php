<?php

return [ //TODO: reverse
    \Rosem\App\Http\Middleware\RequestHandlerMiddleware::class,
    \Rosem\App\Http\Middleware\RouteMiddleware::class,
    \Rosem\Http\Middleware\GraphQLMiddleware::class,
    //auth
];
