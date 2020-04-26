<?php

namespace Rosem\Component\Container\Exception;

use Rosem\Contract\Container\ServiceProviderInterface;

class ServiceProviderException extends ContainerException
{
    /**
     * @param string $serviceProviderClass
     *
     * @return self
     */
    public static function dueToMissingClass(string $serviceProviderClass): self
    {
        return new self("The service provider \"$serviceProviderClass\" does not exist.");
    }

    /**
     * @param string $serviceProviderClass
     *
     * @return self
     */
    public static function dueToInvalidType(string $serviceProviderClass): self
    {
        return new self('An item of service providers configuration should be a string ' .
            'that represents a service provider class which implements \"' .
            ServiceProviderInterface::class . "\" interface, got \"$serviceProviderClass\".");
    }

    /**
     * @param string $serviceProviderClass
     *
     * @return self
     */
    public static function dueToInvalidInterface(string $serviceProviderClass): self
    {
        return new self("The service provider \"$serviceProviderClass\" should implement \"" .
            ServiceProviderInterface::class . '\" interface.');
    }
}
