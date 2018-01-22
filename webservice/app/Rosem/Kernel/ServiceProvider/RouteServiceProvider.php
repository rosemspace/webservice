<?php

namespace Rosem\Kernel\ServiceProvider;

use Psr\Container\ContainerInterface;
use TrueStd\Container\ServiceProviderInterface;

class RouteServiceProvider implements ServiceProviderInterface
{
    const TYPE_CHAR_COUNT  = 0;
    const TYPE_GROUP_COUNT = 1;
    const TYPE_GROUP_POS   = 2;
    const TYPE_MARK        = 3;

    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories() : array
    {
        return [
            \FastRoute\RouteCollector::class     => [static::class, 'createRouteCollector'],
            \FastRoute\Dispatcher::class         => [static::class, 'createSimpleRouteDispatcher'],
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     */
    public function getExtensions() : array
    {
        return [
            \FastRoute\RouteCollector::class => function (\FastRoute\RouteCollector $r) {
                $r->addRoute('GET', '/users', 'get_all_users_handler');
                // {id} must be a number (\d+)
                $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
                // The /{title} suffix is optional
                $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
            },
        ];
    }

    public function createRouteCollector(ContainerInterface $container)
    {
        switch ($container->get('kernel')['router']) {
            case self::TYPE_CHAR_COUNT:
                $dataGenerator = \FastRoute\DataGenerator\CharCountBased::class;
                break;
            case self::TYPE_GROUP_POS:
                $dataGenerator = \FastRoute\DataGenerator\GroupPosBased::class;
                break;
            case self::TYPE_MARK:
                $dataGenerator = \FastRoute\DataGenerator\MarkBased::class;
                break;
            default:
                $dataGenerator = \FastRoute\DataGenerator\GroupCountBased::class;
        }

        return new \FastRoute\RouteCollector(new \FastRoute\RouteParser\Std, new $dataGenerator);
    }

    public function createSimpleRouteDispatcher(ContainerInterface $container)
    {
        switch ($container->get('kernel')['router']) {
            case self::TYPE_CHAR_COUNT:
                $dispatcher = \FastRoute\Dispatcher\CharCountBased::class;
                break;
            case self::TYPE_GROUP_POS:
                $dispatcher = \FastRoute\Dispatcher\GroupPosBased::class;
                break;
            case self::TYPE_MARK:
                $dispatcher = \FastRoute\Dispatcher\MarkBased::class;
                break;
            default:
                $dispatcher = \FastRoute\Dispatcher\GroupCountBased::class;
        }

        return new $dispatcher($container->get(\FastRoute\RouteCollector::class)->getData());
    }
}
