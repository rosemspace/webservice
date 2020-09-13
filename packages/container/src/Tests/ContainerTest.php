<?php

declare(strict_types=1);

namespace Rosem\Component\Container\Test;

use PHPUnit\Framework\TestCase;
use Rosem\Component\Container\ServiceContainer;

class ContainerTest extends TestCase
{
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new ServiceContainer();
    }

    protected function tearDown(): void
    {
        $this->calculator = null;
    }

    public function testAdd(): void
    {
        $result = $this->calculator->add(1, 2);
        $this->assertSame(3, $result);
    }
}
