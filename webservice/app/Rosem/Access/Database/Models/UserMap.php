<?php

namespace Rosem\Access\Database\Models;

use Analogue\ORM\EntityMap;

class UserMap extends EntityMap
{
    public $timestamps = true;

    public function role(User $user)
    {
        return $this->belongsTo($user, UserRole::class);
    }
}
