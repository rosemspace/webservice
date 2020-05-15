<?php

namespace PHPSTORM_META {
    registerArgumentsSet(
        'fig_http_methods',
         \Fig\Http\Message\RequestMethodInterface::METHOD_HEAD,
         \Fig\Http\Message\RequestMethodInterface::METHOD_GET,
         \Fig\Http\Message\RequestMethodInterface::METHOD_POST,
         \Fig\Http\Message\RequestMethodInterface::METHOD_PUT,
         \Fig\Http\Message\RequestMethodInterface::METHOD_PATCH,
         \Fig\Http\Message\RequestMethodInterface::METHOD_DELETE,
         \Fig\Http\Message\RequestMethodInterface::METHOD_PURGE,
         \Fig\Http\Message\RequestMethodInterface::METHOD_OPTIONS,
         \Fig\Http\Message\RequestMethodInterface::METHOD_TRACE,
         \Fig\Http\Message\RequestMethodInterface::METHOD_CONNECT,
    );
    expectedArguments(
        \Rosem\Contract\Route\HttpRouteCollectorInterface::addRoute(),
        0,
        argumentsSet('fig_http_methods')
    );
}
