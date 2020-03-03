<?php

namespace Rosem\Component\Container;

use Rosem\Contract\Container\ServiceProviderInterface;

/**
 * Class AbstractAggregateServiceProvider.
 *
 * @package Rosem\Component\Container
 */
abstract class AbstractAggregateServiceProvider implements ServiceProviderInterface
{
    /**
     * @var ServiceProviderInterface[]
     */
    protected array $serviceProviders;

    /**
     * AbstractAggregateServiceProvider constructor.
     *
     * @param array $serviceProviderClassList
     */
    public function __construct(array $serviceProviderClassList)
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
