<?php

namespace Rosem\Contract\Authentication;

interface UserInterface
{
    /**
     * Get the unique user identity (id, username, email address or ...)
     *
     * @return string
     */
    public function getIdentity(): string;

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
