<?php

namespace TrueStandards\GraphQL;

use GraphQL\Type\Definition\ResolveInfo;

abstract class AbstractQuery implements QueryInterface
{
    protected $name;

    protected $description;

    /**
     * @var GraphInterface
     */
    protected $graph;

    /**
     * @var string
     */
    protected $model;

    /**
     * @var \Analogue\ORM\System\Mapper
     */
    protected $mapper;

    public function __construct(GraphInterface $graph, \Analogue\ORM\Analogue $db)
    {
        $this->graph = $graph;
        $this->mapper = $db->mapper($this->model);
    }

    public function args() : array
    {
        return [];
    }

    public function resolve($source, $args, $context, ResolveInfo $info) {}

    public function getName() : string
    {
        return $this->name;
    }

    public function getDescription() : string
    {
        return $this->description;
    }

    public function toArray() : array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'type' => $this->type(),
            'args' => $this->args(),
            'resolve' => \Closure::fromCallable([$this, 'resolve']),
        ];
    }
}
