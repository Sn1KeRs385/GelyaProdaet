<?php

namespace App\Models\Traits\Relations;


use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property Collection<int, Role>|Role[] $roles
 */
trait PermissionRelations
{

    public function roles(): BelongsToMany
    {
        return parent::roles()
            ->using(RolePermission::class);
    }
}
