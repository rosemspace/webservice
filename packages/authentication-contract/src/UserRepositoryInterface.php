<?php

declare(strict_types=1);

namespace Rosem\Contract\Authentication;

interface UserRepositoryInterface
{
    public function identifyUser(string $identity): ?UserInterface;
}
