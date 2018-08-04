<?php

namespace Rosem\Authentication;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};

interface AuthenticationInterface
{
    /**
     * @param ServerRequestInterface $request
     *
     * @return string|null
     */
    public function authenticate(ServerRequestInterface $request): ?string;

    /**
     * Create unauthorized response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function createUnauthorizedResponse(ServerRequestInterface $request): ResponseInterface;
}
