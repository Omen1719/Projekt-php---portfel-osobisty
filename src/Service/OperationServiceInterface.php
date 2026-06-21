<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Dto\OperationListInputFiltersDto;
use App\Entity\Operation;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Contract for the operation service.
 */
interface OperationServiceInterface
{
    /**
     * Get paginated list.
     *
     * @param int                          $page    Page
     * @param User                         $author  Author
     * @param OperationListInputFiltersDto $filters Filters
     *
     * @return PaginationInterface<string, mixed> Paginated list
     */
    public function getPaginatedList(int $page, User $author, OperationListInputFiltersDto $filters): PaginationInterface;

    /**
     * Income minus expense for the given user and filters (e.g. a date range).
     *
     * @param User                         $author  Author
     * @param OperationListInputFiltersDto $filters Filters
     */
    public function calculateBalance(User $author, OperationListInputFiltersDto $filters): float;

    /**
     * Save.
     *
     * @param Operation $operation Operation
     */
    public function save(Operation $operation): void;

    /**
     * Delete.
     *
     * @param Operation $operation Operation
     */
    public function delete(Operation $operation): void;

    /**
     * Tells whether saving this operation would push its wallet balance below zero.
     *
     * @param Operation $operation Operation
     */
    public function wouldExceedFunds(Operation $operation): bool;
}
