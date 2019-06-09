<?php

namespace Rosem\Component\Authentication;

use Rosem\Contract\Authentication\{
    UserFactoryInterface, UserInterface
};
use function call_user_func;

/**
 * Generic implementation of UserInterface.
 * This implementation is modeled as immutable, to prevent propagation of
 * user state changes.
 * We recommend that any details injected are serializable.
 */
final class UserFactory implements UserFactoryInterface
{
    /**
     * The function to get user roles by a username.
     *
     * @var callable
     */
    private $userRolesResolver;

    /**
     * The function to get user details by a username.
     *
     * @var callable
     */
    private $userDetailsResolver;

    /**
     * UserFactory constructor.
     *
     * @param callable|null $userRolesResolver
     * @param callable|null $userDetailsResolver
     */
    public function __construct(
        ?callable $userRolesResolver = null,
        ?callable $userDetailsResolver = null
    ) {
        $this->userRolesResolver = $userRolesResolver ?: function () {
            return [];
        };
        $this->userDetailsResolver = $userDetailsResolver ?: function () {
            return [];
        };
    }

    public function createUser(string $identity): UserInterface
    {
        return new User(
            $identity,
            call_user_func($this->userRolesResolver, $identity),
            call_user_func($this->userDetailsResolver, $identity)
        );
    }
}
