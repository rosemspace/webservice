<?php

namespace Rosem\Container\Definition\Proxy;

use Rosem\Container\Definition\{
    AbstractDefinition,
    Aggregate\AggregatedDefinitionInterface,
    DefinitionInterface
};

class DefinitionProxy extends AbstractDefinition implements DefinitionProxyInterface
{
    /**
     * @var bool
     */
    protected $committed = false;

    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregatedDefinitionInterface
     */
    public function withMethodCall(string $method, array $args = []) : AggregatedDefinitionInterface
    {
        return (new AggregatedDefinitionProxy($this->container, $this))->withMethodCall($method, $args);
    }

    /**
     * @param callable $function
     * @param array    $args
     *
     * @return AggregatedDefinitionInterface
     */
    public function withFunctionCall(callable $function, array $args = []) : AggregatedDefinitionInterface
    {
        return (new AggregatedDefinitionProxy($this->container, $this))->withFunctionCall($function, $args);
    }

    public function resolve() : DefinitionInterface
    {
        return $this->container->forceBind($this->getAbstract(), $this->getConcrete(), ...$this->args);
    }

    /**
     * @param array[] ...$args
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function make(array &...$args)
    {
        return $this->committed
            ? $this->resolve()->make(...$args)
            : $this->resolve()->commit()->make(...$args);
    }

    public function commit() : DefinitionInterface
    {
        $this->committed = true;

        return parent::commit();
    }
}
