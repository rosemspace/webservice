<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use function is_callable;
use function is_string;

class LazyFactoryMiddleware extends AbstractLazyMiddleware
{
    /**
     * LazyFactoryMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param callable           $middleware
     * @param array              $options
     */
    public function __construct(ContainerInterface $container, $middleware, array $options = [])
    {
        if (!is_callable($middleware)) {
            throw new InvalidArgumentException('Middleware factory should be callable.');
        }

        parent::__construct($container, $middleware, $options);
    }

    protected function initialize() : void
    {
        $middlewareFactory = $this->middleware;

        if (is_string(reset($middlewareFactory))) {
            $middlewareFactory[key($middlewareFactory)] = $this->container->get(reset($middlewareFactory));
        }

        $this->middleware = $middlewareFactory($this->container);

        if (!($this->middleware instanceof MiddlewareInterface)) {
            throw new InvalidArgumentException('The callable should return an object that implement "' .
                MiddlewareInterface::class . '" interface');
        }
    }
}
