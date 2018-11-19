<?php

namespace Rosem\Http\Server;

use Psr\Http\Server\RequestHandlerInterface;
use Rosem\Psr\Http\Server\EmitterInterface;
use Rosem\Psr\Http\Server\MiddlewareRunnerInterface;
use Zend\Diactoros\ServerRequestFactory;

class MiddlewareRunner implements MiddlewareRunnerInterface
{
    /**
     * @var RequestHandlerInterface
     */
    protected $requestHandler;

    /**
     * @var EmitterInterface
     */
    protected $emitter;

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
