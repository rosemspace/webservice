<?php

namespace Rosem\App;

use Psrnext\App\{AppFactoryInterface, AppInterface};

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
