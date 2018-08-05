<?php

namespace Rosem\GraphQL;

use Fig\Http\Message\RequestMethodInterface;
use GraphQL\Error\Debug;
use GraphQL\Server\StandardServer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;

class GraphQLRequestHandler implements RequestHandlerInterface
{
    /**
     * @var StandardServer
     */
    protected $server;

    /**
     * @var bool|int
     */
    protected $debug;

    /**
     * GraphQLMiddleware constructor.
     *
     * @param StandardServer $server
     * @param bool           $debug
     */
    public function __construct(StandardServer $server, $debug = false)
    {
        $this->server = $server;
        $this->debug = $debug;
    }

    /**
     * @return bool|int
     */
    protected function getDebug()
    {
        if ($this->debug) {
            return Debug::INCLUDE_DEBUG_MESSAGE | Debug::INCLUDE_TRACE;
        }

        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug): void
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

        if (strtoupper($request->getMethod()) === RequestMethodInterface::METHOD_GET) {
            $params = $request->getQueryParams();
            $params['variables'] = $params['variables'] ?? null;
            $request = $request->withQueryParams($params);
        } else {
            $params = json_decode($request->getBody()->getContents(), true);
            $params['variables'] = $params['variables'] ?? null;
            $request = $request->withParsedBody($params);
        }

        return new JsonResponse($this->server->executePsrRequest($request)->toArray($this->getDebug()));
    }
}
