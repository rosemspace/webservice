<?php

declare(strict_types=1);

namespace Rosem\Component\Container;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;
use function is_object;

/**
 * Class AbstractFacade
 */
abstract class AbstractFacade
{
    /**
     * The container
     * @var ContainerInterface
     */
    protected static $container;

    /**
     * The resolved object instances.
     * @var array
     */
    protected static $resolvedInstances;

    /**
     * Private constructor; non-instantiable.
     * @codeCoverageIgnore
     */
    private function __construct()
    {
    }

    /**
     * Handle dynamic, static calls to the object.
     *
     * @return mixed
     * @throws RuntimeException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    final public static function __callStatic(string $method, array $args)
    {
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new RuntimeException('A facade root has not been set.');
        }

        return $instance->{$method}(...$args);
    }

    /**
     * Set the container instance.
     */
    final public static function registerContainer(ContainerInterface $container): void
    {
        static::$container = $container;
    }

    /**
     * Get the root object behind the facade.
     * @return mixed
     * @throws RuntimeException
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    /**
     * Clear a resolved facade instance.
     */
    public static function clearResolvedFacadeInstance(string $name): void
    {
        unset(static::$resolvedInstances[$name]);
    }

    /**
     * Clear all of the resolved facade instances.
     */
    public static function clearResolvedFacadeInstances(): void
    {
        static::$resolvedInstances = [];
    }

    /**
     * Get the registered name of the component.
     * @return string|object
     * @throws RuntimeException
     */
    abstract protected static function getFacadeAccessor();

    /**
     * Resolve the facade root instance from the container.
     *
     * @param string|object $name
     *
     * @return mixed
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    protected static function resolveFacadeInstance($name)
    {
        return is_object($name)
            ? $name
            : static::$resolvedInstances[$name]
            ?? (static::$resolvedInstances[$name] = static::$container->get($name));
    }
}
