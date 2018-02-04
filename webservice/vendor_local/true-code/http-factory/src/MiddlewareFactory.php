<?php

namespace TrueCode\Http\Factory;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use TrueCode\Container\Container;
use Psrnext\Http\Factory\MiddlewareFactoryInterface;

class MiddlewareFactory implements MiddlewareFactoryInterface
{
    /**
     * @var Container
     */
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param array|callable $callable
     *
     * @return MiddlewareInterface
     */
    public function createMiddleware($callable) : MiddlewareInterface
    {
        return new class ($this->container, $callable) implements MiddlewareInterface
        {
            /**
             * @var Container
             */
            protected $container;

            /**
             * @var array|callable
             */
            protected $callable;

            public function __construct($container, $callable)
            {
                $this->container = $container;
                $this->callable = $callable;
            }

            /**
             * @param ServerRequestInterface  $request
             * @param RequestHandlerInterface $handler
             *
             * @return ResponseInterface
             * @throws \Psr\Container\ContainerExceptionInterface
             * @throws \Psr\Container\NotFoundExceptionInterface
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ) : ResponseInterface {
                return $this->container->call($this->callable, [], [$request, $handler]);
            }
        };
    }
}
