<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;
use UnexpectedValueException;

class DeferredMiddleware extends AbstractLazyMiddleware
{
    /**
     * LazyMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param string             $middleware
     */
    public function __construct(ContainerInterface $container, string $middleware)
    {
        parent::__construct($container, $middleware);
    }

    /**
     * @throws UnexpectedValueException
     */
    protected function initialize(): void
    {
        $this->middleware = $this->container->get($this->middleware);

        if (!($this->middleware instanceof MiddlewareInterface)) {
            throw new UnexpectedValueException('The middleware "' . $this->middleware . '" should implement "' .
                MiddlewareInterface::class . '" interface');
        }
    }
}
