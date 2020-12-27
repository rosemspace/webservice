<?php

declare(strict_types=1);

namespace Rosem\Contract\App;

interface AppInterface
{
    /**
     * Run the application.
     */
    public function run(): bool;

    /**
     * Get the root directory of the application.
     *
     * @param int $levelsUp levels to go up from the public directory (document root)
     *
     * @return string
     * TODO: move to Filesystem\DirectoryList class (+ getRoot getPublicPath getPath)
     */
    public function getRootDir(int $levelsUp = 1): string;

    /**
     * Get the version number of the application.
     */
    public function getVersion(): string;

    /**
     * Get the environment of the application.
     */
    public function getEnvironment(): string;

    /**
     * Get the current application locale.
     */
    public function getLocale(): string;

    /**
     * Determine if the application is allowed to debug.
     */
    public function isAllowedToDebug(): bool;

    /**
     * Determine if the application is down for maintenance.
     */
    public function isDownForMaintenance(): bool;

    /**
     * Determine if the application version is a demo version.
     */
    public function isDemoVersion(): bool;

    /**
     * Determine if the application is running in the console.
     */
    public function isRunningInConsole(): bool;
}
