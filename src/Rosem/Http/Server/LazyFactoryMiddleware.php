<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use function is_callable;
use function is_string;

class LazyFactoryMiddleware extends AbstractLazyMiddleware
{
    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     * @throws InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        if (is_callable($this->middleware)) {
            $middlewareFactory = $this->middleware;

            if (is_string(reset($middlewareFactory))) {
                $middlewareFactory[key($middlewareFactory)] = $this->container->get(reset($middlewareFactory));
            }

            $this->middleware = $middlewareFactory($this->container);

            if ($this->middleware instanceof MiddlewareInterface) {
                return $this->middleware->process($request, $delegate);
            }

            throw new InvalidArgumentException('The callable should return an object that implement "' .
                MiddlewareInterface::class . '" interface');
        }

        if ($this->middleware instanceof MiddlewareInterface) {
            return $this->middleware->process($request, $delegate);
        }

        throw new InvalidArgumentException('Middleware factory should be callable.');
    }
}
