<?php

namespace Psrnext\Environment;

abstract class AbstractEnvironment implements EnvironmentInterface
{
    public function isMaintenanceMode(): bool
    {
        return $this->getMode() === EnvironmentMode::MAINTENANCE;
    }

    public function isDemoMode(): bool
    {
        return $this->getMode() === EnvironmentMode::DEMO;
    }

    public function isDevelopmentMode(): bool
    {
        return $this->getMode() === EnvironmentMode::DEVELOPMENT;
    }

    public function isStagingMode(): bool
    {
        return $this->getMode() === EnvironmentMode::STAGING;
    }

    public function isProductionMode(): bool
    {
        return $this->getMode() === EnvironmentMode::PRODUCTION;
    }
}
