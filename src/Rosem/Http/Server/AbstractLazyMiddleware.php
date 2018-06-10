<?php

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

abstract class AbstractLazyMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var mixed|MiddlewareInterface
     */
    protected $middleware;

    /**
     * LazyMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param string             $middleware
     */
    public function __construct(ContainerInterface $container, $middleware)
    {
        $this->container = $container;
        $this->middleware = $middleware;
    }
}
