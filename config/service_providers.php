<?php

return [
    Rosem\Authentication\Provider\HttpAuthenticationProvider::class,
    Rosem\Admin\Provider\AdminServiceProvider::class,
    Rosem\GraphQL\Provider\GraphQLServiceProvider::class,
    Rosem\App\Provider\AppServiceProvider::class,
    Rosem\Doctrine\Provider\ORMServiceProvider::class,
    Rosem\Atlas\Provider\AtlasServiceProvider::class,
    Rosem\Access\Provider\AccessServiceProvider::class,
];
