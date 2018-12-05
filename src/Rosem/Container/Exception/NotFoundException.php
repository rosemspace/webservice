<?php

namespace Rosem\Container\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    /**
     * @param $id
     *
     * @return NotFoundException
     * @throws NotFoundException
     */
    public static function notFound($id): self
    {
        throw new self("\"$id\" definition is not found in the container.");
    }
}
