<?php

namespace Rosem\Kernel;

use TrueCode\Container\ReflectionContainer;
use TrueStd\Application\{AppFactoryInterface, AppInterface};

class AppFactory implements AppFactoryInterface
{
    /**
     * @return AppInterface
     */
    public static function create() : AppInterface
    {
        $app = new App;
        $app->delegate(new ReflectionContainer);

        return $app;
    }
}
