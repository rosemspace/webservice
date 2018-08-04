<?php

namespace Psrnext\Eav;

interface AttributeInterface
{
    public function addValue(ValueInterface $value): void;

    public function getValues(): iterable;
}
