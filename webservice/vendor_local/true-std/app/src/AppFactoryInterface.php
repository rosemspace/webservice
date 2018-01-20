<?php

namespace TrueStd\Application;

interface AppFactoryInterface
{
    public static function create(string $serviceProvidersFilePath) : AppInterface;
}
