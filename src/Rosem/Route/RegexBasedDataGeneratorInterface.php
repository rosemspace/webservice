<?php

namespace Rosem\Route;

interface RegexBasedDataGeneratorInterface
{
    /**
     * Add the route.
     *
     * @param RouteInterface $route
     */
    public function addRoute(RouteInterface $route): void;

    /**
     * Prepare internal data for the new chunk.
     */
    public function newChunk(): void;
}
