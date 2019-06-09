<?php

namespace Rosem\Component\GraphQL;

use GraphQL\Server\StandardServer;
use Psr\Http\Message\{
    ResponseInterface, ServerRequestInterface
};
use Psr\Http\Server\{
    MiddlewareInterface, RequestHandlerInterface
};

class GraphQLMiddleware extends GraphQLRequestHandler implements MiddlewareInterface
{
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
     * GraphQLMiddleware constructor.
     *
     * @param StandardServer $server
     * @param string         $graphQLUri
     * @param bool           $debug
     */
    public function __construct(StandardServer $server, $graphQLUri = '/graphql', $debug = false)
    {
        parent::__construct($server, $debug);

        $this->graphQLUri = $graphQLUri;
    }

    /**
     * @param ServerRequestInterface  $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isGraphQLRequest($request)) {
            return $this->handle($request);
        }

        return $handler->handle($request);
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
