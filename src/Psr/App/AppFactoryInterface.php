<?php

namespace Rosem\Psr\App;

interface AppFactoryInterface
{
    /**
     * Create a new app.
     *
     * @return AppInterface The new app
     */
    public static function create(): AppInterface;
}
