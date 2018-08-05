<?php

namespace Rosem\App;

use Rosem\Psr\App\{
    AppFactoryInterface, AppInterface
};

class AppFactory implements AppFactoryInterface
{
    /**
     * @return AppInterface
     * @throws \Rosem\Container\Exception\ContainerException
     */
    public static function create(): AppInterface
    {
        return new App;
    }
}
