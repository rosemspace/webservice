<?php

namespace True\DI\Binding;

interface DependentBindingInterface
{
    public function make($context, array $args = []);
}
