<?php

namespace App\Models;

use App\Models\Traits\EntityPhpDoc;
use App\Models\Traits\Relations\PermissionRelations;
use Spatie\Permission\Models\Permission as BasePermission;

class Permission extends BasePermission
{
    use PermissionRelations;
}
