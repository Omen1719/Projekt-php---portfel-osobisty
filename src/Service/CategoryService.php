<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Category;
use App\Entity\User;
use App\Repository\CategoryRepository;
use App\Repository\OperationRepository;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Category business logic: listing, persistence and deletion checks.
 */
class CategoryService implements CategoryServiceInterface
{
    private const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param CategoryRepository  $categoryRepository  Category repository
     * @param OperationRepository $operationRepository Operation repository
     * @param PaginatorInterface  $paginator           Paginator
     */
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly OperationRepository $operationRepository, private readonly PaginatorInterface $paginator)
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
            $this->categoryRepository->queryAll($author),
            $page,
            self::PAGINATOR_ITEMS_PER_PAGE,
            [
                'sortFieldAllowList' => ['category.id', 'category.createdAt', 'category.updatedAt', 'category.title'],
                'defaultSortFieldName' => 'category.updatedAt',
                'defaultSortDirection' => 'desc',
            ]
        );
    }

    /**
     * Save.
     *
     * @param Category $category Category
     */
    public function save(Category $category): void
    {
        $this->categoryRepository->save($category);
    }

    /**
     * Delete.
     *
     * @param Category $category Category
     */
    public function delete(Category $category): void
    {
        $this->categoryRepository->delete($category);
    }

    /**
     * Can be deleted.
     *
     * @param Category $category Category
     *
     * @return bool Can be deleted
     */
    public function canBeDeleted(Category $category): bool
    {
        return 0 === $this->operationRepository->countByCategory($category);
    }

    /**
     * Find one by id.
     *
     * @param int $id Id
     *
     * @return Category|null One by id
     */
    public function findOneById(int $id): ?Category
    {
        return $this->categoryRepository->find($id);
    }

    /**
     * Find all.
     *
     * @param User $author Author
     *
     * @return array<int, Category> List of categories
     */
    public function findAll(User $author): array
    {
        return $this->categoryRepository->findBy(['author' => $author], ['title' => 'ASC']);
    }
}
