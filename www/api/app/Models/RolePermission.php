<?php

namespace App\Models;

use App\Models\Traits\Relations\RolePermissionRelations;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RolePermission extends Pivot
{
    use RolePermissionRelations;

    protected $table = 'role_has_permission';
}
