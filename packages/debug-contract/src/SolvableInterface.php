<?php

namespace Rosem\Contract\Debug;

interface SolvableInterface
{
    public function getSolution(?int $code = null): string;
}
