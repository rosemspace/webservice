<?php

namespace Rosem\Access\Database\Models;

use Analogue\ORM\Entity;

class UserRole extends Entity
{
    public function __construct($name)
    {
        $this->name = $name;
    }
}
