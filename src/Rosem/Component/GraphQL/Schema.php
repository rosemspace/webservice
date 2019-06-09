<?php

namespace Rosem\Component\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use Psr\Container\ContainerInterface;
use Rosem\Contract\GraphQL\{
    AbstractSchema, NodeType, QueryInterface, TypeRegistryInterface
};

class Schema extends AbstractSchema
{
    /**
     * The container with types.
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var TypeRegistryInterface
     */
    protected $typeRegistry;

    /**
     * Entries of the schema.
     * @var array
     */
    protected $entries;

    public function __construct(ContainerInterface $container, TypeRegistryInterface $typeRegistry)
    {
        $this->container = $container;
        $this->typeRegistry = $typeRegistry;
    }

    public function addNode(string $type, string $name, callable $nodeFactory): void
    {
        if (\in_array(
            $type,
            [NodeType::QUERY, NodeType::MUTATION, NodeType::SUBSCRIPTION],
            true)
        ) {
            if (\is_array($nodeFactory) && \is_string(reset($nodeFactory))) {
                $nodeFactory[key($nodeFactory)] = $this->container->get(reset($nodeFactory));
            }

            $node = $nodeFactory($this->typeRegistry);

            if ($node instanceof QueryInterface) {
                $nodeArray = [
                    'name'        => $name,
                    'description' => $node->getDescription(),
                    'type'        => $node->getType($this->typeRegistry),
                    'args'        => $node->getArguments($this->typeRegistry),
                    'resolve'     => [$node, 'resolve'],
                ];

                if (!isset($this->entries[$type])) {
                    $this->entries[$type] = [$name => $nodeArray];
                } elseif (!isset($this->entries[$type][$name])) {
                    $this->entries[$type][$name] = $nodeArray;
                } else {
                    throw new \LogicException("The node \"$name\" of the type \"$type\" already exists");
                }
            } else {
                throw new \LogicException('Factory return type should be ' . QueryInterface::class);
            }
        } else {
            throw new \InvalidArgumentException('Invalid entry type of the graph');
        }
    }

    protected function getRootType(string $type): ?array
    {
        if (isset($this->entries[$type])) {
            return [
                'name'   => $type,
                'fields' => $this->entries[$type],
            ];
        }

        return null;
    }

    public function getTree(): array
    {
        $tree = [];

        if ($query = $this->getRootType(NodeType::QUERY)) {
            $tree['query'] = new ObjectType($query);
        }

        if ($mutation = $this->getRootType(NodeType::MUTATION)) {
            $tree['mutation'] = new ObjectType($mutation);
        }

        if ($subscription = $this->getRootType(NodeType::SUBSCRIPTION)) {
            $tree['subscription'] = new ObjectType($subscription);
        }

        return $tree;
    }
}
