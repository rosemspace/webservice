<?php

namespace Rosem\Psr\Environment;

abstract class AbstractEnvironment implements EnvironmentInterface
{
    public function isMaintenanceMode(): bool
    {
        return $this->getAppMode() === EnvironmentMode::MAINTENANCE;
    }

    public function isDemoMode(): bool
    {
        return $this->getAppMode() === EnvironmentMode::DEMO;
    }

    public function isDevelopmentMode(): bool
    {
        return $this->getAppMode() === EnvironmentMode::DEVELOPMENT;
    }

    public function isStagingMode(): bool
    {
        return $this->getAppMode() === EnvironmentMode::STAGING;
    }

    public function isProductionMode(): bool
    {
        return $this->getAppMode() === EnvironmentMode::PRODUCTION;
    }
}
