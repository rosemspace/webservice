<?php

namespace Rosem\Container;

abstract class AbstractDefinition implements DefinitionInterface
{
    /**
     * @var mixed
     */
    protected $concrete;

    /**
     * @var array
     */
    protected $arguments;

    /**
     * @var array
     */
    protected $parameters = [];

    public function __construct($concrete, array $arguments = [])
    {
        $this->concrete = $concrete;
        $this->arguments = $arguments;
    }

    /**
     * Get stack of classes and parameters for automatic building.
     *
     * @param \ReflectionParameter[] $params
     *
     * @return array $parameters
     */
    public function resolveParameters(array $params): array
    {
        $length = count($params);
        $parameters = [];

        while ($length) {
            $parameters[] = $params[--$length]->getClass() ?: $params[$length];
        }

        return $parameters;
    }

    public function getParameters() : array
    {
        return $this->parameters;
    }

    public function getArguments() : array
    {
        return $this->arguments;
    }
}
