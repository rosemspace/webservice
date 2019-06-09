<?php

namespace Rosem\Contract\Http\Server;

interface MiddlewareRunnerInterface
{
    /**
     * Run the application
     */
    public function run(): bool;
}
