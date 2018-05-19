<?php

namespace Rosem\Http\Authentication\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Http\Authentication\AbstractHttpAuthentication;
use function call_user_func;

class BasicAuthenticationMiddleware extends AbstractHttpAuthentication implements MiddlewareInterface
{
    private const AUTHORIZATION_HEADER_PREFIX = 'Basic';

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
        return $this->createResponse(
            $request,
            $requestHandler,
            self::AUTHORIZATION_HEADER_PREFIX . ' realm="' . $this->realm . '"'
        );
    }

    /**
     * Check the user credentials and return the username or false.
     *
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function authenticate(ServerRequestInterface $request): ?string
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader)) {
            return null;
        }

        if (!preg_match(
            '/' . self::AUTHORIZATION_HEADER_PREFIX . ' (?<credentials>[a-zA-Z0-9\+\/\=]+)/',
            reset($authHeader),
            $match)
        ) {
            return null;
        }

        [$username, $enteredPassword] = explode(':', base64_decode($match['credentials']), 2);
        $password = call_user_func($this->getPassword, $username);

        if (!$password || $password !== $enteredPassword) {
            return null;
        }

        return $username;
    }
}
