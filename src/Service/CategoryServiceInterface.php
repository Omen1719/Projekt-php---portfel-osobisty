<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use Knp\Component\Pager\Pagination\PaginationInterface;

/**
 * Contract for the category service.
 */
interface CategoryServiceInterface
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
     * @param Category $category Category
     */
    public function save(Category $category): void;

    /**
     * Delete.
     *
     * @param Category $category Category
     */
    public function delete(Category $category): void;

    /**
     * Can be deleted.
     *
     * @param Category $category Category
     */
    public function canBeDeleted(Category $category): bool;

    /**
     * Find one by id.
     *
     * @param int $id Id
     */
    public function findOneById(int $id): ?Category;

    /**
     * Find all.
     *
     * @param User $author Author
     *
     * @return array<int, Category> List of categories
     */
    public function findAll(User $author): array;
}
