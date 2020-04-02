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
     * Determine the current environment.
     *
     * @param string $env
     *
     * @return bool
     */
    public function isEnvironment(string $env): bool;

    /**
     * Get the current application locale.
     *
     * @return string
     */
    public function getLocale(): string;

    /**
     * Determine the current locale.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function isLocale(string $locale): bool;

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
