<?php

namespace Rosem\Contract\App;

interface PackageInterface
{
    /**
     * Get the version number of the package.
     *
     * @return string
     */
    public function getVersion(): string;

    public function getServiceProviders(): array;

    /**
     * Determine if the package is allowed to debug.
     *
     * @return bool
     */
    public function isAllowedToDebug(): bool;

    /**
     * Determine if the package version is a demo version.
     *
     * @return bool
     */
    public function isDemoVersion(): bool;

    /**
     * Determine if the package is running in the console.
     *
     * @return bool
     */
    public function isRunningInConsole(): bool;
}
