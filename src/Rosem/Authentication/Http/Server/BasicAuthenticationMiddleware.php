<?php

namespace Rosem\Authentication\Http\Server;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Message\ResponseFactoryInterface;
use function call_user_func;

class BasicAuthenticationMiddleware extends AbstractAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Basic';

    /**
     * @var string|null
     */
    protected static $realmAttribute = 'auth.realm';

    /**
     * @var string
     */
    protected $realm;

    /**
     * Define de users.
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param callable                 $userPasswordGetter function (string $username) {...}
     * @param string                   $realm
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        callable $userPasswordGetter,
        string $realm = 'Login'
    ) {
        parent::__construct($responseFactory, $userPasswordGetter);

        $this->realm = $realm;
    }

    /**
     * Set the name of the realm attribute.
     *
     * @param string $attribute
     *
     * @throws \LogicException
     */
    public static function setRealmAttribute(string $attribute): void
    {
        self::setAttribute('realmAttribute', $attribute);
    }

    /**
     * Get name of the realm attribute.
     *
     * @return string
     */
    public static function getRealmAttribute(): string
    {
        return static::$realmAttribute;
    }

    /**
     * Get the realm value.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getRealm(ServerRequestInterface $request): string
    {
        return $request->getAttribute(static::$realmAttribute) ?: $this->realm;
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
        $password = call_user_func($this->getPassword, $username, $request);

        if (!$password || $password !== $enteredPassword) {
            return null;
        }

        return $username;
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
        return $this->responseFactory->createResponse(401)
            ->withHeader(
                'WWW-Authenticate',
                self::AUTHORIZATION_HEADER_PREFIX . ' realm="' . $this->getRealm($request) . '"'
            );
    }
}
