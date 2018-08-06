<?php

namespace Rosem\Psr\Authentication;

interface IdentityInterface
{
    /**
     * Get the unique user identity (id, username, email address or ...)
     *
     * @return string
     */
    public function getIdentity(): string;
}
