<?php

declare(strict_types=1);

namespace Rosem\Contract\Authentication;

interface UserInterface
{
    /**
     * Get the unique user identity (id, username, email address or ...)
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
     * @param null $default
     *
     * @return mixed
     */
    public function getDetail(string $name, $default = null);

    /**
     * Get all the details, if any
     */
    public function getDetails(): array;
}
