<?php

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
     * @return string
     * TODO: move to Filesystem\DirectoryList class (+ getRoot getPublicPath getPath)
     */
    public function getRootDir(): string;

    /**
     * Get the version number of the application.
     *
     * @return string
     */
    public function getVersion(): string;

    /**
     * Get the environment of the application.
     *
     * @return string
     */
    public function getEnvironment(): string;

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Determine if the application is allowed to debug.
     *
     * @return bool
     */
    public function isAllowedToDebug(): bool;

    /**
     * Determine if the application is down for maintenance.
     *
     * @return bool
     */
    public function isDownForMaintenance(): bool;

    /**
     * Determine if the application version is a demo version.
     *
     * @return bool
     */
    public function isDemoVersion(): bool;

    /**
     * Determine if the application is running in the console.
     *
     * @return bool
     */
    public function isRunningInConsole(): bool;
}
