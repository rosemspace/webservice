<?php

namespace Rosem\Authentication\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Authentication\AbstractAuthentication;

abstract class AbstractAuthenticationMiddleware extends AbstractAuthentication implements MiddlewareInterface
{
    /**
     * Create unauthorized response.
     *
     * @return ResponseInterface
     */
    abstract protected function createUnauthorizedResponse(): ResponseInterface;

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
            return $this->createUnauthorizedResponse();
        }

        if (null !== $this->attribute) {
            $request = $request->withAttribute($this->attribute, $userIdentity);
        }

        return $requestHandler->handle($request);
    }
}
