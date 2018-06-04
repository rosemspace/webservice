<?php

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\Http\Server\MiddlewareProcessorInterface;
use function in_array;
use function call_user_func;

class MiddlewareProcessor implements MiddlewareInterface, MiddlewareProcessorInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var RequestHandlerInterface|object
     */
    protected $lastHandler;

    /**
     * @var RequestHandlerInterface
     */
    protected $defaultHandler;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
     * @throws \InvalidArgumentException
     */
    public function use(string $middlewareClass, float $priority = 0): void // TODO: priority functionality
    {
        if (in_array(MiddlewareInterface::class, class_implements($middlewareClass) ?: [], true)) {
            if ($this->lastHandler) {
                $this->lastHandler = &$this->lastHandler->nextHandler;
            } else {
                $this->defaultHandler = &$this->lastHandler;
            }

            $this->lastHandler = $this->createMiddlewareRequestHandler(function () use (&$middlewareClass) {
                return $this->container->get($middlewareClass);
            });
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
        return $this->defaultHandler->handle($request);
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $nextHandler = &$this->lastHandler->nextHandler;
        $nextHandler = $handler;

        return $this->handle($request);
    }
}
