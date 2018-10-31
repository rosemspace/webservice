<?php

namespace Rosem\Doctrine;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Psr\Container\ContainerInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\Environment\EnvironmentInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    public const PROXY_NAMESPACE = 'Rosem\Doctrine\ORM\GeneratedProxies';

    public function getFactories(): array
    {
        return [
            'ormEntityPaths'   => function (ContainerInterface $container): array {
                return [
                    $container->get('app.baseDir') . '/src/Rosem/Access/Entity' //TODO: improve
                ];
            },
            EntityManager::class => function (ContainerInterface $container) {
                $isDevelopmentMode = $container->get(EnvironmentInterface::class)->isDevelopmentMode();
                $dbConfig = $container->get('database');
                $ormConfig = new Configuration;
                $ormConfig->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));
                $ormConfig->setMetadataDriverImpl(new StaticPHPDriver($container->get('ormEntityPaths')));
                $ormConfig->setProxyDir(getcwd() . '/../var/database/proxies');
                $ormConfig->setProxyNamespace(self::PROXY_NAMESPACE);
                $ormConfig->setAutoGenerateProxyClasses($isDevelopmentMode);

                if ($isDevelopmentMode) {
                    if (!isset($dbConfig['username'])) {
                        $dbConfig['username'] = 'root';
                    }

                    if (!isset($dbConfig['password'])) {
                        $dbConfig['password'] = '';
                    }

                    if (!isset($dbConfig['host'])) {
                        $dbConfig['host'] = 'localhost';
                    }

                    if (!isset($dbConfig['driver'])) {
                        $dbConfig['driver'] = 'pdo_mysql';
                    }

                    $cache = new ArrayCache;
                } else {
                    $cache = new ApcuCache;
                }

                $ormConfig->setMetadataCacheImpl($cache);
                $ormConfig->setQueryCacheImpl($cache);
                $entityManager = EntityManager::create([
                    'dbname'   => $dbConfig['name'],
                    'user'     => $dbConfig['username'],
                    'password' => $dbConfig['password'],
                    'host'     => $dbConfig['host'],
                    'driver'   => $dbConfig['driver'],
                ], $ormConfig);

                return $entityManager;
            },
        ];
    }

    public function getExtensions(): array
    {
        return [];
    }
}
