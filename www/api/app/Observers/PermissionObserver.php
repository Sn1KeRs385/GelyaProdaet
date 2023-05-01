<?php

namespace App\Observers;

use App\Enums\Role;
use App\Models\Permission;


class PermissionObserver
{
    public function created(Permission $permission): void
    {
        $permission->assignRole(Role::getAdminRole()->value);
    }
}
