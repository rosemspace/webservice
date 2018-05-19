<?php

namespace Rosem\Http\Authentication\Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Http\Authentication\AbstractHttpAuthentication;

class DigestAuthenticationMiddleware extends AbstractHttpAuthentication implements MiddlewareInterface
{
    /**
     * @var string|null The nonce value
     */
    private $nonce;

    /**
     * Set the nonce value.
     *
     * @param string $nonce
     */
    public function setNonce(string $nonce): void
    {
        $this->nonce = $nonce;
    }

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
        return $this->createResponse($request, $requestHandler, sprintf(
            'Digest realm="%s",qop="auth",nonce="%s",opaque="%s"',
            $this->realm,
            $this->nonce ?: uniqid(),
            md5($this->realm)
        ));
    }

    /**
     * Check the user credentials and return the username or false.
     *
     * @param ServerRequestInterface $request
     *
     * @return mixed
     */
    public function authenticate(ServerRequestInterface $request)
    {
        $authHeader = $request->getHeader('Authorization');

        if (empty($authHeader) || strpos(reset($authHeader), 'Digest ') !== 0) {
            return null;
        }

        $neededParts = [
            'nonce'    => 1,
            'nc'       => 1,
            'cnonce'   => 1,
            'qop'      => 1,
            'username' => 1,
            'uri'      => 1,
            'response' => 1,
        ];

        if (!preg_match_all(
            '/('
            . implode('|', array_keys($neededParts))
            . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))/',
            substr(reset($authHeader), 7),
            $matches,
            PREG_SET_ORDER
        )) {
            return null;
        }

        $authorization = [];

        /** @var array[] $matches */
        foreach ($matches as $match) {
            $authorization[$match[1]] = $match[3] ?: $match[4];
            unset($neededParts[$match[1]]);
        }

        if (!empty($neededParts)) {
            return null;
        }

        //Check whether user exists
        if (!isset($this->users[$authorization['username']])) {
            return null;
        }

        if (!$this->isValid($authorization, $request->getMethod(), $this->users[$authorization['username']])) {
            return null;
        }

        return $authorization['username'];
    }

    /**
     * Validates the authorization.
     *
     * @param array  $authorization
     * @param string $method
     * @param string $password
     *
     * @return bool
     */
    private function isValid(array $authorization, string $method, string $password): bool
    {
        $validResponse = md5(sprintf(
            '%s:%s:%s:%s:%s:%s',
            md5(sprintf('%s:%s:%s', $authorization['username'], $this->realm, $password)),
            $authorization['nonce'],
            $authorization['nc'],
            $authorization['cnonce'],
            $authorization['qop'],
            md5(sprintf('%s:%s', $method, $authorization['uri']))
        ));

        return $authorization['response'] === $validResponse;
    }
}
