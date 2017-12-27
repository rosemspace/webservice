<?php

namespace RosemStandards\Kernel;

use TrueStandards\DI\ContainerInterface;

interface AppInterface extends ContainerInterface
{
    public static function launch() : void;
}
