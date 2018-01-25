<?php

namespace Rosem\Kernel\Facade;

use TrueStd\App\AppInterface;
use TrueCode\Container\AbstractFacade;

class App extends AbstractFacade
{
    /**
     * @return string Facade accessor
     */
    protected static function getFacadeAccessor() {
        return AppInterface::class;
    }
}
