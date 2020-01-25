<?php

namespace Rosem\Contract\App;

use Psr\Container\ContainerInterface;

interface AppInterface extends ContainerInterface
{
    /**
     * Get an application environment.
     *
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * Check if an application is on debugging.
     *
     * @return bool
     */
    public function onDebugging(): bool;
}
