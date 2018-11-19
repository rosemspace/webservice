<?php

namespace Rosem\Psr\Http\Server;

interface MiddlewareRunnerInterface
{
    /**
     * Run the application
     */
    public function run(): bool;
}
