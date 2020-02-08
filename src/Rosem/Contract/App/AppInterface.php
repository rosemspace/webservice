<?php

namespace Rosem\Contract\App;

use Psr\Container\ContainerInterface;
use Rosem\Contract\Http\Server\MiddlewareRunnerInterface;

interface AppInterface extends ContainerInterface, MiddlewareRunnerInterface
{
    /**
     * Get an application root directory.
     *
     * @return string
     */
    public function getRootDir(): string;

    /**
     * Get an application version.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Get an application environment.
     *
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * Check if the application is allowed to debug.
     *
     * @return bool
     */
    public function isAllowedToDebug(): bool;

    /**
     * Check if the application is down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance(): bool;

    /**
     * Check if the application version is a demo version.
     *
     * @return bool
     */
    public function isDemoVersion(): bool;
}
