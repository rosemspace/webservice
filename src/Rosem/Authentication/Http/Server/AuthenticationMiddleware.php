<?php

namespace Rosem\Authentication\Http\Server;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use function call_user_func;
use Psrnext\Http\Factory\ResponseFactoryInterface;

class AuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Bearer';

    protected $redirectUri;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        callable $getPassword,
        string $redirectUri = '/login'
    ) {
        parent::__construct($responseFactory, $getPassword);

        $this->redirectUri = $redirectUri;
    }

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

        if ($request->getMethod() !== RequestMethodInterface::METHOD_POST) {
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
        return $this->responseFactory->createResponse(StatusCodeInterface::STATUS_FOUND)
            ->withHeader('Location', $this->redirectUri);
    }
}
