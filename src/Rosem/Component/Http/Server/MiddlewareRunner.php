<?php

namespace Rosem\Component\Http\Server;

use Laminas\Diactoros\ServerRequestFactory;
use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Contract\Http\Server\{
    EmitterInterface,
    MiddlewareRunnerInterface
};

class MiddlewareRunner implements MiddlewareRunnerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    protected RequestHandlerInterface $requestHandler;

    /**
     * @var EmitterInterface
     */
    protected EmitterInterface $emitter;

    /**
     * MiddlewareRunner constructor.
     *
     * @param RequestHandlerInterface $requestHandler
     * @param EmitterInterface        $emitter
     */
    public function __construct(RequestHandlerInterface $requestHandler, EmitterInterface $emitter)
    {
        $this->requestHandler = $requestHandler;
        $this->emitter = $emitter;
    }

    /**
     * Run the application
     */
    public function run(): bool
    {
        return $this->emitter->emit($this->requestHandler->handle(ServerRequestFactory::fromGlobals()));
    }
}
