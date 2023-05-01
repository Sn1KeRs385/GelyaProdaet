<?php

namespace App\Enums;


enum Permission: string
{
    case ADMIN_MENU_ACCESS = 'admin-menu-access';
    case ROLE_PERMISSION_SETTING_ACCESS = 'role-permission-setting-access';
}
