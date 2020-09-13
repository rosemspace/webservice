<?php

declare(strict_types=1);

namespace Rosem\Component\Authentication\Middleware;

use InvalidArgumentException;
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};
use Rosem\Contract\Authentication\AuthenticationExceptionInterface;
use Rosem\Contract\Authentication\{
    AuthenticationInterface,
    UserFactoryInterface,
    UserInterface
};

/**
 * Class HttpAuthenticationMiddleware.
 */
final class HttpAuthenticationMiddleware implements AuthenticationInterface, MiddlewareInterface
{
    /**
     * HTTP basic authentication type.
     */
    public const TYPE_BASIC = 'basic';

    /**
     * HTTP digest authentication type.
     */
    public const TYPE_DIGEST = 'digest';

    /**
     * Middleware delegate execution to.
     */
    public AbstractAuthenticationMiddleware $delegateMiddleware;

    /**
     * HttpAuthenticationMiddleware constructor.
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver,
        string $realm,
        string $nonce = '',
        string $type = 'digest'
    ) {
        switch ($type) {
            case 'basic':
                $this->delegateMiddleware = new BasicAuthenticationMiddleware(
                    $responseFactory,
                    $userFactory,
                    $userPasswordResolver,
                    $realm
                );

                break;
            case 'digest':
                $this->delegateMiddleware = new DigestAuthenticationMiddleware(
                    $responseFactory,
                    $userFactory,
                    $userPasswordResolver,
                    $realm,
                    $nonce
                );

                break;
            default:
                throw new InvalidArgumentException('Unknown HTTP authentication type.');
        }
    }

    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        return $this->delegateMiddleware->authenticate($request);
    }

    /**
     * @throws InvalidArgumentException
     * @throws AuthenticationExceptionInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->delegateMiddleware->process($request, $handler);
    }
}
