<?php

namespace TrueStandards\DI;

use Psr\Container\NotFoundExceptionInterface as PsrNotFoundExceptionInterface;

/**
 * No entry was found in the container.
 */
interface NotFoundExceptionInterface extends PsrNotFoundExceptionInterface, ContainerExceptionInterface
{
}
