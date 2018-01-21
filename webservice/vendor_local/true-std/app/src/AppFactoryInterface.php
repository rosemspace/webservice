<?php

namespace TrueStd\Application;

interface AppFactoryInterface
{
    public static function create() : AppInterface;
}
