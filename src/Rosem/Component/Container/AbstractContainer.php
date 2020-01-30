<?php
declare(strict_types=1);

namespace Rosem\Component\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Rosem\Component\Container\Exception;

/**
 * Class AbstractContainer.
 *
 * @package Rosem\Component\Container
 */
abstract class AbstractContainer implements ContainerInterface
{
    use ConfigFileTrait;

    /**
     * @var mixed[]|Definition[]
     */
    protected array $definitions;

    /**
     * @var ContainerInterface|null
     */
    protected ?ContainerInterface $delegate = null;

    /**
     * AbstractContainer constructor.
     *
     * @param array $definitions
     */
    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
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
        if ($this->has($id)) {
            return $this->definitions[$id];
        }

        if ($this->delegate !== null) {
            return $this->delegate->get($id);
        }

        return Exception\NotFoundException::notFound($id);
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
    public function has($id): bool
    {
        return isset($this->definitions[$id]);
    }

    public function delegate(self $delegate): void
    {
        $this->delegate = $delegate;
    }
}
