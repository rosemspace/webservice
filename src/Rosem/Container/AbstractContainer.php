<?php

namespace Rosem\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Rosem\Container\Exception;

abstract class AbstractContainer implements ContainerInterface
{
    /**
     * @var mixed[]|Definition[]
     */
    protected $definitions;

    /**
     * @var ContainerInterface
     */
    protected $delegator;

    /**
     * @var ContainerInterface
     */
    protected $delegate;

    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
        $this->delegator = $this;
    }

    /**
     * @param string $filePath
     *
     * @return mixed
     * @throws \Exception
     */
    public static function getConfigurationFromFile(string $filePath): array
    {
        if (file_exists($filePath)) {
            if (is_readable($filePath)) {
                $config = include $filePath;

                if (\is_array($config)) {
                    return $config;
                }

                throw new Exception\ContainerException(
                    "$filePath configuration file should return an array"
                );
            }

            throw new Exception\ContainerException(
                "$filePath configuration file does not readable"
            );
        }

        throw new Exception\ContainerException(
            "$filePath configuration file does not exists"
        );
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

        if ($this->delegate) {
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
        $delegate->delegator = $this;
        $this->delegate = $delegate;
    }
}
