<?php

namespace Psrnext\Environment;

abstract class AbstractEnvironment implements EnvironmentInterface
{
    public function isDevelopmentMode(): bool
    {
        return $this->getMode() === self::MODE_DEVELOPMENT;
    }

    public function isMaintenanceMode(): bool
    {
        return $this->getMode() === self::MODE_MAINTENANCE;
    }

    public function isProductionMode(): bool
    {
        return $this->getMode() === self::MODE_PRODUCTION;
    }

    public function isStagingMode(): bool
    {
        return $this->getMode() === self::MODE_STAGING;
    }

    public function isTestingMode(): bool
    {
        return $this->getMode() === self::MODE_TESTING;
    }
}
