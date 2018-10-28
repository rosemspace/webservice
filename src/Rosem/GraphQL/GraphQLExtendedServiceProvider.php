<?php

namespace Rosem\GraphQL;

use Psr\Container\ContainerInterface;
use Rosem\Psr\Environment\EnvironmentInterface;
use Rosem\Psr\Http\Server\MiddlewareDispatcherInterface;

class GraphQLExtendedServiceProvider extends GraphQLServiceProvider
{
    public function getFactories(): array
    {
        return parent::getFactories() + [
            GraphQLMiddleware::class => [static::class, 'createGraphQLMiddleware'],
        ];
    }

    public function getExtensions(): array
    {
        return [
            MiddlewareDispatcherInterface::class => function (
                ContainerInterface $container,
                MiddlewareDispatcherInterface $dispatcher
            ) {
                $dispatcher->use(GraphQLMiddleware::class);
            },
        ];
    }

    public function createGraphQLMiddleware(ContainerInterface $container): GraphQLMiddleware
    {
        return new GraphQLMiddleware(
            $this->createGraphQLServer($container),
            $container->get(static::CONFIG_URI),
            $container->get(EnvironmentInterface::class)->isDevelopmentMode()
        );
    }
}
