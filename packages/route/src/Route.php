<?php

declare(strict_types=1);

namespace Rosem\Component\Route;

use Rosem\Component\Route\Contract\RouteInterface;

class Route extends Regex implements RouteInterface
{
    protected string $scope;

    /**
     * The resource.
     *
     * @var mixed
     */
    protected $resource;

    protected string $pathPattern;

    protected string $hostPattern;

    protected string $schemePattern;

    protected array $meta;

    public function __construct(string $scope, string $routePattern, $resource, array $meta = [])
    {
        $this->scope = $scope;
        $this->resource = $resource;
        $this->pathPattern = $routePattern;
        $this->meta = $meta;
        [$regex] = $meta;

        parent::__construct("~^${regex}$~sx");
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function getScope(): string
    {
        return $this->scope;
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function getPathPattern(): string
    {
        return $this->pathPattern;
    }

    public function getHostPattern(): string
    {
        return $this->hostPattern;
    }

    public function getSchemePattern(): string
    {
        return $this->schemePattern;
    }
}
