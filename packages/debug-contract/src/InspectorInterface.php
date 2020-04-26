<?php

namespace Rosem\Contract\Debug;

interface InspectorInterface
{
    public function inspect(InspectableInterface $inspectable): void;
}
