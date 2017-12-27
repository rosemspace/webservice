<?php

namespace TrueStandards\GraphQL;

use Closure;
use GraphQL\Type\Definition\{Type, ObjectType};
use GraphQL\Type\Definition\ResolveInfo;

abstract class AbstractObjectType extends ObjectType implements ObjectTypeInterface
{
    /**
     * @var GraphInterface
     */
    protected $graph;

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

    public function type(string $typeName)
    {
        return $this->graph->getType($typeName);
    }

    final public static function field(Type $type, ?string $description = null) : array
    {
        $field = ['type' => $type];

        if ($description) {
            $filed['description'] = $description;
        }

        return $field;
    }
}
