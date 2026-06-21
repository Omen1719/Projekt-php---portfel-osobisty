<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Entity\Wallet;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Contract for the wallet service.
 */
interface WalletServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int  $page   Page
     * @param User $author Author
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface;

    /**
     * Save.
     *
     * @param Wallet $wallet Wallet
     */
    public function save(Wallet $wallet): void;

    /**
     * Delete.
     *
     * @param Wallet $wallet Wallet
     */
    public function delete(Wallet $wallet): void;

    /**
     * Can be deleted.
     *
     * @param Wallet $wallet Wallet
     */
    public function canBeDeleted(Wallet $wallet): bool;

    /**
     * Get balance.
     *
     * @param Wallet $wallet Wallet
     */
    public function getBalance(Wallet $wallet): float;
}
