<?php

namespace TrueStd\App;

interface AppFactoryInterface
{
    public static function create() : AppInterface;
}
