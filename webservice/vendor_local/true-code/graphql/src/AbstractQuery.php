<?php

namespace True\GraphQL;

use Analogue\ORM\Analogue;
use GraphQL\Type\Definition\ResolveInfo;
use TrueStandards\GraphQL\GraphInterface;

abstract class AbstractQuery extends \TrueStandards\GraphQL\AbstractQuery
{
    /**
     * @var string
     */
    protected $model;

    /**
     * @var \Analogue\ORM\System\Mapper
     */
    protected $mapper;

    public function __construct(GraphInterface $graph, string $name, string $description, Analogue $db)
    {
        parent::__construct($graph, $name, $description);

        if (class_exists($this->model)) {
            $this->mapper = $db->mapper($this->model);
        } else {
            throw new \Exception("Model \"$this->model\" does not exist");
        }
    }

    public function resolve($source, $args, $context, ResolveInfo $info)
    {
        return $args ? $this->mapper->where($args)->get() : $this->mapper->all();
    }
}
