<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Operation;
use App\Entity\User;
use App\Entity\Wallet;
use App\Repository\OperationRepository;
use App\Repository\WalletRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Wallet business logic: listing, persistence, deletion checks and balance.
 */
class WalletService implements WalletServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param WalletRepository    $walletRepository    Wallet repository
     * @param OperationRepository $operationRepository Operation repository
     * @param PaginatorInterface  $paginator           Paginator
     */
    public function __construct(private readonly WalletRepository $walletRepository, private readonly OperationRepository $operationRepository, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int  $page   Page
     * @param User $author Author
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page, User $author): PaginationInterface
    {
        return $this->paginator->paginate(
            $this->walletRepository->queryAll($author),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['wallet.id', 'wallet.createdAt', 'wallet.updatedAt', 'wallet.title', 'wallet.type'],
                'defaultSortFieldName' => 'wallet.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save.
     *
     * @param Wallet $wallet Wallet
     */
    public function save(Wallet $wallet): void
    {
        $this->walletRepository->save($wallet);
    }

    /**
     * Delete.
     *
     * @param Wallet $wallet Wallet
     */
    public function delete(Wallet $wallet): void
    {
        $this->walletRepository->delete($wallet);
    }

    /**
     * Can be deleted.
     *
     * @param Wallet $wallet Wallet
     *
     * @return bool Can be deleted
     */
    public function canBeDeleted(Wallet $wallet): bool
    {
        return 0 === $this->operationRepository->countByWallet($wallet);
    }

    /**
     * Get balance.
     *
     * @param Wallet $wallet Wallet
     *
     * @return float Balance
     */
    public function getBalance(Wallet $wallet): float
    {
        $income = $this->operationRepository->sumByType($wallet, Operation::TYPE_INCOME);
        $expense = $this->operationRepository->sumByType($wallet, Operation::TYPE_EXPENSE);

        return $income - $expense;
    }
}
