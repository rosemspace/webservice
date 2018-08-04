<?php
declare(strict_types=1);

namespace Rosem\Http\Server;

use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

class CallableBasedMiddleware extends CallableBasedRequestHandler implements MiddlewareInterface
{
    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return $this->execute($this->callable, $request, $handler);
    }
}
