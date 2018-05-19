<?php

namespace Rosem\Doctrine\Provider;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\Common\Proxy\AbstractProxyFactory;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Psr\Container\ContainerInterface;
use Psrnext\Config\ConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Environment\EnvironmentInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    public const PROXY_NAMESPACE = 'Rosem\Doctrine\ORM\GeneratedProxies';

    public function getFactories(): array
    {
        return [
            'ormEntityPaths'   => function (): array {
                return [
                    getcwd() . '/../app/Rosem/Access/Entity' //TODO: move into Access lib
                ];
            },
            EntityManager::class => function (ContainerInterface $container) {
                $isDevelopmentMode = $container->get(EnvironmentInterface::class)->isDevelopmentMode();
                $dbConfig = $container->get(ConfigInterface::class)->get('database');
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

                    $ormConfig->setAutoGenerateProxyClasses(AbstractProxyFactory::AUTOGENERATE_EVAL);
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
