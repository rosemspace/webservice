<?php

namespace Rosem\Component\GraphQL;

use Psr\Container\ContainerInterface;
use Rosem\Contract\App\AppInterface;
use Rosem\Contract\Http\Server\MiddlewareCollectorInterface;

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
            MiddlewareCollectorInterface::class => function (
                ContainerInterface $container,
                MiddlewareCollectorInterface $middlewareCollector
            ) {
                $middlewareCollector->addDeferredMiddleware(GraphQLMiddleware::class);
            },
        ];
    }

    public function createGraphQLMiddleware(ContainerInterface $container): GraphQLMiddleware
    {
        return new GraphQLMiddleware(
            $this->createGraphQLServer($container),
            $container->get(static::CONFIG_URI),
            true //todo $container->isAllowedToDebug()
        );
    }
}
