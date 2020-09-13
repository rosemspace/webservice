<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use PHPUnit\Framework\TestCase;
use Rosem\Component\Route\Contract\RouteDispatcherInterface;

class DispatcherTest extends TestCase
{
    /**
     * @var RouteDispatcherInterface
     */
    protected $dispatcher;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dispatcher = new Router();
    }

    /**
     * Test that true does in fact equal true
     */
    public function testDispatch(): void
    {
        $result = $this->dispatcher->dispatch('GET', '/');

        self::assertSame(['GET', '/'], $result);
    }

    public function testRegExpDelimiter(): void
    {
    }
}
