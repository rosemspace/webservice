<?php

namespace Rosem\Kernel\Facades;

use RosemStandards\Kernel\AppInterface;
use TrueStandards\DI\AbstractFacade;

class App extends AbstractFacade
{
    /**
     * @return string Facade accessor
     */
    protected static function getFacadeAccessor() {
        return AppInterface::class;
    }
}
