<?php

namespace Rosem\Component\Container\Test;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    private $calculator;

    protected function setUp(): void
    {
        $this->calculator = new \Rosem\Component\Container\ServiceContainer();
    }

    protected function tearDown(): void
    {
        $this->calculator = null;
    }

    public function testAdd()
    {
        $result = $this->calculator->add(1, 2);
        $this->assertEquals(3, $result);
    }

}
