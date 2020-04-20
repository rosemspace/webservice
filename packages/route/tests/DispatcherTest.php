<?php

namespace Rosem\Component\Route;

use Rosem\Component\Route\DataGenerator\MarkBasedDataGenerator;
use Rosem\Component\Route\Dispatcher\MarkBasedDispatcher;

class DispatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RouteCollector
     */
    protected $collector;

    /**
     * @var RouteDispatcher
     */
    protected $dispatcher;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

        $this->collector = new RouteCollector(new Compiler(new Parser()), new MarkBasedDataGenerator());
        $this->dispatcher = new RouteDispatcher(
            $this->collector->getStaticRouteMap(),
            $this->collector->getVariableRouteMap(),
            new MarkBasedDispatcher()
        );
    }

    /**
     * Test that true does in fact equal true
     */
    public function testDispatch(): void
    {
        $result = $this->dispatcher->dispatch('GET', '/');
        $this->assertEquals(['GET', '/'], $result);
    }
}