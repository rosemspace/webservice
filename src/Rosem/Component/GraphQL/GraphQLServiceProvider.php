<?php

namespace Rosem\Component\GraphQL;

use Fig\Http\Message\RequestMethodInterface;
use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema as GraphQLSchema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\DisableIntrospection;
use GraphQL\Validator\Rules\QueryComplexity;
use GraphQL\Validator\Rules\QueryDepth;
use Psr\Container\ContainerInterface;
use Rosem\Contract\{
    Container\ServiceProviderInterface, Environment\EnvironmentInterface, GraphQL\GraphInterface, GraphQL\TypeRegistryInterface, Route\RouteCollectorInterface
};
use function Rosem\Component\String\snakeCase;

class GraphQLServiceProvider implements ServiceProviderInterface
{
    /**
     * GraphQL uri config key.
     */
    public const CONFIG_URI = 'graphql.uri';

    /**
     * GraphQL schema config key.
     */
    public const CONFIG_SCHEMA = 'graphql.schema';

    /**
     * Maximum query complexity config key.
     */
    public const CONFIG_MAX_QUERY_COMPLEXITY = 'graphql.maxQueryComplexity';

    /**
     * Maximum query complexity config key.
     */
    public const CONFIG_MAX_QUERY_DEPTH = 'graphql.maxQueryDepth';

    /**
     * GraphQL debug config key.
     */
    public const CONFIG_DEBUG = 'graphql.debug';

    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_URI => function (): string {
                return '/graphql';
            },
            static::CONFIG_SCHEMA => function (): string {
                return 'default';
            },
            static::CONFIG_MAX_QUERY_COMPLEXITY => function (): int {
                return 200;
            },
            static::CONFIG_MAX_QUERY_DEPTH => function (): int {
                return 20;
            },
            static::CONFIG_DEBUG => function (ContainerInterface $container): bool {
                if ($container->has(EnvironmentInterface::class)) {
                    return $container->get(EnvironmentInterface::class)->isDevelopmentMode();
                }

                return false;
            },
            TypeRegistryInterface::class => [static::class, 'createGraphQLTypeRegistry'],
            GraphInterface::class => [static::class, 'createGraphQLGraph'],
            GraphQLRequestHandler::class => [static::class, 'createGraphQLRequestHandler'],
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the modified entry
     * Callables have the following signature:
     *        function(Psr\Container\ContainerInterface $container, $previous)
     *     or function(Psr\Container\ContainerInterface $container, $previous = null)
     * About factories parameters:
     * - the container (instance of `Psr\Container\ContainerInterface`)
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null`
     * will be passed.
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->addRoute(
                    [RequestMethodInterface::METHOD_GET, RequestMethodInterface::METHOD_POST],
                    $container->get(static::CONFIG_URI),
                    GraphQLRequestHandler::class
                );
            },
        ];
    }

    public function createGraphQLTypeRegistry(ContainerInterface $container): TypeRegistry
    {
        return new TypeRegistry($container);
    }

    public function createGraphQLGraph(ContainerInterface $container): Graph
    {
        $graph = new Graph();
        $graph->addSchema('default', new Schema($container, $container->get(TypeRegistryInterface::class)));

        return $graph;
    }

    protected function createGraphQLServer(ContainerInterface $container): StandardServer
    {
        // set max query complexity
        DocumentValidator::addRule(new QueryComplexity($container->get(static::CONFIG_MAX_QUERY_COMPLEXITY)));
        // set max query depth
        DocumentValidator::addRule(new QueryDepth($container->get(static::CONFIG_MAX_QUERY_DEPTH)));
//        DocumentValidator::addRule(new DisableIntrospection());
        $schema = $container->get(GraphInterface::class)->getSchema($container->get(static::CONFIG_SCHEMA));
        $schemaConfig = SchemaConfig::create($schema->getTree());
        $typeRegistry = $container->get(TypeRegistryInterface::class);
        $schemaConfig->setTypeLoader(function ($name) use (&$typeRegistry) {
            return $typeRegistry->get($name);
        });

        return new StandardServer([
            'schema' => new GraphQLSchema($schemaConfig),
            'context' => $container,
            'fieldResolver' => function ($source, $args, $context, ResolveInfo $info) {
                $fieldName = $info->fieldName;
                $property = null;

                if ((\is_array($source) || $source instanceof \ArrayAccess) && isset($source[$fieldName])) {
                    $property = $source[$fieldName];
                } elseif (\is_object($source)) {
                    if (isset($source->{$fieldName})) {
                        $property = $source->{$fieldName};
                    } else {
                        $method = 'get' . ucfirst($fieldName);

                        if (method_exists($source, $method)) {
                            $property = $source->$method();
                        } elseif (method_exists($source, 'get')) {
                            $property = $source->get(snakeCase($fieldName));
                        }
                    }
                }

                return $property instanceof \Closure ? $property($source, $args, $context) : $property;
            },
        ]);
    }

    public function createGraphQLRequestHandler(ContainerInterface $container): GraphQLRequestHandler
    {
        return new GraphQLRequestHandler($this->createGraphQLServer($container), $container->get(static::CONFIG_DEBUG));
    }
}
