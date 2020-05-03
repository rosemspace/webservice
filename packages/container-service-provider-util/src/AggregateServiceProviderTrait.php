<?php

namespace Rosem\Component\Container;

use Rosem\Contract\Container\ServiceProviderInterface;

use function get_class;

/**
 * Trait AbstractAggregateServiceProvider.
 *
 * @see \Rosem\Contract\Container\ServiceProviderInterface
 */
trait AggregateServiceProviderTrait
{
    /**
     * @var ServiceProviderInterface[]
     */
    protected array $serviceProviders;

    /**
     * AbstractAggregatedServiceProvider constructor.
     *
     * @param array $serviceProviderClassList
     *
     * @return void
     */
    public function addServiceProviders(array $serviceProviderClassList): void
    {
        foreach ($serviceProviderClassList as $serviceProviderClass) {
            // todo same validation as in ServiceContainer
            $this->serviceProviders[] = new $serviceProviderClass();
        }
    }

    /**
     * @inheritDoc
     */
    public function getFactories(): array
    {
        $factories = [];

        foreach ($this->serviceProviders as $serviceProvider) {
            $factories = [get_class($serviceProvider) => fn() => $serviceProvider] +
                $serviceProvider->getFactories() +
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

        foreach ($this->serviceProviders as $serviceProvider) {
            /** @noinspection AdditionOperationOnArraysInspection */
            $extensions = $serviceProvider->getExtensions() + $extensions;
        }

        return $extensions;
    }
}
