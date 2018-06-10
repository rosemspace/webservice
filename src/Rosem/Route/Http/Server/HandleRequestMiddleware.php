<?php

namespace Rosem\Route\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Rosem\Http\Server\{
    CallableBasedMiddleware, LazyMiddleware, MiddlewareQueue
};

class HandleRequestMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface used to resolve the handlers
     */
    private $container;

    /**
     * @var string attribute name for handler reference
     */
    private $handlerAttribute = 'requestHandler';

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
     * @return self
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
        $requestData = $request->getAttribute($this->handlerAttribute);

        if (!empty($requestData[0])) { // TODO: add constants
            $requestHandler = new MiddlewareQueue($this->container, $this->container->get($requestData[1]));

            /** @var array[] $requestData */
            foreach ($requestData[0] as $middleware) {
                $requestHandler->use(new LazyMiddleware($this->container, $middleware));
            }
        } else {
            $requestHandler = $this->container->get($requestData[1]);
        }

        if ($requestHandler instanceof RequestHandlerInterface) {
            return $requestHandler->handle($request);
        }

        if (\is_callable($requestHandler)) {
            if (\is_string(reset($requestHandler))) {
                $requestHandler[key($requestHandler)] = $this->container->get(reset($requestHandler));
            }

            return (new CallableBasedMiddleware(
                $this->container->get(ResponseFactoryInterface::class),
                $requestHandler)
            )->process($request, $handler);
        }

        throw new \RuntimeException(sprintf('Invalid request handler: %s', \gettype($requestHandler)));
    }
}
