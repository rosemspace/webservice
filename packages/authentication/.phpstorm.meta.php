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
}
