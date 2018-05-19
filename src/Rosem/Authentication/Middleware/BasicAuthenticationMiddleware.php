<?php

namespace Rosem\Authentication\Middleware;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use function call_user_func;

class BasicAuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Basic';

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
        $password = call_user_func($this->getPassword, $username, $request);

        if (!$password || $password !== $enteredPassword) {
            return null;
        }

        return $username;
    }

    /**
     * Create unauthorized response.
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function createUnauthorizedResponse(): ResponseInterface
    {
        return $this->responseFactory->createResponse(401)
            ->withHeader(
                'WWW-Authenticate',
                self::AUTHORIZATION_HEADER_PREFIX . ' realm="' . $this->realm . '"'
            );
    }
}
