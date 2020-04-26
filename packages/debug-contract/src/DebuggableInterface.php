<?php

namespace Rosem\Contract\Debug;

interface DebuggableInterface
{
    /**
     * Determine if the application is allowed to debug.
     *
     * @return bool
     */
    public function isAllowedToDebug(): bool;
}
