<?php

namespace Rosem\Kernel\ServiceProvider;

use FastRoute\DataGenerator\{
    CharCountBased as CharCountBasedDataGenerator, GroupCountBased as GroupCountBasedDataGenerator, GroupPosBased as GroupPosBasedDataGenerator, MarkBased as MarkBasedDataGenerator
};
use FastRoute\Dispatcher;
use FastRoute\Dispatcher\{
    CharCountBased as CharCountBasedDispatcher, GroupCountBased as GroupCountBasedDispatcher, GroupPosBased as GroupPosBasedDispatcher, MarkBased as MarkBasedDispatcher
};
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Psr\Container\ContainerInterface;
use Rosem\Kernel\Controller\MainController;
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
            RouteCollector::class     => [static::class, 'createRouteCollector'],
            Dispatcher::class         => [static::class, 'createSimpleRouteDispatcher'],
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
            RouteCollector::class => function (RouteCollector $r) {
                $r->get( '/{home:.*}', [MainController::class, 'index']);

//                // {id} must be a number (\d+)
//                $r->addRoute('GET', '/user/{id:\d+}', 'get_user_handler');
//                // The /{title} suffix is optional
//                $r->addRoute('GET', '/articles/{id:\d+}[/{title}]', 'get_article_handler');
            },
        ];
    }

    /**
     * @param ContainerInterface $container
     *
     * @return RouteCollector
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteCollector(ContainerInterface $container)
    {
        switch ($container->get('kernel')['router']) {
            case self::TYPE_CHAR_COUNT:
                $dataGenerator = CharCountBasedDataGenerator::class;
                break;
            case self::TYPE_GROUP_POS:
                $dataGenerator = GroupPosBasedDataGenerator::class;
                break;
            case self::TYPE_MARK:
                $dataGenerator = MarkBasedDataGenerator::class;
                break;
            case self::TYPE_GROUP_COUNT:
            default:
                $dataGenerator = GroupCountBasedDataGenerator::class;
        }

        return new RouteCollector(new Std, new $dataGenerator);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createSimpleRouteDispatcher(ContainerInterface $container)
    {
        switch ($container->get('kernel')['router']) {
            case self::TYPE_CHAR_COUNT:
                $dispatcher = CharCountBasedDispatcher::class;
                break;
            case self::TYPE_GROUP_POS:
                $dispatcher = GroupPosBasedDispatcher::class;
                break;
            case self::TYPE_MARK:
                $dispatcher = MarkBasedDispatcher::class;
                break;
            case self::TYPE_GROUP_COUNT:
            default:
                $dispatcher = GroupCountBasedDispatcher::class;
        }

        return new $dispatcher($container->get(RouteCollector::class)->getData());
    }
}
