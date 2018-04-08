<?php

namespace Rosem\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\SchemaConfig;
use Psr\Container\ContainerInterface;
use Psrnext\GraphQL\AbstractSchema;
use Psrnext\GraphQL\NodeInterface;
use GraphQL\Type\Schema as GraphQLSchema;

class Schema extends AbstractSchema
{
    /**
     * The container with types.
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Entries of the schema.
     *
     * @var array
     */
    protected $entries;

    public function __construct(ContainerInterface $container)
    {
        $this->container = new TypeRegistry($container);
    }

    public function addNode(string $type, string $name, callable $nodeFactory): void
    {
        if (\in_array(
            $type,
            [self::NODE_TYPE_QUERY, self::NODE_TYPE_MUTATION, self::NODE_TYPE_SUBSCRIPTION],
            true)
        ) {
            if (\is_array($nodeFactory) && \is_string(reset($nodeFactory))) {
                $nodeFactory[key($nodeFactory)] = $this->container->get(reset($nodeFactory));
            }

            $node = \call_user_func($nodeFactory, $this->container);

            if ($node instanceof NodeInterface) {
                $nodeArray = $node->create($this->container, $name);

                if (!isset($this->entries[$type])) {
                    $this->entries[$type] = [$name => $nodeArray];
                } elseif (!isset($this->entries[$type][$name])) {
                    $this->entries[$type][$name] = $nodeArray;
                } else {
                    throw new \LogicException("The node \"$name\" of the type \"$type\" already exists");
                }
            } else {
                throw new \LogicException('Factory return type should be ' . NodeInterface::class);
            }
        } else {
            throw new \InvalidArgumentException('Invalid entry type of the graph');
        }
    }

    protected function getRootType(string $type): ?ObjectType
    {
        if (isset($this->entries[$type])) {
            return new ObjectType([
                'name'   => $type,
                'fields' => $this->entries[$type],
            ]);
        }

        return null;
    }

    public function create(): GraphQLSchema
    {
        $schemaConfig = SchemaConfig::create();

        if ($query = $this->getRootType(self::NODE_TYPE_QUERY)) {
            $schemaConfig->setQuery($query);
        }

        if ($mutation = $this->getRootType(self::NODE_TYPE_MUTATION)) {
            $schemaConfig->setMutation($mutation);
        }

        if ($subscription = $this->getRootType(self::NODE_TYPE_SUBSCRIPTION)) {
            $schemaConfig->setSubscription($subscription);
        }

        return new GraphQLSchema($schemaConfig);
    }
}
