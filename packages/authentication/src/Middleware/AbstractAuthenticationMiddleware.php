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

abstract class AbstractAuthenticationMiddleware implements MiddlewareInterface, AuthenticationInterface
{
    protected ResponseFactoryInterface $responseFactory;

    protected UserFactoryInterface $userFactory;

    /**
     * The function to get a password by a username.
     *
     * @var callable
     */
    protected $userPasswordResolver;

    /**
     * Define de users.
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        ?callable $userPasswordResolver
    ) {
        $this->responseFactory = $responseFactory;
        $this->userFactory = $userFactory;

        if ($userPasswordResolver !== null) {
            $this->userPasswordResolver = $userPasswordResolver;
        }
    }

    /**
     * @return static
     */
    public function withPasswordResolver(callable $resolver): self
    {
        $new = clone $this;
        $new->setPasswordResolver($resolver);

        return $new;
    }

    /**
     * @throws InvalidArgumentException
     * @throws AuthenticationExceptionInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $user = $this->authenticate($request);

        if ($user) {
            return $requestHandler->handle($request->withAttribute(UserInterface::class, $user));
        }

        return $this->createUnauthorizedResponse();
    }

    /**
     * Create unauthorized response.
     */
    abstract public function createUnauthorizedResponse(): ResponseInterface;

    private function setPasswordResolver(callable $resolver): void
    {
        $this->userPasswordResolver = $resolver;
    }
}
