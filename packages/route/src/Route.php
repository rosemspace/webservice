<?php

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

        parent::__construct("~^$regex$~sx");
    }

    public function getMeta(): array
    {
        return $this->meta;
    }

    /**
     * @inheritDoc
     */
    public function getScope(): string
    {
        return $this->scope;
    }

    /**
     * @inheritDoc
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * @inheritDoc
     */
    public function getPathPattern(): string
    {
        return $this->pathPattern;
    }

    /**
     * @inheritDoc
     */
    public function getHostPattern(): string
    {
        return $this->hostPattern;
    }

    /**
     * @inheritDoc
     */
    public function getSchemePattern(): string
    {
        return $this->schemePattern;
    }
}
