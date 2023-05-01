<?php

namespace App\Enums;


enum Role: string
{
    case ADMIN = 'admin';

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    public static function getAdminRole(): self
    {
        return self::ADMIN;
    }
}
