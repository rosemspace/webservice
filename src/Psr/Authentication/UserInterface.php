<?php

namespace Rosem\Psr\Authentication;

interface UserInterface extends IdentityInterface
{
    /**
     * Get all user roles
     *
     * @return string[]
     */
    public function getRoles(): array;

    /**
     * Get a detail $name if present, $default otherwise
     *
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function getDetail(string $name, $default = null);

    /**
     * Get all the details, if any
     *
     * @return array
     */
    public function getDetails(): array;
}
