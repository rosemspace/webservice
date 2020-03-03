<?php

namespace Rosem\Component\GraphQL;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use GraphQL\Error\Debug;
use GraphQL\Server\StandardServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;

class GraphQLRequestHandler implements RequestHandlerInterface
{
    /**
     * @var StandardServer
     */
    protected StandardServer $server;

    /**
     * @var int|null
     */
    public ?int $debug;

    /**
     * GraphQLMiddleware constructor.
     *
     * @param StandardServer $server
     * @param int|null       $debug
     */
    public function __construct(StandardServer $server, ?int $debug = null)
    {
        $this->server = $server;
        $this->debug = $debug;
    }

    /**
     * @return int|null
     */
    protected function getDebug(): ?int
    {
        if ($this->debug) {
            return Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;
        }

        return $this->debug;
    }

    /**
     * @param int|null $debug
     */
    public function setDebug(?int $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * Handle the request and return a response.
     *
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        //TODO: move json_decode into separated middleware

        if (strtoupper($request->getMethod()) === RequestMethod::METHOD_GET) {
            $params = $request->getQueryParams();
            $params['variables'] ??= null;
            $request = $request->withQueryParams($params);
        } else {
            $params = json_decode($request->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);
            $params['variables'] ??= null;
            $request = $request->withParsedBody($params);
        }

        return new JsonResponse($this->server->executePsrRequest($request)->toArray($this->getDebug() ?? false));
    }
}
