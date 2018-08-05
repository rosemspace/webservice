<?php

namespace Rosem\Authentication\Http\Server;

use Fig\Http\Message\RequestMethodInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Psr\Http\Message\ResponseFactoryInterface;
use function call_user_func;

class AuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Bearer';

    protected static $loginUriAttribute = 'auth.login.uri';

    protected $loginUri;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        callable $userPasswordGetter,
        string $userIdentityAttribute = 'auth.user.identity',
        string $loginUri = '/login',
        string $loginUriAttribute = 'auth.login.uri'
    ) {
        parent::__construct($responseFactory, $userPasswordGetter, $userIdentityAttribute);

        $this->loginUri = $loginUri;
        static::$loginUriAttribute = $loginUriAttribute;
    }

    /**
     * Get name of the realm attribute.
     *
     * @return string
     */
    public static function getLoginUriAttribute(): string
    {
        return static::$loginUriAttribute;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $requestHandler
    ): ResponseInterface {
        $userIdentityAttribute = static::$userIdentityAttribute;
        $loginUri = $request->getAttribute(static::$loginUriAttribute) ?: $this->loginUri;
        $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
        $username = $session->get($userIdentityAttribute);

        if (!$username) {
            if ($request->getMethod() === RequestMethodInterface::METHOD_POST) {
                $body = $request->getParsedBody();

                if (!empty($body['username']) && !empty($body['password'])) {
                    $password = call_user_func($this->getPassword, $body['username']);

                    if ($password && $password === $body['password']) {
                        $session->set($userIdentityAttribute, $body['username']);
                        $username = $body['username'];
                    }
                }
            }

            if (!$username) {
                if ($request->getMethod() === RequestMethodInterface::METHOD_GET
                    && $request->getUri()->getPath() === $loginUri
                ) {
                    return $requestHandler->handle($request);
                }

                return $this->responseFactory
                    ->createResponse(StatusCodeInterface::STATUS_FOUND)
                    ->withHeader('Location', $loginUri);
            }
        }

        $request = $request->withAttribute($userIdentityAttribute, $username);

        return $requestHandler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function authenticate(ServerRequestInterface $request): ?string
    {
        return '';
    }

    /**
     * Create unauthorized response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function createUnauthorizedResponse(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(StatusCodeInterface::STATUS_FOUND)
            ->withHeader('Location', $this->loginUri); // TODO: get login uri from request
    }
}
