<?php

namespace Rosem\Component\Route\Dispatcher;

use Fig\Http\Message\StatusCodeInterface;
use Rosem\Component\Route\RegexBasedDispatcherInterface;

abstract class AbstractRegexBasedDispatcher implements RegexBasedDispatcherInterface
{
    protected const ROUTE_FOUND = StatusCodeInterface::STATUS_OK;

    protected const ROUTE_METHOD_NOT_ALLOWED = StatusCodeInterface::STATUS_METHOD_NOT_ALLOWED;

    protected const ROUTE_NOT_FOUND = StatusCodeInterface::STATUS_NOT_FOUND;
}
