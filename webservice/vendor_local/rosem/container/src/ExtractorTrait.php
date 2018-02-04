<?php

namespace Rosem\Container;

trait ExtractorTrait
{
    protected function &extractFirst(array &$args)
    {
        if (false === $value = reset($args)) {
            $value = [];
        }

        unset($args[key($args)]);

        return $value;
    }
}
