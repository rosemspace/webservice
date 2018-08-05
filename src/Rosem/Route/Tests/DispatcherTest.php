<?php

namespace Rosem\Route;

use Rosem\Route\DataGenerator\MarkBasedDataGenerator;
use Rosem\Route\Dispatcher\MarkBasedDispatcher;

class DispatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Collector
     */
    protected $collector;

    /**
     * @var Dispatcher
     */
    protected $dispatcher;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

        $this->collector = new Collector(new Compiler(new Parser()), new MarkBasedDataGenerator());
        $this->dispatcher = new Dispatcher(
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
