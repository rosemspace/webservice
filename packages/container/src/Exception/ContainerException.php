<?php

declare(strict_types=1);

namespace Rosem\Component\Container\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    /**
     * @param $id
     */
    public static function forUndefinedEntry($id): self
    {
        return new self("The entry \"${id}\" is not defined in the container.");
    }
}
