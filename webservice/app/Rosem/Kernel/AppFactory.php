<?php

namespace Rosem\Kernel;

use TrueStd\Application\{AppFactoryInterface, AppInterface};

class AppFactory implements AppFactoryInterface
{
    /**
     * @return AppInterface
     */
    public static function create() : AppInterface
    {
        $app = new App;

        return $app;
    }
}
