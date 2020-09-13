<?php

declare(strict_types=1);

namespace Rosem\Contract\Debug;

interface InspectableInterface
{
    public function inspect(): array;
}
