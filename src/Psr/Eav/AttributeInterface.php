<?php

namespace Rosem\Psr\Eav;

interface AttributeInterface
{
    public function addValue(ValueInterface $value): void;

    public function getValues(): iterable;
}
