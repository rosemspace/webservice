<?php

namespace TrueCode\Container\Binding;

interface DependentBindingInterface
{
    public function make($context, array $args = []);
}
