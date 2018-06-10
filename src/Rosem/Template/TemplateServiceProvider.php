<?php

namespace Rosem\Template;

use Psr\Container\ContainerInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Template\TemplateRendererInterface;

class TemplateServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            'template.paths.root' => function () {
                return '';
            },
            'template.extension' => function () {
                return 'phtml';
            },
            TemplateRendererInterface::class => [static::class, 'createTemplateRenderer'],
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the modified entry
     * Callables have the following signature:
     *        function(Psr\Container\ContainerInterface $container, $previous)
     *     or function(Psr\Container\ContainerInterface $container, $previous = null)
     * About factories parameters:
     * - the container (instance of `Psr\Container\ContainerInterface`)
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null`
     * will be passed.
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [];
    }

    public function createTemplateRenderer(ContainerInterface $container)
    {
        return new TemplateRenderer(
            $container->get('template.paths.root'),
            $container->get('template.extension')
        );
    }
}
