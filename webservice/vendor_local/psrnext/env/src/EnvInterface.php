<?php

namespace Psrnext\Env;

interface EnvInterface
{
    public const KEY_MODE         = 'APP_ENV';

    public const MODE_DEVELOPMENT = 'development';

    public const MODE_MAINTENANCE = 'maintenance';

    public const MODE_PRODUCTION  = 'production';

    public const MODE_STAGING     = 'staging';

    public const MODE_TESTING     = 'testing';

    public function getMode(): string;

    public function isDevelopmentMode(): bool;

    public function isMaintenanceMode(): bool;

    public function isProductionMode(): bool;

    public function isStagingMode(): bool;

    public function isTestingMode(): bool;
}
