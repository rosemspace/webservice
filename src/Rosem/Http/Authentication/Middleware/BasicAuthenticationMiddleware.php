<?php

namespace Rosem\Http\Authentication\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Http\Authentication\AbstractHttpAuthentication;

class BasicAuthenticationMiddleware extends AbstractHttpAuthentication implements MiddlewareInterface
{
    /**
     * Process a server request and return a response.
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
        return $this->createResponse($request, $requestHandler, sprintf('Basic realm="%s"', $this->realm));
    }

    /**
     * Check the user credentials and return the username or false.
     *
     * @param ServerRequestInterface $request
     *
     * @return mixed
     */
    public function authenticate(ServerRequestInterface $request)
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader)) {
            return null;
        }

        if (!preg_match(
            '/Basic (?<credentials>[a-zA-Z0-9\+\/\=]+)/',
            reset($authHeader),
            $match)
        ) {
            return null;
        }

        [$username, $password] = explode(':', base64_decode($match['credentials']), 2);

        //Check the user
        if (!isset($this->users[$username])) {
            return null;
        }

        if ($this->users[$username] !== $password) {
            return null;
        }

        return $username;
    }
}
