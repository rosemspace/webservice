<?php

namespace Rosem\Psr\Environment;

interface EnvironmentInterface
{
    public function load(): array;

    public function get(string $key): string;

    public function getMode(): string;

    public function isMaintenanceMode(): bool; // show maintenance page

    public function isDemoMode(): bool; // allow only partial functionality

    public function isDevelopmentMode(): bool; // show errors, no caches, show debug info

    public function isStagingMode(): bool; // show errors, use caches, no debug info

    public function isProductionMode(): bool; // hide errors, use caches, no debug info
}
