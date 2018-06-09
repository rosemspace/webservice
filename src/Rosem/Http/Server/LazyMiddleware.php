<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class LazyMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var string
     */
    protected $middleware;

    /**
     * LazyMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param string             $middleware
     */
    public function __construct(ContainerInterface $container, string $middleware)
    {
        $this->container = $container;
        $this->middleware = $middleware;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        $middleware = $this->container->get($this->middleware);

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware->process($request, $delegate);
        }

        throw new InvalidArgumentException('The middleware "' . $this->middleware . '" should implement "' .
            MiddlewareInterface::class . '" interface');
    }
}
