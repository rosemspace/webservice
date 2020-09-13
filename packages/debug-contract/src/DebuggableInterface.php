<?php

declare(strict_types=1);

namespace Rosem\Contract\Debug;

interface DebuggableInterface
{
    /**
     * Determine if the application is allowed to debug.
     */
    public function isAllowedToDebug(): bool;
}
