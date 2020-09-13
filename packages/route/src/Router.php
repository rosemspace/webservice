<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Rosem\Component\Route\Exception\HttpMethodNotAllowedException;
use Rosem\Component\Route\Map\MarkBasedMap;
use Rosem\Contract\Route\{
    HttpRouteCollectorInterface,
    HttpRouteCollectorTrait
};

/**
 * Class RouteCollector.
 */
final class Router extends MarkBasedMap implements HttpRouteCollectorInterface
{
    use HttpRouteCollectorTrait;

    /**
     * Allowed methods.
     */
    protected array $allowedScopes = [
        RequestMethod::METHOD_HEAD,
        RequestMethod::METHOD_GET,
        RequestMethod::METHOD_POST,
        RequestMethod::METHOD_PUT,
        RequestMethod::METHOD_PATCH,
        RequestMethod::METHOD_DELETE,
        RequestMethod::METHOD_OPTIONS,
    ];

    /**
     * @throws HttpMethodNotAllowedException
     */
    public function any(string $routePattern, $handler): void
    {
        $this->addRoute($this->allowedScopes, $routePattern, $handler);
    }

    /**
     * Check if HTTP methods are allowed.
     *
     * @throws HttpMethodNotAllowedException
     */
    protected function assertAllowedScopes(array $httpMethods): void
    {
        $notAllowedScopes = array_diff($httpMethods, $this->allowedScopes);

        if (count($notAllowedScopes) > 0) {
            throw HttpMethodNotAllowedException::forNotAllowedHttpMethods($notAllowedScopes, $this->allowedScopes);
        }
    }
}
