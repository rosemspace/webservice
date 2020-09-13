<?php

declare(strict_types=1);

namespace Rosem\Contract\Authentication;

interface UserFactoryInterface
{
    public function createUser(string $identity): UserInterface;
}
