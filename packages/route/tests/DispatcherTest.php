<?php

namespace Rosem\Component\Route;

use Rosem\Component\Route\Contract\RouteDispatcherInterface;

class DispatcherTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RouteDispatcherInterface
     */
    protected $dispatcher;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        parent::setUp();

        $this->dispatcher = new Router(new RouteParser());
    }

    /**
     * Test that true does in fact equal true
     */
    public function testDispatch(): void
    {
        $result = $this->dispatcher->dispatch('GET', '/');
        $this->assertEquals(['GET', '/'], $result);
    }

    public function testRegExpDelimiter(): void
    {
        // Static route
        //$routeCollector->get('/[home~[page]]'
        // Dynamic route
        //$routeCollector->get('/[home~[page{\d?}]]'

        // {a:b\}/{c:d}
        // /{/a}/{b:c}
        // \{a:b}/{c:d}
        // {\a}/{b:c}
    }
}
