<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server;

use Psr\Http\Message\{
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface,
    RequestHandlerInterface
};

class MiddlewareRequestHandler implements RequestHandlerInterface
{
    protected RequestHandlerInterface $finalHandler;

    protected RequestHandlerInterface $startHandler;

    protected RequestHandlerInterface $lastHandler;

    public function __construct(RequestHandlerInterface $finalHandler)
    {
        $this->startHandler = $this->lastHandler = $this->finalHandler = $finalHandler;
    }

    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->lastHandler = RequestHandler::withMiddleware($middleware, $this->lastHandler);

        if ($this->startHandler === $this->finalHandler) {
            $this->startHandler = $this->lastHandler;
        }

        $this->lastHandler = &$this->lastHandler->requestHandler;

        return $this;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->startHandler->handle($request);
    }
}
