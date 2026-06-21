<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Contract for the user service.
 */
interface UserServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int $page Page
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page): PaginationInterface;

    /**
     * Save.
     *
     * @param User $user User
     */
    public function save(User $user): void;

    /**
     * Change password.
     *
     * @param User   $user          User
     * @param string $plainPassword Plain password
     */
    public function changePassword(User $user, string $plainPassword): void;
}
