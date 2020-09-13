<?php

declare(strict_types=1);

namespace Rosem\Component\Container\Exception;

use Rosem\Contract\Container\ServiceProviderInterface;

class ServiceProviderException extends ContainerException
{
    public static function dueToMissingClass(string $serviceProviderClass): self
    {
        return new self("The service provider \"${serviceProviderClass}\" does not exist.");
    }

    public static function dueToInvalidType(string $serviceProviderClass): self
    {
        return new self('An item of service providers configuration should be a string ' .
            'that represents a service provider class which implements \"' .
            ServiceProviderInterface::class . "\" interface, got \"${serviceProviderClass}\".");
    }

    public static function dueToInvalidInterface(string $serviceProviderClass): self
    {
        return new self("The service provider \"${serviceProviderClass}\" should implement \"" .
            ServiceProviderInterface::class . '\" interface.');
    }
}
