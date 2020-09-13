<?php

declare(strict_types=1);

namespace Rosem\Contract\Debug;

interface SolvableInterface
{
    public function getSolution(?int $code = null): string;
}
