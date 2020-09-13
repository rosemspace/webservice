<?php

declare(strict_types=1);

namespace Rosem\Contract\Authentication;

use Psr\Http\Message\ServerRequestInterface;

interface AuthenticationInterface
{
    /**
     * Authenticate the user request.
     *
     * @throws AuthenticationExceptionInterface
     */
    public function authenticate(ServerRequestInterface $request): ?UserInterface;
}
