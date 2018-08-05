<?php

namespace Rosem\Route;

trait MapTrait
{
    /**
     * @var array[]
     */
    protected $staticRouteMap = [];

    /**
     * @var RegexBasedDataGeneratorInterface[]
     */
    protected $variableRouteMap = [];
}
