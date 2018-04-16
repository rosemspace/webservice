<?php

namespace Rosem\GraphQL;

use GraphQL\Type\Definition\ObjectType;
use Psr\Container\{
    ContainerExceptionInterface, ContainerInterface, NotFoundExceptionInterface
};
use Psrnext\GraphQL\ObjectTypeInterface;
use Psrnext\GraphQL\TypeRegistryInterface;

final class TypeRegistry implements TypeRegistryInterface
{
    /**
     * App container
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var ObjectTypeInterface[]
     */
    private $types = [];

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @return mixed Entry.
     */
    public function get($id)
    {
        $placeholder = &$this->types[$id];

        if (null === $placeholder) {
            // TODO: improve and add exception when not a type
            $type = $this->container->get($id);

            if (!$type instanceof ObjectTypeInterface) {
                throw new \Exception('Invalid type');
            }

            $placeholder = new ObjectType([
                'name'        => $type->getName(),
                'description' => $type->getDescription(),
                'fields'      => function () use (&$type) {
                    return $type->getFields($this);
                },
            ]);
            $this->types[$type->getName()] = &$placeholder; // alias
        }

        return $placeholder;
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return $this->container->has($id);
    }
}
