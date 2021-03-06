<?php

declare(strict_types=1);

namespace Rosem\Component\Authentication\Middleware;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use InvalidArgumentException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Rosem\Contract\Authentication\{
    UserFactoryInterface,
    UserInterface
};

use function call_user_func;
use function count;
use function strlen;

/** @noinspection LongInheritanceChainInspection */

class DigestAuthenticationMiddleware extends BasicAuthenticationMiddleware
{
    /**
     * Authorization header prefix.
     */
    private const AUTHORIZATION_HEADER_PREFIX = 'Digest';

    /**
     * Authorization header needed parts.
     */
    private const AUTHORIZATION_HEADER_NEEDED_PARTS = [
        'username',
        'nonce',
        'uri',
        'response',
        'qop',
        'nc',
        'cnonce',
    ];

    /**
     * @var string|null The nonce value
     */
    protected ?string $nonce;

    /**
     * DigestAuthenticationMiddleware constructor.
     */
    public function __construct(
        ResponseFactoryInterface $responseFactory,
        UserFactoryInterface $userFactory,
        callable $userPasswordResolver,
        string $realm,
        string $nonce = ''
    ) {
        parent::__construct($responseFactory, $userFactory, $userPasswordResolver, $realm);

        $this->nonce = $nonce ?: uniqid('', true);
    }

    /**
     * Check the user credentials and return the username or false.
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader)
            || strpos(reset($authHeader), self::AUTHORIZATION_HEADER_PREFIX . ' ') !== 0
            || ! preg_match_all(
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

        $identity = $authorization['username'];
        $password = call_user_func($this->userPasswordResolver, $identity, $request);

        if (! $password
            || $authorization['response'] !== md5(
                sprintf(
                    '%s:%s:%s:%s:%s:%s',
                    md5("{$authorization['username']}:{$this->realm}:${password}"),
                    $authorization['nonce'],
                    $authorization['nc'],
                    $authorization['cnonce'],
                    $authorization['qop'],
                    md5($request->getMethod() . ':' . $authorization['uri'])
                )
            )
        ) {
            return null;
        }

        return $this->userFactory->createUser($identity);
    }

    /**
     * Create unauthorized response.
     *
     * @throws InvalidArgumentException
     */
    public function createUnauthorizedResponse(): ResponseInterface
    {
        $realm = $this->realm;

        return $this->responseFactory->createResponse(StatusCode::STATUS_UNAUTHORIZED)
            ->withHeader(
                'WWW-Authenticate',
                sprintf(
                    self::AUTHORIZATION_HEADER_PREFIX .
                    ' realm="%s",qop="auth",nonce="%s",opaque="%s"',
                    $realm,
                    $this->nonce,
                    md5($realm)
                )
            );
    }
}
