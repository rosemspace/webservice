<?php

namespace Rosem\Http\Server;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class DeferredConfigurableMiddleware extends DeferredMiddleware
{
    protected $options;

    public function __construct(ContainerInterface $container, string $middleware, array $options = [])
    {
        parent::__construct($container, $middleware);

        $this->options = $options;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        if (!($this->middleware instanceof MiddlewareInterface)) {
            $this->initialize();

            foreach ($this->options as $name => $value) {
                $request = $request->withAttribute($name, $value);
            }
        }

        return $this->middleware->process($request, $delegate);
    }
}
