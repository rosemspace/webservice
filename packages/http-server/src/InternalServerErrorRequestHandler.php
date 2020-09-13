<?php

declare(strict_types=1);

namespace Rosem\Component\Http\Server;

use Fig\Http\Message\StatusCodeInterface as StatusCode;
use Psr\Http\Message\{
    ResponseFactoryInterface,
    ResponseInterface,
    ServerRequestInterface
};
use Psr\Http\Server\RequestHandlerInterface;

class InternalServerErrorRequestHandler implements RequestHandlerInterface
{
    protected ResponseFactoryInterface $responseFactory;

    /**
     * MainController constructor.
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createResponse(StatusCode::STATUS_INTERNAL_SERVER_ERROR);
    }
}
