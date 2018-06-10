<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function is_string;

class LazyMiddleware extends AbstractLazyMiddleware
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
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        if (is_string($this->middleware)) {
            $this->middleware = $this->container->get($this->middleware);

            if ($this->middleware instanceof MiddlewareInterface) {
                return $this->middleware->process($request, $delegate);
            }

            throw new InvalidArgumentException('The middleware "' . $this->middleware . '" should implement "' .
                MiddlewareInterface::class . '" interface');
        }

        return $this->middleware->process($request, $delegate);
    }
}
