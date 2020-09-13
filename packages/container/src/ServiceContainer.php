<?php

declare(strict_types=1);

namespace Rosem\Component\Container;

use Exception;
use Psr\Container\{
    ContainerExceptionInterface,
    NotFoundExceptionInterface
};
use Rosem\Component\Container\Exception\{
    ContainerException,
    NotFoundException,
    ServiceProviderException
};
use Rosem\Contract\Container\ServiceProviderInterface;

use function class_exists;
use function class_implements;
use function get_class;
use function in_array;
use function is_object;
use function is_string;

class ServiceContainer extends AbstractContainer
{
    /**
     * Container constructor.
     *
     * @throws ServiceProviderException
     */
    protected function __construct(iterable $serviceProviders)
    {
        parent::__construct();

        AbstractFacade::registerContainer($this->parent ?? $this);

        /** @var ServiceProviderInterface[] $serviceProviderInstances */
        $serviceProviderInstances = [];

        // 1. In the first pass, the container calls the getFactories method of all service providers.
        foreach ($serviceProviders as $serviceProvider) {
            $serviceProviderInstance = $serviceProvider;

            if (is_string($serviceProvider)) {
                if (! class_exists($serviceProvider)) {
                    throw ServiceProviderException::dueToMissingClass($serviceProvider);
                }

                if (! in_array(ServiceProviderInterface::class, class_implements($serviceProvider, true), true)) {
                    //                if (!is_a($serviceProvider, ServiceProviderInterface::class)) {
                    throw ServiceProviderException::dueToInvalidInterface($serviceProvider);
                }

                $serviceProviderInstance = new $serviceProvider();
            } elseif (! is_object($serviceProvider)) {
                throw ServiceProviderException::dueToInvalidType($serviceProvider);
            }

            if ($serviceProviderInstance instanceof ServiceProviderInterface) {
                $serviceProviderInstances[] = $serviceProviderInstance;
                $this->set(
                    $serviceProvider,
                    static function () use ($serviceProviderInstance) {
                        return $serviceProviderInstance;
                    }
                );

                foreach ($serviceProviderInstance->getFactories() as $key => $factory) {
                    $this->set($key, $factory);
                }
            } else {
                throw ServiceProviderException::dueToInvalidInterface(get_class($serviceProviderInstance));
            }
        }

        // 2. In the second pass, the container calls the getExtensions method of all service providers.
        foreach ($serviceProviderInstances as $serviceProviderInstance) {
            foreach ($serviceProviderInstance->getExtensions() as $key => $factory) {
                if ($this->has($key)) {
                    $this->extend($key, $factory);
                }
            }
        }
    }

    /**
     * Create container instance from array configuration.
     *
     * @throws ContainerException
     */
    public static function fromArray(array $definitions): self
    {
        return new static($definitions);
    }

    /**
     * Create container instance from file configuration.
     *
     * @throws ContainerException
     * @throws Exception
     */
    public static function fromFile(string $filename): self
    {
        return self::fromArray(self::getConfigurationFromFile($filename));
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return mixed Entry.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     * @throws NotFoundExceptionInterface No entry was found for **this** identifier.
     */
    public function get($id)
    {
        if ($this->has($id)) {
            $definition = $this->definitions[$id];

            if ($definition instanceof Definition) {
                $value = $definition->create($this->parent ?? $this);

                if ($value !== null) {
                    return $this->definitions[$id] = $value;
                }

                if ($this->child !== null) {
                    return $this->child->get($id);
                }

                throw ContainerException::forUndefinedEntry($id);
            }

            return $definition;
        }

        if ($this->child !== null) {
            return $this->child->get($id);
        }

        throw NotFoundException::dueToMissingEntry($id);
    }

    protected function set(string $id, $factory): void
    {
        $this->definitions[$id] = new Definition($factory);
    }

    protected function extend(string $id, $factory): void
    {
        $this->definitions[$id]->extend($factory);
    }
}
