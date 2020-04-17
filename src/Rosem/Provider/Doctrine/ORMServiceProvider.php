<?php

namespace Rosem\Provider\Doctrine;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Persistence\Mapping\Driver\StaticPHPDriver;
use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;
use Psr\Container\ContainerInterface;
use Rosem\Contract\App\AppEnv;
use Rosem\Contract\App\AppEnvKey;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\App\DirEnvVar;

class ORMServiceProvider implements ServiceProviderInterface
{
    public const PROXY_NAMESPACE = 'Proxies\__CG__';

    public function getFactories(): array
    {
        return [
            'ormEntityPaths' => static function (ContainerInterface $container): array {
                return [
                    $container->get(DirEnvVar::ROOT) .
                        '/src/Rosem/Component/Access/Entity' //TODO: improve
                ];
            },
            EntityManager::class => function (ContainerInterface $container) {
                $isDevelopmentMode = $container->get(AppEnvKey::ENV) === AppEnv::DEVELOPMENT;
                $ormConfig = new Configuration();
                $ormConfig->setNamingStrategy(new UnderscoreNamingStrategy(CASE_LOWER));
                $ormConfig->setMetadataDriverImpl(new StaticPHPDriver($container->get('ormEntityPaths')));
                $ormConfig->setProxyDir(getcwd() . '/../var/database/proxies');
                $ormConfig->setProxyNamespace(self::PROXY_NAMESPACE);
                $ormConfig->setAutoGenerateProxyClasses($isDevelopmentMode);

                if ($isDevelopmentMode) {
                    $dbConfig = [
                        'driver' => $container->has('database.driver')
                            ? $container->get('database.driver')
                            : 'pdo_mysql',
                        'host' => $container->has('database.host')
                            ? $container->get('database.host')
                            : 'localhost',
                        'name' => $container->get('database.name'),
                        'username' => $container->has('database.username')
                            ? $container->get('database.username')
                            : 'root',
                        'password' => $container->has('database.password')
                            ? $container->get('database.password')
                            : '',
                    ];
                    $cache = new ArrayCache;
                } else {
                    $dbConfig = [
                        'driver' => $container->get('database.driver'),
                        'host' => $container->get('database.host'),
                        'name' => $container->get('database.name'),
                        'username' => $container->get('database.username'),
                        'password' => $container->get('database.password'),
                    ];
                    $cache = new ApcuCache;
                }

                $ormConfig->setMetadataCacheImpl($cache);
                $ormConfig->setQueryCacheImpl($cache);
                $entityManager = EntityManager::create([
                    'driver' => $dbConfig['driver'],
                    'host' => $dbConfig['host'],
                    'dbname' => $dbConfig['name'],
                    'user' => $dbConfig['username'],
                    'password' => $dbConfig['password'],
                    //TODO
                    //DATABASE_PORT
                    //DATABASE_CHARSET => 'utf-8'
                    //DATABASE_ENGINE
                    //DATABASE_COLLATION => 'utf8_unicode_ci'
                    //DATABASE_PREFIX => ''
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
