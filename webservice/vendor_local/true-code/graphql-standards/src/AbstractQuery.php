<?php

namespace TrueStandards\GraphQL;

abstract class AbstractQuery implements QueryInterface
{
    /**
     * @var GraphInterface
     */
    protected $graph;

    protected $name;

    protected $description;

    public function __construct(GraphInterface $graph, string $name, string $description)
    {
        $this->graph = $graph;
        $this->name = $name;
        $this->description = $description;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function args() : array
    {
        return [];
    }

    public function toArray() : array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
            'type'        => $this->type(),
            'args'        => $this->args(),
            'resolve'     => \Closure::fromCallable([$this, 'resolve']),
        ];
    }
}
