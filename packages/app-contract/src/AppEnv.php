<?php

namespace Rosem\Contract\App;

class AppEnv
{
    // private usage
    public const LOCAL = 'local';

    // allow only preview functionality
    public const DEMO = 'demo';

    // allow only partial functionality
    public const PARTIAL = 'partial';

    // display errors, no caches
    public const DEVELOPMENT = 'development';

    // display errors, use caches
    public const TEST = 'test';

    // log errors, use caches
    public const ACCEPTANCE  = 'acceptance';

    // real data from PRODUCTION and new functionality from ACCEPTANCE
    public const STAGING  = 'staging';

    // log errors, use caches
    public const PRODUCTION = 'production';
}
