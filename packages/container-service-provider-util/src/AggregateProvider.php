<?php

declare(strict_types=1);

namespace Rosem\Component\Container;

use Rosem\Contract\Container\ServiceProviderInterface;

use function get_class;

/**
 * Trait AbstractAggregateServiceProvider.
 * @TODO rename to AggregateServiceProvider
 */
class AggregateProvider implements ServiceProviderInterface
{
    /**
     * Providers collection.
     *
     * @var ServiceProviderInterface[]
     */
    protected array $providers;

    /**
     * Add the service provider to the collection.
     *
     * @param ServiceProviderInterface $serviceProvider
     *
     * @return void
     */
    public function addProvider(ServiceProviderInterface $serviceProvider): void
    {
        // todo same validation as in ServiceContainer
        $this->providers[] = $serviceProvider;
    }

    /**
     * @inheritDoc
     */
    public function getFactories(): array
    {
        $factories = [];

        foreach ($this->providers as $provider) {
            $factories = [get_class($provider) => fn() => $provider] +
                $provider->getFactories() +
                $factories;
        }

        return $factories;
    }

    /**
     * @inheritDoc
     */
    public function getExtensions(): array
    {
        $extensions = [];

        foreach ($this->providers as $provider) {
            /** @noinspection AdditionOperationOnArraysInspection */
            $extensions = $provider->getExtensions() + $extensions;
        }

        return $extensions;
    }
}
