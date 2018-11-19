<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface};
use Rosem\Psr\Http\Server\MiddlewareCollectorInterface;
use function call_user_func;

class MiddlewareCollector implements MiddlewareCollectorInterface, RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

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

    public static function fromCallable(callable $middleware)
    {
        return new class ($middleware) implements MiddlewareInterface
        {
            private $middleware;

            public function __construct(callable $middleware)
            {
                $this->middleware = $middleware;
            }

            /**
             * {@inheritdoc}
             */
            public function process(
                ServerRequestInterface $request,
                RequestHandlerInterface $handler
            ): ResponseInterface {
                return call_user_func($this->middleware, $request, $handler);
            }
        };
    }

    protected function createRequestHandlerFromMiddleware(MiddlewareInterface $middleware): object
    {
        return new class ($middleware) implements RequestHandlerInterface
        {
            private $middleware;

            public $nextHandler;

            public function __construct(MiddlewareInterface $middleware)
            {
                $this->middleware = $middleware;
            }

            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                return $this->middleware->process($request, $this->nextHandler);
            }
        };
    }

    /**
     * @param string $middleware
     *
     * @return object
     */
    protected function createDeferredRequestHandlerFromMiddleware(string $middleware): object
    {
        return new class ($this->container, $middleware) implements RequestHandlerInterface
        {
            private $container;

            private $middleware;

            public $nextHandler;

            public function __construct(ContainerInterface $container, string $middleware)
            {
                $this->container = $container;
                $this->middleware = $middleware;
            }

            /**
             * Handle the request and return a response.
             *
             * @param ServerRequestInterface $request
             *
             * @return ResponseInterface
             * @throws \Psr\Container\NotFoundExceptionInterface
             * @throws \Psr\Container\ContainerExceptionInterface
             * @throws InvalidArgumentException
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $middlewareInstance = $this->container->get($this->middleware);

                if (!$middlewareInstance instanceof MiddlewareInterface) {
                    throw new InvalidArgumentException(
                        "The middleware \"$this->middleware\" should implement \"" .
                        MiddlewareInterface::class . '" interface.'
                    );
                }

                return $middlewareInstance->process($request, $this->nextHandler);
            }
        };
    }

    protected function initializeQueue(): void
    {
        if ($this->lastHandler) {
            $this->lastHandler = &$this->lastHandler->nextHandler;
        } else {
            $this->handlerQueue = &$this->lastHandler;
        }
    }

    public function add(MiddlewareInterface $middleware): void
    {
        $this->initializeQueue();
        $this->lastHandler = $this->createRequestHandlerFromMiddleware($middleware);
        $this->lastHandler->nextHandler = $this->finalHandler;
    }

    /**
     * @param string           $middleware
     * @param int|float|string $priority
     */
    public function use(string $middleware, $priority = 0): void // TODO: priority functionality
    {
        $this->initializeQueue();
        $this->lastHandler = $this->createDeferredRequestHandlerFromMiddleware($middleware);
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
