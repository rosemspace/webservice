<?php

namespace True\DI\Binding;

use ReflectionMethod;
use SplFixedArray;
use True\DI\AbstractAggregateBinding;
use True\DI\AbstractContainer;
use True\DI\BindingInterface;
use True\DI\ReflectedBuildTrait;

class MethodAggregateBinding extends AbstractAggregateBinding
{
    use ReflectedBuildTrait;

    /**
     * @var ReflectionMethod[]
     */
    protected $reflectors;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    protected $stacks;

    public function __construct(AbstractContainer $container, BindingInterface $context, array $aggregate = [])
    {
        parent::__construct($container, $context);

        foreach ($aggregate as $method => $args) {
            $this->withMethodCall($method, $args);
        }
    }

    public function make(array &...$args)
    {
        $instance = $this->context->make($this->extractFirst($args));

        if (! $this->reflectors) {
            foreach ($this->aggregate as $method => $defaultArgs) {
                $this->reflectors[] = $reflector = new ReflectionMethod($this->context->getConcrete(), $method);
                $params = $this->params[] = SplFixedArray::fromArray($reflector->getParameters());
                $stack = $this->stacks[] = $this->getStack($params);
                $resolvedArgs = current($args) ?: $defaultArgs; //TODO: short
                $params
                    ? $reflector->invokeArgs($instance, $this->build($stack, $resolvedArgs))
                    : call_user_func_array([$instance, $method], current($args) ?: $defaultArgs);
                next($args);
            }

            reset($args);

            return $instance;
        }

        foreach ($this->aggregate as $method => $defaultArgs) {

            $resolvedArgs = current($args) ?: $defaultArgs;

            current($this->params)
                ? current($this->reflectors)->invokeArgs($instance, $this->build(current($this->stacks), $resolvedArgs))
                : call_user_func_array([$instance, $method], current($args) ?: $defaultArgs);
            next($this->reflectors);
            next($this->params);
            next($this->stacks);
            next($args);
        }

        reset($this->reflectors);
        reset($this->params);
        reset($this->stacks);
        reset($args);

        return $instance;
    }

    public function withMethodCall(string $method, array $args = []) : BindingInterface
    {
        $this->aggregate[$method] = $args;

        return $this;
    }
}
