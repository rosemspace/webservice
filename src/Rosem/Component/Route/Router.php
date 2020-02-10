<?php

namespace Rosem\Component\Route;

use Rosem\Contract\Route\RouteDispatcherInterface;
use Rosem\Component\Route\DataGenerator\MarkBasedDataGenerator;
use Rosem\Component\Route\DataGenerator\StringNumberBasedDataGenerator;
use Rosem\Component\Route\DataGenerator\GroupCountBasedDataGenerator;
use Rosem\Component\Route\Dispatcher\MarkBasedDispatcher;
use Rosem\Component\Route\Dispatcher\StringNumberBasedDispatcher;
use Rosem\Component\Route\Dispatcher\GroupCountBasedDispatcher;

class Router extends RouteCollector implements RouteDispatcherInterface
{
    use RouteDispatcherTrait;

    /**
     * Router constructor.
     *
     * @param int $routeCountPerRegex
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(int $routeCountPerRegex = PHP_INT_MAX)
    {
        parent::__construct(new Compiler(new Parser()), new MarkBasedDataGenerator($routeCountPerRegex));
//        parent::__construct(new Compiler(new Parser()), new StringNumberBasedDataGenerator());
//        parent::__construct(new Compiler(new Parser()), new GroupCountBasedDataGenerator());

        $this->dispatcher = new MarkBasedDispatcher();
//        $this->dispatcher = new StringNumberBasedDispatcher();
//        $this->dispatcher = new GroupCountBasedDispatcher();
    }
}
