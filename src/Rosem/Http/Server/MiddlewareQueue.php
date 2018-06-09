<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\Http\Server\MiddlewareQueueInterface;
use function in_array;
use function call_user_func;

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

    protected function createMiddlewareRequestHandler(callable $middlewareFactory)
    {
        return new class ($middlewareFactory) implements RequestHandlerInterface
        {
            private $middlewareFactory;

            public $nextHandler;

            public function __construct(callable $middlewareFactory)
            {
                $this->middlewareFactory = $middlewareFactory;
            }

            /**
             * Handle the request and return a response.
             *
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return call_user_func($this->middlewareFactory)->process($request, $this->nextHandler);
            }
        };
    }

    /**
     * @param string $middlewareClass
     * @param float  $priority
     *
     * @throws \Psr\Container\NotFoundExceptionInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \InvalidArgumentException
     */
    public function use(string $middlewareClass, float $priority = 0): void // TODO: priority functionality
    {
        if (in_array(MiddlewareInterface::class, class_implements($middlewareClass) ?: [], true)) {
            if ($this->lastHandler) {
                $this->lastHandler = &$this->lastHandler->nextHandler;
            } else {
                $this->handlerQueue = &$this->lastHandler;
            }

            $this->lastHandler = $this->createMiddlewareRequestHandler(function () use (&$middlewareClass) {
                return $this->container->get($middlewareClass);
            });
            $this->lastHandler->nextHandler = $this->finalHandler;
        } else {
            throw new InvalidArgumentException('The middleware "' . $middlewareClass . '" should implement "' .
                MiddlewareInterface::class . '" interface');
        }
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
