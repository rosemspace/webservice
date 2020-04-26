<?php

namespace Rosem\Component\Container\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    /**
     * @param $id
     *
     * @return self
     */
    public static function forUndefinedEntry($id): self
    {
        return new self("The entry \"$id\" is not defined in the container.");
    }
}
