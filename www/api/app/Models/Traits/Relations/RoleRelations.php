<?php

namespace App\Models\Traits\Relations;


use App\Models\Permission;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property Collection<int, Permission>|Permission[] $permissions
 */
trait RoleRelations
{
    public function permissions(): BelongsToMany
    {
        return parent::permissions()
            ->using(RolePermission::class);
    }
}
