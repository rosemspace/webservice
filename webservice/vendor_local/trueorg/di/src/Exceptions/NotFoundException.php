<?php

namespace True\DI\Exceptions;

use Exception;
use TrueStandards\DI\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
}
