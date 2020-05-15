<?php

namespace PHPSTORM_META {
    registerArgumentsSet(
        'http_methods',
        'HEAD',
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
        'PURGE',
        'OPTIONS',
        'TRACE',
        'CONNECT',
    );
    expectedArguments(
        \Rosem\Contract\Route\HttpRouteCollectorInterface::addRoute(),
        0,
        argumentsSet('http_methods')
    );
}
