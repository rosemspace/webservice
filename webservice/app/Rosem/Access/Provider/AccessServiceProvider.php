<?php

namespace Rosem\Access\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\GraphQL\GraphInterface;
use Psrnext\GraphQL\NodeInterface;

class AccessServiceProvider implements ServiceProviderInterface
{
    public function getFactories(): array
    {
        return [
            'db'       => function () {
                $applicationMode = 'development';
                $config = new \Doctrine\ORM\Configuration;
                $namingStrategy = new \Doctrine\ORM\Mapping\UnderscoreNamingStrategy(CASE_LOWER);
                $config->setNamingStrategy($namingStrategy);

                if ($applicationMode === 'development') {
                    $cache = new \Doctrine\Common\Cache\ArrayCache;
                } else {
                    $cache = new \Doctrine\Common\Cache\ApcuCache;
                }

                $config->setMetadataCacheImpl($cache);
                $metadataDriver = new \Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver(
                    getcwd() . '/../app/Rosem/Access/Entity'
                );
                $config->setMetadataDriverImpl($metadataDriver);
                $config->setQueryCacheImpl($cache);
                $config->setProxyDir(getcwd() . '/../var/db/proxies');
                $config->setProxyNamespace('Rosem\Database\GeneratedProxies');
                $config->setAutoGenerateProxyClasses($applicationMode === 'development');

                if ('development' === $applicationMode) {
                    $config->setAutoGenerateProxyClasses(\Doctrine\Common\Proxy\AbstractProxyFactory::AUTOGENERATE_EVAL);
                }

                $entityManager = \Doctrine\ORM\EntityManager::create([
                    'dbname' => 'rosem',
                    'user' => 'root',
                    'password' => '',
                    'host' => 'localhost',
                    'driver' => 'pdo_mysql',
                ], $config);

                return $entityManager;
            },
            'User'     => function (): NodeInterface {
                return new \Rosem\Access\Http\GraphQL\Types\UserType;
            },
            'UserRole' => function (): NodeInterface {
                return new \Rosem\Access\Http\GraphQL\Types\UserRoleType;
            },
        ];
    }

    public function getExtensions(): array
    {
        return [
            GraphInterface::class => function (ContainerInterface $container, GraphInterface $graph) {
                $graph->schema()->query('users', function (): NodeInterface {
                    return new \Rosem\Access\Http\GraphQL\Queries\UsersQuery;
                });
                $graph->schema()->mutation('updateUser', function (): NodeInterface {
                    return new \Rosem\Access\Http\GraphQL\Mutations\UpdateUserMutation;
                });
            },
        ];
    }
}
