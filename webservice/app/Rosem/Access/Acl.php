<?php

namespace Rosem\Access;

use Analogue\ORM\Analogue;
use TrueStandards\GraphQL\GraphInterface;

class Acl
{
    public function __construct(GraphInterface $graph, Analogue $db)
    {
        $graph->addType('User', new \Rosem\Access\Http\GraphQL\Types\UserType($graph));
        $graph->addType('UserRole', new \Rosem\Access\Http\GraphQL\Types\UserRoleType($graph));
        $graph->addQuery(new \Rosem\Access\Http\GraphQL\Queries\UsersQuery($graph, $db));
    }
}
