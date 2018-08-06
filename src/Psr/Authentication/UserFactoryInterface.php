<?php

namespace Rosem\Psr\Authentication;

interface UserFactoryInterface
{
    public function createUser(string $identity, array $roles = [], array $details = []): UserInterface;
}
