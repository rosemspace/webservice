<?php

namespace Rosem\GraphQL\Middleware;

use GraphQL\{
    Error\Debug, Server\StandardServer
};
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};
use Psrnext\Router\RouteCollectorInterface;
use Zend\Diactoros\Response\JsonResponse;

class GraphQLMiddleware implements MiddlewareInterface
{
    /**
     * @var StandardServer
     */
    protected $server;

    /**
     * @var string
     */
    protected $graphQLUri;

    /**
     * @var array
     */
    private $graphQLHeaderList = [
        'application/graphql',
    ];

    /**
     * @var array
     */
    protected $allowedMethods = [
        'GET', 'POST',
    ];

    /**
     * @var bool|int
     */
    protected $debug;

    /**
     * GraphQLMiddleware constructor.
     *
     * @param StandardServer $server
     * @param string         $graphQLUri
     * @param bool           $debug
     */
    public function __construct(StandardServer $server, $graphQLUri = '/graphql', $debug = false)
    {
        $this->server = $server;
        $this->graphQLUri = $graphQLUri;
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
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $delegate
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $delegate): ResponseInterface
    {
        //TODO: move json_decode into separated middleware
//        $request = $request->withHeader('Content-Type', 'application/json');

        if (!$this->isGraphQLRequest($request)) {
            return $delegate->handle($request);
        }

        if (strtoupper($request->getMethod()) === RouteCollectorInterface::METHOD_GET) {
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

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function isGraphQLRequest(ServerRequestInterface $request): bool
    {
        return $this->hasUri($request) || $this->hasGraphQLHeader($request);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function hasUri(ServerRequestInterface $request): bool
    {
        return $this->graphQLUri === $request->getUri()->getPath();
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return bool
     */
    private function hasGraphQLHeader(ServerRequestInterface $request): bool
    {
        if (!$request->hasHeader('content-type')) {
            return false;
        }

        $requestHeaderList = \array_map(function ($header) {
            return \trim($header);
        }, \explode(',', $request->getHeaderLine('content-type')));

        foreach ($this->graphQLHeaderList as $allowedHeader) {
            if (\in_array($allowedHeader, $requestHeaderList, true)) {
                return true;
            }
        }

        return false;
    }
}
