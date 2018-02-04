<?php

namespace Psrnext\App;

interface AppFactoryInterface
{
    public static function create() : AppInterface;
}
