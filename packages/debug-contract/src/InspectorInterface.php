<?php

declare(strict_types=1);

namespace Rosem\Contract\Debug;

interface InspectorInterface
{
    public function inspect(InspectableInterface $inspectable): void;
}
