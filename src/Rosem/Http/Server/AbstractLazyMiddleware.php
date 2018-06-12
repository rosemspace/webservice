<?php

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

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
     * @var array
     */
    protected $options;

    /**
     * LazyMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param mixed              $middleware
     */
    public function __construct(ContainerInterface $container, $middleware, array $options = [])
    {
        $this->container = $container;
        $this->middleware = $middleware;
        $this->options = $options;
    }

    abstract protected function initialize(): void;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        return $this->__call('process', [$request, $delegate]);
    }

    public function __call($name, $arguments)
    {
        if (!($this->middleware instanceof MiddlewareInterface)) {
            $this->initialize();
        }

        return $this->middleware->$name(...$arguments);
    }
}
