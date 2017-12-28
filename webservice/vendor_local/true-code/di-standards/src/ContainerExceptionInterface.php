<?php

namespace TrueStandards\DI;

use Throwable;
use Psr\Container\ContainerExceptionInterface as PsrContainerExceptionInterface;

/**
 * Base interface representing a generic exception in a container.
 */
interface ContainerExceptionInterface extends Throwable, PsrContainerExceptionInterface
{
}
