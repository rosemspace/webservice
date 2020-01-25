<?php

namespace Rosem\Contract\App;

class EnvEnum
{
    // private usage
    public const LOCAL = 'local';

    // show maintenance page
    public const MAINTENANCE = 'maintenance';

    // allow only partial functionality
    public const DEMO = 'demo';

    // display errors, no caches
    public const DEVELOPMENT = 'development';

    // display errors, use caches
    public const TEST = 'test';

    // log errors, use caches
    public const ACCEPTANCE  = 'acceptance';

    // log errors, use caches
    public const PRODUCTION = 'production';
}
