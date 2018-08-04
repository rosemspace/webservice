<?php

namespace Rosem\Authentication\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Rosem\Authentication\AuthenticationInterface;

abstract class AbstractAuthenticationMiddleware implements MiddlewareInterface, AuthenticationInterface
{
    /**
     * @var string|null
     */
    protected static $userIdentityAttribute = 'auth.user.identity';

    /**
     * @var ResponseFactoryInterface
     */
    protected $responseFactory;

    /**
     * @var callable The function to get a password by a username.
     */
    protected $getPassword;

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param callable                 $userPasswordGetter function (string $username) {...}
     * @param string                   $userIdentityAttribute
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        callable $userPasswordGetter,
        string $userIdentityAttribute = 'auth.user.identity'
    ) {
        $this->responseFactory = $responseFactory;
        $this->getPassword = $userPasswordGetter;
        static::$userIdentityAttribute = $userIdentityAttribute;
    }

    /**
     * Get the name of the user identity attribute.
     *
     * @return string
     */
    public static function getUserIdentityAttribute(): string
    {
        return static::$userIdentityAttribute;
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
        $userIdentity = $this->authenticate($request);

        if (null === $userIdentity) {
            return $this->createUnauthorizedResponse($request);
        }

        if (null !== static::$userIdentityAttribute) {
            $request = $request->withAttribute(static::$userIdentityAttribute, $userIdentity);
        }

        return $requestHandler->handle($request);
    }
}
