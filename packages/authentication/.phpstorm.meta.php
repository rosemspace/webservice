<?php

namespace PHPSTORM_META {
    override(
        \Psr\Http\Message\ServerRequestInterface::getAttribute(0),
        map(
            [
                \PSR7Sessions\Storageless\Http\SessionMiddleware::SESSION_ATTRIBUTE =>
                    \PSR7Sessions\Storageless\Session\SessionInterface::class,
                'session' => \PSR7Sessions\Storageless\Session\SessionInterface::class,
            ]
        )
    );
    expectedArguments(
        \Psr\Http\Message\ServerRequestInterface::getAttribute(),
        0,
        \PSR7Sessions\Storageless\Http\SessionMiddleware::SESSION_ATTRIBUTE
    );
    expectedArguments(
        \Rosem\Component\Authentication\Middleware\HttpAuthenticationMiddleware::__construct(),
        5,
        \Rosem\Component\Authentication\Middleware\HttpAuthenticationMiddleware::TYPE_BASIC,
        \Rosem\Component\Authentication\Middleware\HttpAuthenticationMiddleware::TYPE_DIGEST
    );
}
