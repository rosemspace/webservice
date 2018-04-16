<?php

namespace Rosem\GraphQL\Provider;

use GraphQL\Server\StandardServer;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema as GraphQLSchema;
use GraphQL\Type\SchemaConfig;
use Psr\Container\ContainerInterface;
use Psrnext\{
    Container\ServiceProviderInterface, Environment\EnvironmentInterface, GraphQL\GraphInterface, GraphQL\TypeRegistryInterface
};
use Psrnext\Config\ConfigInterface;
use Rosem\GraphQL\Graph;
use Rosem\GraphQL\Middleware\GraphQLMiddleware;
use Rosem\GraphQL\Schema;
use Rosem\GraphQL\TypeRegistry;

class GraphQLServiceProvider implements ServiceProviderInterface
{
    /**
     * GraphQL schema config key.
     */
    public const CONFIG_SCHEMA = 'graphql.schema';

    /**
     * GraphQL uri config key.
     */
    public const CONFIG_URI = 'graphql.uri';

    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     *
     * @return callable[]
     */
    public function getFactories() : array
    {
        return [
            TypeRegistryInterface::class => [static::class, 'createGraphQLTypeRegistry'],
            GraphInterface::class        => [static::class, 'createGraphQLGraph'],
            GraphQLMiddleware::class     => [static::class, 'createGraphQLMiddleware'],
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
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null` will be passed.
     *
     * @return callable[]
     */
    public function getExtensions() : array
    {
        return [];
    }

    public function createGraphQLTypeRegistry(ContainerInterface $container): TypeRegistry
    {
        return new TypeRegistry($container);
    }

    public function createGraphQLGraph(ContainerInterface $container): Graph
    {
        $graph = new Graph;
        $graph->addSchema('default', new Schema($container, $container->get(TypeRegistryInterface::class)));

        return $graph;
    }

    public function createGraphQLMiddleware(ContainerInterface $container): GraphQLMiddleware
    {
        $config = $container->get(ConfigInterface::class);
        $schema = $container->get(GraphInterface::class)
            ->schema($config->get(static::CONFIG_SCHEMA, 'default'));
        $schemaConfig = SchemaConfig::create();
        $typeRegistry = $container->get(TypeRegistryInterface::class);
        $schemaConfig->setTypeLoader(function ($name) use (&$typeRegistry) {
            return $typeRegistry->get($name);
        });

        if ($query = $schema->getQueryData()) {
            $schemaConfig->setQuery(new ObjectType($query));
        }

        if ($mutation = $schema->getMutationData()) {
            $schemaConfig->setMutation(new ObjectType($mutation));
        }

        if ($subscription = $schema->getSubscriptionData()) {
            $schemaConfig->setSubscription(new ObjectType($subscription));
        }

        return new GraphQLMiddleware(
            new StandardServer([
                'schema'  => new GraphQLSchema($schemaConfig),
                'context' => $container,
                'fieldResolver' => function ($source, $args, $context, ResolveInfo $info) {
                    $fieldName = $info->fieldName;
                    $property = null;

                    if (\is_array($source) || $source instanceof \ArrayAccess) {
                        if (isset($source[$fieldName])) {
                            $property = $source[$fieldName];
                        }
                    } else if (\is_object($source)) {
                        if (isset($source->{$fieldName})) {
                            $property = $source->{$fieldName};
                        } else {
                            $method = 'get' . ucfirst($fieldName);

                            if (method_exists($source, $method)) {
                                $property = $source->$method();
                            }
                        }
                    }

                    return $property instanceof \Closure ? $property($source, $args, $context) : $property;
                }
            ]),
            $config->get(static::CONFIG_URI, '/graphql'),
            $container->get(EnvironmentInterface::class)->isDevelopmentMode()
        );
    }
}
