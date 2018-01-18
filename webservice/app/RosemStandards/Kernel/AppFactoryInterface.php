<?php

namespace RosemStandards\Kernel;

interface AppFactoryInterface
{
    public static function create(string $serviceProvidersFileName) : AppInterface;
}
