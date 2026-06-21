<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Dto\OperationListFiltersDto;
use App\Dto\OperationListInputFiltersDto;
use App\Entity\Operation;
use App\Entity\User;
use App\Repository\OperationRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Operation business logic: listing, filtering, balance and funds checks.
 */
class OperationService implements OperationServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param OperationRepository      $operationRepository Operation repository
     * @param CategoryServiceInterface $categoryService     Category service
     * @param TagServiceInterface      $tagService          Tag service
     * @param PaginatorInterface       $paginator           Paginator
     */
    public function __construct(private readonly OperationRepository $operationRepository, private readonly CategoryServiceInterface $categoryService, private readonly TagServiceInterface $tagService, private readonly PaginatorInterface $paginator)
    {
    }

    /**
     * Get paginated list.
     *
     * @param int                          $page    Page
     * @param User                         $author  Author
     * @param OperationListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface Paginated list
     */
    public function getPaginatedList(int $page, User $author, OperationListInputFiltersDto $filters): PaginationInterface
    {
        $preparedFilters = $this->prepareFilters($filters);

        return $this->paginator->paginate(
            $this->operationRepository->queryAll($author, $preparedFilters),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['operation.id', 'operation.createdAt', 'operation.updatedAt', 'operation.title', 'operation.amount', 'operation.type', 'operation.date'],
                'defaultSortFieldName' => 'operation.date',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Calculate balance.
     *
     * @param User                         $author  Author
     * @param OperationListInputFiltersDto $filters Filters
     *
     * @return float Calculate balance
     */
    public function calculateBalance(User $author, OperationListInputFiltersDto $filters): float
    {
        return $this->operationRepository->calculateBalance($author, $this->prepareFilters($filters));
    }

    /**
     * Save.
     *
     * @param Operation $operation Operation
     */
    public function save(Operation $operation): void
    {
        $this->operationRepository->save($operation);
    }

    /**
     * Delete.
     *
     * @param Operation $operation Operation
     */
    public function delete(Operation $operation): void
    {
        $this->operationRepository->delete($operation);
    }

    /**
     * Would exceed funds.
     *
     * @param Operation $operation Operation
     *
     * @return bool Would exceed funds
     */
    public function wouldExceedFunds(Operation $operation): bool
    {
        $wallet = $operation->getWallet();
        if (null === $wallet) {
            return false;
        }

        // Balance of the wallet without this operation (so edits recalculate correctly).
        $excludeId = $operation->getId();
        $income = $this->operationRepository->sumByType($wallet, Operation::TYPE_INCOME, $excludeId);
        $expense = $this->operationRepository->sumByType($wallet, Operation::TYPE_EXPENSE, $excludeId);
        $balanceWithout = $income - $expense;

        // Apply this operation's effect; reject if the resulting balance is negative.
        $amount = (float) $operation->getAmount();
        $delta = Operation::TYPE_INCOME === $operation->getType() ? $amount : -$amount;

        return ($balanceWithout + $delta) < 0;
    }

    /**
     * Prepare filters.
     *
     * @param OperationListInputFiltersDto $filters Filters
     *
     * @return OperationListFiltersDto Prepare filters
     */
    private function prepareFilters(OperationListInputFiltersDto $filters): OperationListFiltersDto
    {
        $dateFrom = $this->parseDate($filters->dateFrom);
        $dateTo = $this->parseDate($filters->dateTo);
        if ($dateTo instanceof \DateTimeImmutable) {
            $dateTo = $dateTo->setTime(23, 59, 59);
        }

        return new OperationListFiltersDto(
            null !== $filters->categoryId ? $this->categoryService->findOneById($filters->categoryId) : null,
            $dateFrom,
            $dateTo,
            null !== $filters->tagId ? $this->tagService->findOneById($filters->tagId) : null,
        );
    }

    /**
     * Parse date.
     *
     * @param string|null $value Value
     *
     * @return \DateTimeImmutable|null Parse date
     */
    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if (null === $value || '' === $value) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return false !== $date ? $date : null;
    }
}
