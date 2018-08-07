<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use Exception;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Psr\Http\Server\MiddlewareQueueInterface;

class MiddlewareQueue implements MiddlewareQueueInterface, RequestHandlerInterface
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

    protected function createMiddlewareRequestHandler(string $middleware)
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
             * @throws Exception
             * @throws InvalidArgumentException
             */
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                if (!$this->container->has($this->middleware)) {
                    throw new Exception("Middleware \"$this->middleware\" does not exist.");
                }

                $middlewareInstance = $this->container->get($this->middleware);

                if (!$middlewareInstance instanceof MiddlewareInterface) {
                    throw new InvalidArgumentException("The middleware \"$this->middleware\" should implement \"" .
                        MiddlewareInterface::class . '" interface.');
                }

                return $middlewareInstance->process($request, $this->nextHandler);
            }
        };
    }

    /**
     * @param string           $middleware
     * @param array            $requestAttributes
     * @param int|float|string $priority
     */
    public function use(string $middleware, array $requestAttributes = [], $priority = 0): void // TODO: priority functionality
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
