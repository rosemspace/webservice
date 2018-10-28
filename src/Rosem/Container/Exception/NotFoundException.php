<?php

namespace Rosem\Container\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    /**
     * @param $id
     *
     * @throws NotFoundException
     */
    public static function notFound($id) {
        throw new self("\"$id\" definition not found in the container.");
    }
}
