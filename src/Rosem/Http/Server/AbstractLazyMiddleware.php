<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

abstract class AbstractLazyMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var mixed|MiddlewareInterface
     */
    protected $middleware;

    /**
     * LazyMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param mixed              $middleware
     */
    public function __construct(ContainerInterface $container, $middleware)
    {
        $this->container = $container;
        $this->middleware = $middleware;
    }

    abstract protected function initialize(): void;

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        if (!($this->middleware instanceof MiddlewareInterface)) {
            $this->initialize();
        }

        return $this->middleware->process($request, $delegate);
    }
}
