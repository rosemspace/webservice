<?php

namespace Rosem\Component\Container\Exception;

use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends ContainerException implements NotFoundExceptionInterface
{
    /**
     * @param $id
     *
     * @return self
     */
    public static function dueToMissingEntry($id): self
    {
        return new self("The entry \"$id\" is not found in the container.");
    }
}
