<?php

namespace Rosem\Contract\Eav;

interface AttributeInterface
{
    public function addValue(ValueInterface $value): void;

    public function getValues(): iterable;
}
