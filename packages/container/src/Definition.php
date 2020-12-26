<?php

declare(strict_types=1);

namespace Rosem\Component\Container;

use Psr\Container\ContainerInterface;
use TypeError;

use function is_array;
use function is_callable;
use function is_string;
use function reset;

class Definition
{
    /**
     * @var callable
     */
    private $initializingFactory;

    /**
     * @var callable[]
     */
    private array $extendingFactories = [];

    /**
     * Definition constructor.
     *
     * @param callable|string[] $factory
     */
    public function __construct(callable|array $factory)
    {
        $isLazyCallable = is_array($factory) && is_string(reset($factory));

        if (!$isLazyCallable && !is_callable($factory)) {
            throw self::notCallableTypeError($factory);
        }

        if ($isLazyCallable) {
            [$interface, $method] = $factory;
            $this->initializingFactory = static function (ContainerInterface $container) use ($interface, $method) {
                $callable = [
                    $container->get($interface),
                    $method,
                ];

                // TODO is it better to use method_exists?
                if (!is_callable($callable)) {
                    throw self::notCallableTypeError($callable);
                }

                return $callable($container);
            };
        } else {
            $this->initializingFactory = $factory;
        }
    }

    final public static function notCallableTypeError(mixed $target): TypeError
    {
        return new TypeError(
            'A factory in a service provider should be callable, "' .
            get_debug_type($target) . '" given.'
        );
    }

    public function create(ContainerInterface $container)
    {
        $result = ($this->initializingFactory)($container);

        foreach ($this->extendingFactories as $factory) {
            $factory($container, $result);
        }

        return $result;
    }

    public function extend($factory): void
    {
        $this->extendingFactories[] = $factory;
    }
}
