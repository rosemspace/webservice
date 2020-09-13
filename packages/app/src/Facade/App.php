<?php

declare(strict_types=1);

namespace Rosem\Component\App\Facade;

use Rosem\Component\Container\AbstractFacade;
use Rosem\Contract\App\AppInterface;

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
