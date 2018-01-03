<?php

namespace True\DI;

interface DependentBindingInterface
{
    public function make($context, array $args = []);
}
