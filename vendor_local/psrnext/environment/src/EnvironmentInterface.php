<?php

namespace Psrnext\Environment;

interface EnvironmentInterface
{
    public function load(): array;

    public function get(string $key): string;

    public function getMode(): string;

    public function isDevelopmentMode(): bool;

    public function isMaintenanceMode(): bool;

    public function isProductionMode(): bool;

    public function isStagingMode(): bool;

    public function isTestingMode(): bool;
}
