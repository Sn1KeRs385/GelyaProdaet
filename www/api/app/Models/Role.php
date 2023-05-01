<?php

namespace App\Models;

use App\Exceptions\Models\RolePermission\CanNotDeletePermissionFromAdminRoleException;
use App\Models\Traits\Relations\RoleRelations;
use Spatie\Permission\Models\Role as BaseRole;

class Role extends BaseRole
{
    use RoleRelations;

    public function syncPermissions(...$permissions)
    {
        if ($this->name === \App\Enums\Role::getAdminRole()->value) {
            throw new CanNotDeletePermissionFromAdminRoleException();
        }

        parent::syncPermissions(...$permissions);
    }
}
