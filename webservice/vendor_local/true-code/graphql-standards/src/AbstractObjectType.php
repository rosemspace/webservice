<?php

namespace TrueStandards\GraphQL;

use Closure;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

abstract class AbstractObjectType extends ObjectType implements ObjectTypeInterface
{
    /**
     * @var GraphInterface
     */
    protected $graph;

    protected $temp;

    public function __construct(GraphInterface $graph)
    {
        $this->graph = $graph;

        parent::__construct([
            'name'         => $this->name,
            'description'  => $this->description,
            'fields'       => Closure::fromCallable([$this, 'fields']),
            'resolveField' => Closure::fromCallable([$this, 'resolveField']),
        ]);
    }

    public function resolveField($value, $args, $context, ResolveInfo $info)
    {
        return $value->{$info->fieldName};
    }
}
