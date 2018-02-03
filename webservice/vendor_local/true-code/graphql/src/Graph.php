<?php

namespace True\GraphQL;

use GraphQL\Type\{
    Definition\ObjectType, Schema, SchemaConfig
};
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use TrueStandards\GraphQL\{
    GraphInterface, QueryInterface
};

class Graph implements GraphInterface
{
    protected $container;

    /**
     * @var \TrueCode\Container\Definition\DefinitionInterface[]
     */
    protected $types   = [];

    protected $schemas = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    protected function addEntity(string $type, string $name, QueryInterface $query, string $schema = 'default')
    {
        if (! isset($this->schemas[$schema])) {
            $this->schemas[$schema] = [$type => [$name => $query->toArray()]];
        } elseif (! isset($this->schemas[$schema][$type])) {
            $this->schemas[$schema][$type] = [$name => $query->toArray()];
        } elseif (! isset($this->schemas[$schema][$type][$query->getName()])) {
            $this->schemas[$schema][$type][$name] = $query->toArray();
        }
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $description
     *
     * @throws \Exception
     */
    public function addType(string $class, string $name, string $description) : void
    {
        try {
            // TODO: improve
            $this->container->share($class, $class, [$name, $description])->commit();
            $this->types[$name] = $class;
        } catch (ContainerExceptionInterface $e) {
            throw new \Exception("Cannot add type $name to graph");
        }
    }

    /**
     * @param string $class
     * @param string $name
     * @param string $description
     * @param string $schema
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function addQuery(string $class, string $name, string $description, string $schema = 'default') : void
    {
        $this->addEntity(
            'Query',
            $name,
            $this->container->share($class, $class, [$name, $description])->make(),
            $schema
        );
    }

    public function addMutation(string $class, string $name, string $description, string $schema = 'default') : void
    {
        $this->addEntity(
            'Mutation',
            $name,
            $this->container->share($class, $class, [$name, $description])->make(),
            $schema
        );
    }

    public function addSubscription(string $class, string $name, string $description, string $schema = 'default') : void
    {
        $this->addEntity(
            'Mutation',
            $name,
            $this->container->share($class, $class, [$name, $description])->make(),
            $schema
        );
    }

    public function getType(string $name)
    {
        return $this->container->get($this->types[$name]);
    }

    protected function getRootType(string $schema, string $type): ?ObjectType
    {
        if (isset($this->schemas[$schema][$type])) {
            return new ObjectType([
                'name'   => $type,
                'fields' => $this->schemas[$schema][$type],
            ]);
        }

        return null;
    }

    public function getSchema(string $schema = 'default')
    {
        $schemaConfig = SchemaConfig::create();

        if ($query = $this->getRootType($schema, 'Query')) {
            $schemaConfig->setQuery($query);
        }

        if ($mutation = $this->getRootType($schema, 'Mutation')) {
            $schemaConfig->setMutation($mutation);
        }

        if ($subscription = $this->getRootType($schema, 'Subscription')) {
            $schemaConfig->setSubscription($subscription);
        }

        return new Schema($schemaConfig);
    }
}
