<?php

namespace Rosem\Authentication\Http\Server;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use function call_user_func;

class AuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Bearer';

    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function authenticate(ServerRequestInterface $request): ?string
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $username = $session->get($this->attribute);

        if ($username) {
            return $username;
        }

        if ($request->getMethod() !== 'POST') {
            return null;
        }

        $body = $request->getParsedBody();

        if (empty($body['username']) || empty($body['password'])) {
            return null;
        }

        $password = call_user_func($this->getPassword, $body['username']);

        if (!$password || $password !== $body['password']) {
            return null;
        }

        $session->set($this->attribute, $body['username']);

        return $body['username'];
    }

    public function createUnauthorizedResponse(): ResponseInterface
    {
        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/admin/login');
    }
}
