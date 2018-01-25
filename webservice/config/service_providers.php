<?php

return [
    // should be before kernel service provider to provide correct order of routes
    Rosem\Admin\AdminServiceProvider::class,
    Rosem\Kernel\KernelServiceProvider::class,
    Rosem\Access\AccessServiceProvider::class,
];
