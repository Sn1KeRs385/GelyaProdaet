<?php

namespace App\Exceptions\Models\RolePermission;

use App\Exceptions\Models\AbstractModelException;
use Illuminate\Http\Response;

class CanNotDeletePermissionFromAdminRoleException extends AbstractModelException
{
    protected int $errorCode = Response::HTTP_BAD_REQUEST;
    protected string $errorMessageCode = 'CAN_NOT_DELETE_PERMISSION_FROM_ADMIN_ROLE';

    public function __construct()
    {
        $this->errorMessage = __('exceptions.can_not_delete_permission_from_admin_role');

        parent::__construct();
    }
}
