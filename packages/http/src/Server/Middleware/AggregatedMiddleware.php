<?php

namespace Rosem\Component\Http\Server\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};

class AggregatedMiddleware implements MiddlewareInterface
{
    /**
     * Container instance.
     *
     * @var ContainerInterface
     */
    protected ContainerInterface $container;

    /**
     * List of middleware classes to instantiate and aggregate into one process.
     *
     * @var array
     */
    protected array $middlewareClassList;

    /**
     * AggregateMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param array              $middlewareClassList
     */
    public function __construct(ContainerInterface $container, array $middlewareClassList)
    {
        $this->container = $container;
        $this->middlewareClassList = $middlewareClassList;
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        // TODO: Implement process() method.
    }
}
