<?php

namespace Rosem\Http\Authentication\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Http\Authentication\AbstractHttpAuthentication;
use function call_user_func;
use function count;
use function strlen;

class DigestAuthenticationMiddleware extends AbstractHttpAuthentication implements MiddlewareInterface
{
    private const AUTHORIZATION_HEADER_PREFIX = 'Digest';

    private const AUTHORIZATION_HEADER_NEEDED_PARTS = [
        'username', 'nonce', 'uri', 'response', 'qop', 'nc', 'cnonce'
    ];

    /**
     * @var string|null The nonce value
     */
    private $nonce;

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
            sprintf(
                self::AUTHORIZATION_HEADER_PREFIX . ' realm="%s",qop="auth",nonce="%s",opaque="%s"',
                $this->realm,
                $this->nonce ?: uniqid('', true),
                md5($this->realm)
            )
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

        $password = call_user_func($this->getPassword, $authorization['username']);

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
     * Set the nonce value.
     *
     * @param string $nonce
     */
    public function setNonce(string $nonce): void
    {
        $this->nonce = $nonce;
    }
}
