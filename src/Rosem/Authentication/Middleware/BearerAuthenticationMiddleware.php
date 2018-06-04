<?php

namespace Rosem\Authentication\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;

class BearerAuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Bearer';

    protected $attribute = 'user_id';

    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function authenticate(ServerRequestInterface $request): ?string
    {
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
//        $session->set('counter', $session->get('counter', 0) + 1);

        $username = $session->get('userId');

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

        $session->set('userId', $body['username']);

        return $body['username'];
    }

    public function createUnauthorizedResponse(): ResponseInterface
    {
        return $this->responseFactory->createResponse(302)
            ->withHeader('Location', '/admin/login');
    }
}
