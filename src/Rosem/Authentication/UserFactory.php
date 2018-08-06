<?php

namespace Rosem\Authentication;

use Rosem\Psr\Authentication\UserFactoryInterface;
use Rosem\Psr\Authentication\UserInterface;

/**
 * Generic implementation of UserInterface.
 * This implementation is modeled as immutable, to prevent propagation of
 * user state changes.
 * We recommend that any details injected are serializable.
 */
final class UserFactory implements UserFactoryInterface
{
    public function createUser(string $identity, array $roles = [], array $details = []): UserInterface
    {
        return new User($identity, $roles, $details);
    }
}
