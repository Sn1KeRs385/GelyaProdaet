<?php

namespace App\Observers;

use App\Enums\Role;
use App\Exceptions\Models\RolePermission\CanNotDeletePermissionFromAdminRoleException;
use App\Models\RolePermission;


class RolePermissionObserver
{
    public function deleting(RolePermission $rolePermission): void
    {
        if ($rolePermission->role->name === Role::getAdminRole()->value) {
            throw new CanNotDeletePermissionFromAdminRoleException();
        }
    }
}
