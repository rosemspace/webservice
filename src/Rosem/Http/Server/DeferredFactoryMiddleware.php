<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use UnexpectedValueException;
use function is_callable;
use function is_string;

class DeferredFactoryMiddleware extends AbstractLazyMiddleware
{
    /**
     * LazyFactoryMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param callable           $middleware
     *
     * @throws InvalidArgumentException
     */
    public function __construct(ContainerInterface $container, $middleware)
    {
        if (!is_callable($middleware)) {
            throw new InvalidArgumentException('Middleware factory should be callable.');
        }

        parent::__construct($container, $middleware);
    }

    /**
     * @throws UnexpectedValueException
     */
    protected function initialize() : void
    {
        $middlewareFactory = $this->middleware;

        if (is_string(reset($middlewareFactory))) {
            $middlewareFactory[key($middlewareFactory)] = $this->container->get(reset($middlewareFactory));
        }

        $this->middleware = $middlewareFactory($this->container);

        if (!($this->middleware instanceof MiddlewareInterface)) {
            throw new UnexpectedValueException('The callable should return an object that implement "' .
                MiddlewareInterface::class . '" interface');
        }
    }
}
