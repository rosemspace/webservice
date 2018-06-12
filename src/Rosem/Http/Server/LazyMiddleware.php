<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class LazyMiddleware extends AbstractLazyMiddleware
{
    /**
     * LazyMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param string             $middleware
     * @param array              $options
     */
    public function __construct(ContainerInterface $container, string $middleware, array $options = [])
    {
        parent::__construct($container, $middleware, $options);
    }

    protected function initialize(): void
    {
        $this->middleware = $this->container->get($this->middleware);

        if (!($this->middleware instanceof MiddlewareInterface)) {
            throw new InvalidArgumentException('The middleware "' . $this->middleware . '" should implement "' .
                MiddlewareInterface::class . '" interface');
        }

        foreach ($this->options as $method => $arguments) {
            $this->middleware->$method(...(array)$arguments);
        }
    }
}
