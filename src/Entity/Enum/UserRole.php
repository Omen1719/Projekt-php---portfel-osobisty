<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Entity\Enum;

/**
 * Available application user roles.
 */
enum UserRole: string
{
    case ROLE_USER = 'ROLE_USER';
    case ROLE_ADMIN = 'ROLE_ADMIN';

    /**
     * Label.
     *
     * @return string Label
     */
    public function label(): string
    {
        return match ($this) {
            UserRole::ROLE_USER => 'label.role_user',
            UserRole::ROLE_ADMIN => 'label.role_admin',
        };
    }
}
