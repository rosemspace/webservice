<?php

namespace Psrnext\Environment;

abstract class AbstractEnvironment implements EnvironmentInterface
{
    public function isDevelopmentMode(): bool
    {
        return $this->getMode() === EnvironmentMode::DEVELOPMENT;
    }

    public function isMaintenanceMode(): bool
    {
        return $this->getMode() === EnvironmentMode::MAINTENANCE;
    }

    public function isProductionMode(): bool
    {
        return $this->getMode() === EnvironmentMode::PRODUCTION;
    }

    public function isStagingMode(): bool
    {
        return $this->getMode() === EnvironmentMode::STAGING;
    }

    public function isTestingMode(): bool
    {
        return $this->getMode() === EnvironmentMode::TESTING;
    }
}
