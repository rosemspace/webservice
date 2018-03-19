<?php

namespace Rosem\Container\Strategy;

class DoubleStringReflectedCallStrategy extends DoubleStringCallStrategy
{
    public function make($abstract, array ...$args)
    {
        $proceed = false;
        $result = $this->process($abstract, $args, $proceed);

        if ($proceed) {
            return $this->container->getDefinitionStrategy('')->defineClassNow($abstract)->commit()
                ->withMethodCall(next($callable))->call(...$args);
        }

        return $result;
    }
}
