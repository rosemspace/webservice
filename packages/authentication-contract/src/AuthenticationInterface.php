<?php

namespace Rosem\Contract\Authentication;

use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationInterface
{
    /**
     * Authenticate the user request.
     *
     * @param ServerRequestInterface $request
     *
     * @return UserInterface|null
     * @throws AuthenticationExceptionInterface
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface;
}
