<?php

namespace Psrnext\GraphQL;

abstract class AbstractObjectType extends AbstractNode implements ObjectTypeInterface
{
    /**
     * @var array
     */
    protected $fields = [];

    public function addFields(array $fields): void
    {
        /** @noinspection AdditionOperationOnArraysInspection */
        $this->fields += $fields;
    }
}
