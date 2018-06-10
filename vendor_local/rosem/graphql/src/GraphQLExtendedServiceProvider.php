<?php

namespace Rosem\GraphQL;

use Psr\Container\ContainerInterface;
use Psrnext\Config\ConfigInterface;
use Psrnext\Environment\EnvironmentInterface;
use Psrnext\Http\Server\MiddlewareQueueInterface;
use Rosem\Http\Server\LazyMiddleware;

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
            MiddlewareQueueInterface::class => function (
                ContainerInterface $container,
                MiddlewareQueueInterface $middlewareProcessor
            ) {
                $middlewareProcessor->use(new LazyMiddleware($container, GraphQLMiddleware::class));
            },
        ];
    }

    public function createGraphQLMiddleware(ContainerInterface $container): GraphQLMiddleware
    {
        return new GraphQLMiddleware(
            $this->createGraphQLServer($container),
            $container->get(ConfigInterface::class)->get(static::CONFIG_URI, '/graphql'),
            $container->get(EnvironmentInterface::class)->isDevelopmentMode()
        );
    }
}
