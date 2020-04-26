<?php

namespace Rosem\Component\App\Facade;

use Rosem\Contract\App\AppInterface;
use Rosem\Component\Container\AbstractFacade;

class App extends AbstractFacade
{
    /**
     * @return string Facade accessor
     */
    protected static function getFacadeAccessor(): string
    {
        return AppInterface::class;
    }
}
