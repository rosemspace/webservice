<?php

namespace Rosem\Authentication\Http\Server;

use Psr\Http\Message\{
    ResponseFactoryInterface, ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Rosem\Psr\Authentication\AuthenticationInterface;
use Rosem\Psr\Authentication\UserFactoryInterface;
use Rosem\Psr\Authentication\UserInterface;

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
     * The function to get user roles by a username.
     *
     * @var callable
     */
    protected $userRolesResolver;

    /**
     * The function to get user details by a username.
     *
     * @var callable
     */
    protected $userDetailsResolver;

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param UserFactoryInterface     $userFactory
     * @param callable                 $userPasswordResolver
     * @param callable|null            $userRolesResolver
     * @param callable|null            $userDetailsResolver
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver,
        ?callable $userRolesResolver = null,
        ?callable $userDetailsResolver = null
    ) {
        $this->responseFactory = $responseFactory;
        $this->userFactory = $userFactory;
        $this->userPasswordResolver = $userPasswordResolver;
        $this->userRolesResolver = $userRolesResolver ?: function () {
            return [];
        };
        $this->userDetailsResolver = $userDetailsResolver ?: function () {
            return [];
        };
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
