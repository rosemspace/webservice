<?php

declare(strict_types=1);

namespace Rosem\Contract\App;

interface PackageInterface
{
    /**
     * Get the version number of the package.
     */
    public function getVersion(): string;

    public function getServiceProviders(): array;

    /**
     * Determine if the package is allowed to debug.
     */
    public function isAllowedToDebug(): bool;

    /**
     * Determine if the package version is a demo version.
     */
    public function isDemoVersion(): bool;

    /**
     * Determine if the package is running in the console.
     */
    public function isRunningInConsole(): bool;
}
