<?php

namespace Rosem\Component\Authentication\Http\Server;

use Psr\Http\Message\{
    ResponseFactoryInterface, ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Rosem\Contract\Authentication\{
    AuthenticationInterface, UserFactoryInterface, UserInterface
};

abstract class AbstractAuthenticationMiddleware implements MiddlewareInterface, AuthenticationInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var UserFactoryInterface
     */
    protected $userFactory;

    /**
     * The function to get a password by a username.
     *
     * @var callable
     */
    protected $userPasswordResolver;

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param UserFactoryInterface     $userFactory
     * @param callable                 $userPasswordResolver
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver
    ) {
        $this->responseFactory = $responseFactory;
        $this->userFactory = $userFactory;
        $this->userPasswordResolver = $userPasswordResolver;
    }

    private function setPasswordResolver(callable $resolver): void
    {
        $this->userPasswordResolver = $resolver;
    }

    /**
     * @param callable $resolver
     *
     * @return static
     */
    public function withPasswordResolver(callable $resolver)
    {
        $new = clone $this;
        $new->setPasswordResolver($resolver);

        return $new;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $requestHandler
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
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
}
