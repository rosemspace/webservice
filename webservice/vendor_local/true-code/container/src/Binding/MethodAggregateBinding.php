<?php

namespace TrueCode\Container\Binding;

use TrueCode\Container\ExtractorTrait;

class MethodAggregateBinding extends AbstractAggregateBinding
{
    use ExtractorTrait;

    /**
     * @param $args
     *
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    protected function invoke(array &...$args)
    {
        if (! isset($args[1])) {
            $args[] = $args[0];
        }

        $context = $this->context->make($this->extractFirst($args));
        $result = null;
        reset($args);

        // preserve temporary context which will be injected into all methods calls
        $this->container->instance($this->getAbstract(), $context)->commit();

        foreach ($this->aggregate as $method) {
            $resolvedArgs = current($args) ?: [];
            $result = $method->make($context, $resolvedArgs);
            next($args);
        }

        // replace preserved earlier temporary context by reverting original binding
        $this->container->set($this->getAbstract(), $this);

        return [$context, $result];
    }

    /**
     * @param string $method
     * @param array  $args
     *
     * @return AggregateBindingInterface
     * @throws \ReflectionException
     */
    public function withMethodCall(string $method, array $args = []) : AggregateBindingInterface
    {
        $this->aggregate[$method] = new MethodBinding($this->container, $this->getConcrete(), $method, $args);

        return $this;
    }
}
