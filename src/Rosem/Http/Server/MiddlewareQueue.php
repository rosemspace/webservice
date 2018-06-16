<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\Http\Server\MiddlewareQueueInterface;

class MiddlewareQueue implements MiddlewareQueueInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestHandlerInterface
     */
    protected $finalHandler;

    /**
     * @var RequestHandlerInterface
     */
    protected $handlerQueue;

    /**
     * @var RequestHandlerInterface|object
     */
    protected $lastHandler;

    public function __construct(ContainerInterface $container, RequestHandlerInterface $finalHandler)
    {
        $this->container = $container;
        $this->handlerQueue = $this->finalHandler = $finalHandler;
    }

    protected function createMiddlewareRequestHandler(MiddlewareInterface $middleware)
    {
        return new class ($middleware) implements RequestHandlerInterface
        {
            private $middleware;

            public $nextHandler;

            public function __construct(MiddlewareInterface $middleware)
            {
                $this->middleware = $middleware;
            }

            /**
             * Handle the request and return a response.
             *
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             * @throws InvalidArgumentException
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->nextHandler);
            }
        };
    }

    /**
     * @param MiddlewareInterface $middleware
     * @param int|float|string    $priority
     */
    public function use(MiddlewareInterface $middleware, $priority = 0): void // TODO: priority functionality
    {
        if ($this->lastHandler) {
            $this->lastHandler = &$this->lastHandler->nextHandler;
        } else {
            $this->handlerQueue = &$this->lastHandler;
        }

        $this->lastHandler = $this->createMiddlewareRequestHandler($middleware);
        $this->lastHandler->nextHandler = $this->finalHandler;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handlerQueue->handle($request);
    }
}
