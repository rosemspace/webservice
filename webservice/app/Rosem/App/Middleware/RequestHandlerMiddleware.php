<?php

namespace Rosem\App\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\Http\Factory\ResponseFactoryInterface;

class RequestHandlerMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface used to resolve the handlers
     */
    private $container;

    /**
     * @var string attribute name for handler reference
     */
    private $handlerAttribute = 'request-handler';

    /**
     * RequestHandlerMiddleware constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Set the attribute name to store handler reference.
     * @param string $handlerAttribute
     * @return RequestHandlerMiddleware
     */
    public function handlerAttribute(string $handlerAttribute): self
    {
        $this->handlerAttribute = $handlerAttribute;
        return $this;
    }

    /**
     * Process a server request and return a response.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestHandler = $request->getAttribute($this->handlerAttribute);

        if (\is_string($requestHandler)) {
            $requestHandler = $this->container->get($requestHandler);
        }

        if ($requestHandler instanceof MiddlewareInterface) {
            return $requestHandler->process($request, $handler);
        }

        if ($requestHandler instanceof RequestHandlerInterface) {
            return $requestHandler->handle($request);
        }

        if (\is_callable($requestHandler)) {
            if (\is_string(reset($requestHandler))) {
                $requestHandler[key($requestHandler)] = $this->container->get(reset($requestHandler));
            }

            return (new CallableHandlerMiddleware(
                $this->container->get(ResponseFactoryInterface::class),
                $requestHandler)
            )->process($request, $handler);
        }

        throw new \RuntimeException(sprintf('Invalid request handler: %s', \gettype($requestHandler)));
    }
}
