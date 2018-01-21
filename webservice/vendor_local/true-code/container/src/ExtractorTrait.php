<?php

namespace TrueCode\Container;

trait ExtractorTrait
{
    protected function &extractFirst(array &$args)
    {
        if ($value = reset($args)) {
            unset($args[key($args)]);
        } else {
            $value = [];
        }

        return $value;
    }
}
