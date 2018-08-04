<?php

namespace Rosem\Authentication\Http\Server;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use function call_user_func;
use function count;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use function strlen;

/** @noinspection LongInheritanceChainInspection */
class DigestAuthenticationMiddleware extends BasicAuthenticationMiddleware
{
    public const PARAM_NONCE = 'auth.nonce';

    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Digest';

    /**
     * Authorization header needed parts.
     */
    private const AUTHORIZATION_HEADER_NEEDED_PARTS = [
        'username', 'nonce', 'uri', 'response', 'qop', 'nc', 'cnonce',
    ];

    /**
     * @var string|null
     */
    protected static $nonceAttribute = 'auth.nonce';

    /**
     * @var string|null The nonce value
     */
    protected $nonce;

    public function __construct(
        ResponseFactoryInterface $responseFactory,
        callable $userPasswordGetter,
        string $realm = 'Login',
        string $nonce = ''
    ) {
        parent::__construct($responseFactory, $userPasswordGetter, $realm);

        $this->nonce = $nonce ?: uniqid('', true);
    }

    /**
     * Set the name of the nonce attribute.
     *
     * @param string $attribute
     *
     * @throws \LogicException
     */
    public static function setNonceAttribute(string $attribute): void
    {
        self::setAttribute('realmAttribute', $attribute);
    }

    /**
     * Get name of the nonce attribute.
     *
     * @return string
     */
    public static function getNonceAttribute(): string
    {
        return static::$realmAttribute;
    }

    /**
     * Get the nonce value.
     *
     * @param ServerRequestInterface $request
     *
     * @return string
     */
    public function getNonce(ServerRequestInterface $request): string
    {
        return $request->getAttribute(self::PARAM_NONCE) ?: $this->nonce;
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

        /** @noinspection NotOptimalIfConditionsInspection */
        if (empty($authHeader)
            || strpos(reset($authHeader), self::AUTHORIZATION_HEADER_PREFIX . ' ') !== 0
            || !preg_match_all(
                '/('
                . implode('|', self::AUTHORIZATION_HEADER_NEEDED_PARTS)
                . ')=(?|\'([^\']+?)\'|"([^"]+?)"|([^\s,]+))/',
                substr(reset($authHeader), strlen(self::AUTHORIZATION_HEADER_PREFIX) + 1),
                $matches,
                PREG_SET_ORDER
            )
            || count($matches) !== count(self::AUTHORIZATION_HEADER_NEEDED_PARTS)
        ) {
            return null;
        }

        $authorization = [];

        /** @var array[] $matches */
        foreach ($matches as $match) {
            $authorization[$match[1]] = $match[2];
        }

        $password = call_user_func($this->getPassword, $authorization['username'], $request);

        if (!$password
            || $authorization['response'] !== md5(sprintf(
                '%s:%s:%s:%s:%s:%s',
                md5("{$authorization['username']}:$this->realm:$password"),
                $authorization['nonce'],
                $authorization['nc'],
                $authorization['cnonce'],
                $authorization['qop'],
                md5($request->getMethod() . ':' . $authorization['uri'])
            ))
        ) {
            return null;
        }

        return $authorization['username'];
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
        $realm = $this->getRealm($request);

        return $this->responseFactory->createResponse(401)
            ->withHeader(
                'WWW-Authenticate',
                sprintf(
                    self::AUTHORIZATION_HEADER_PREFIX . ' realm="%s",qop="auth",nonce="%s",opaque="%s"',
                    $realm,
                    $this->getNonce($request),
                    md5($realm)
                )
            );
    }
}
