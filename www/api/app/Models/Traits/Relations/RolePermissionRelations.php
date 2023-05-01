<?php

namespace App\Models\Traits\Relations;


use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property Permission $permission
 * @property Role $role
 */
trait RolePermissionRelations
{
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
