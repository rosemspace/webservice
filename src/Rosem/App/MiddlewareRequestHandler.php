<?php

namespace Rosem\App;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

class MiddlewareRequestHandler implements RequestHandlerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var string
     */
    private $middleware;

    /**
     * @var RequestHandlerInterface
     */
    private $nextHandler;

    public function __construct(
        ContainerInterface $container,
        string $middleware
    ) {
        $this->container = $container;
        $this->middleware = $middleware;
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $middlewareInstance = $this->container->get($this->middleware);

        if ($middlewareInstance instanceof MiddlewareInterface) {
            return $middlewareInstance->process($request, $this->nextHandler);
        }

        throw new \InvalidArgumentException("The middleware \"$this->middleware\" should implement \"" .
            MiddlewareInterface::class . '" interface'); //TODO: middleware exception
    }

    public function &getNextHandlerPointer()
    {
        return $this->nextHandler;
    }
}
