<?php

namespace Rosem\Route;

interface RegexBasedDataGeneratorInterface
{
    /**
     * Add the route.
     *
     * @param RegexRouteInterface $route
     */
    public function addRoute(RegexRouteInterface $route): void;

    /**
     * Prepare internal data for the new chunk.
     */
    public function newChunk(): void;
}
