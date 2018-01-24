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

    public function __construct(GraphInterface $graph, string $name, string $description)
    {
        parent::__construct([
            'name'         => $name,
            'description'  => $description,
            'fields'       => Closure::fromCallable([$this, 'fields']),
            'resolveField' => Closure::fromCallable([$this, 'resolveField']),
        ]);

        $this->graph = $graph;
    }

    public function resolveField($value, $args, $context, ResolveInfo $info)
    {
        return $value->{$info->fieldName};
    }
}
