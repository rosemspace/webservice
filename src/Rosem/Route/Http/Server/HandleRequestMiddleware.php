<?php

namespace Rosem\Route\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface};
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface};
use Rosem\Http\Server\{
    CallableBasedMiddleware,
    MiddlewareCollector};

class HandleRequestMiddleware implements MiddlewareInterface
{
    protected const KEY_HANDLER_OR_ALLOWED_METHODS = 0;

    protected const KEY_MIDDLEWARE = 1;

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
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Set the attribute name to store handler reference.
     *
     * @param string $handlerAttribute
     *
     * @return self
     */
    public function handlerAttribute(string $handlerAttribute): self
    {
        $this->handlerAttribute = $handlerAttribute;

        return $this;
    }

    /**
     * Process a server request and return a response.
     *
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requestData = $request->getAttribute($this->handlerAttribute);

        if (!empty($requestData[static::KEY_MIDDLEWARE])) {
            $requestHandler = new MiddlewareCollector(
                $this->container,
                $this->container->get($requestData[static::KEY_HANDLER_OR_ALLOWED_METHODS])
            );

            /** @var array[] $requestData */
            foreach ($requestData[static::KEY_MIDDLEWARE] as $middlewareExtension) {
                $middlewareExtension($requestHandler, $this->container);
            }
        } else {
            $requestHandler = $this->container->get($requestData[static::KEY_HANDLER_OR_ALLOWED_METHODS]);
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
