<?php

namespace Rosem\Container\Exception;

use Psrnext\Container\ServiceProviderInterface;

class ServiceProviderException extends ContainerException
{
    /**
     * @param string $serviceProviderClass
     *
     * @throws ContainerException
     * @return void
     */
    public static function invalidType(string $serviceProviderClass): void
    {
        throw new self('An item of service providers configuration should be a string ' .
            'that represents a service provider class which implements \"' .
            ServiceProviderInterface::class . "\" interface, got \"$serviceProviderClass\"");
    }

    /**
     * @param string $serviceProviderClass
     *
     * @throws ContainerException
     * @return void
     */
    public static function doesNotExist(string $serviceProviderClass): void
    {
        throw new self("The service provider \"$serviceProviderClass\" does not exist.");
    }

    /**
     * @param string $serviceProviderClass
     *
     * @throws ContainerException
     * @return void
     */
    public static function invalidInterface(string $serviceProviderClass): void
    {
        throw new self("The service provider \"$serviceProviderClass\" should implement \"" .
            ServiceProviderInterface::class . '\" interface.');
    }
}
