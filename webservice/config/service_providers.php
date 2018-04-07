<?php

return [
    // should be before kernel service provider to provide correct order of routes
    Rosem\BackOffice\Provider\BackOfficeServiceProvider::class,
    Rosem\App\Provider\AppServiceProvider::class,
//    Rosem\Access\AccessServiceProvider::class,
];
