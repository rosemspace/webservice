<?php

namespace Rosem\Component\Container\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    /**
     * @param $id
     *
     * @return ContainerException
     * @throws ContainerException
     */
    public static function notDefined($id): self
    {
        throw new self("\"$id\" definition is not defined in the container.");
    }
}
