<?php

/*
 * This file is part of the Personal Wallet project.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Dto\OperationListFiltersDto;
use App\Entity\Category;
use App\Entity\Operation;
use App\Entity\Tag;
use App\Entity\User;
use App\Entity\Wallet;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Operation>
 */
class OperationRepository extends ServiceEntityRepository
{
    public const PAGINATOR_ITEMS_PER_PAGE = 10;

    /**
     * Constructor.
     *
     * @param ManagerRegistry $registry Registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Operation::class);
    }

    /**
     * Query all.
     *
     * @param User                    $author  Author
     * @param OperationListFiltersDto $filters Filters
     *
     * @return QueryBuilder Query all
     */
    public function queryAll(User $author, OperationListFiltersDto $filters): QueryBuilder
    {
        $queryBuilder = $this->createQueryBuilder('operation')
            ->select('operation', 'category', 'wallet', 'tags')
            ->join('operation.category', 'category')
            ->join('operation.wallet', 'wallet')
            ->leftJoin('operation.tags', 'tags')
            ->andWhere('operation.author = :author')
            ->setParameter('author', $author)
            ->orderBy('operation.date', 'DESC');

        return $this->applyFiltersToList($queryBuilder, $filters);
    }

    /**
     * Calculate balance.
     *
     * @param User                    $author  Author
     * @param OperationListFiltersDto $filters Filters
     *
     * @return float Calculate balance
     */
    public function calculateBalance(User $author, OperationListFiltersDto $filters): float
    {
        return $this->sumFilteredByType($author, Operation::TYPE_INCOME, $filters)
            - $this->sumFilteredByType($author, Operation::TYPE_EXPENSE, $filters);
    }

    /**
     * Count by category.
     *
     * @param Category $category Category
     *
     * @return int Count by category
     */
    public function countByCategory(Category $category): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return (int) $qb->select($qb->expr()->countDistinct('operation.id'))
            ->from(Operation::class, 'operation')
            ->where('operation.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Count by wallet.
     *
     * @param Wallet $wallet Wallet
     *
     * @return int Count by wallet
     */
    public function countByWallet(Wallet $wallet): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        return (int) $qb->select($qb->expr()->countDistinct('operation.id'))
            ->from(Operation::class, 'operation')
            ->where('operation.wallet = :wallet')
            ->setParameter('wallet', $wallet)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * Sum of operation amounts for a wallet filtered by type, optionally skipping one operation.
     *
     * @param Wallet   $wallet    Wallet
     * @param string   $type      Type
     * @param int|null $excludeId Operation id to exclude
     *
     * @return float Sum by type
     */
    public function sumByType(Wallet $wallet, string $type, ?int $excludeId = null): float
    {
        $qb = $this->createQueryBuilder('operation')
            ->select('COALESCE(SUM(operation.amount), 0)')
            ->where('operation.wallet = :wallet')
            ->andWhere('operation.type = :type')
            ->setParameter('wallet', $wallet)
            ->setParameter('type', $type);

        if (null !== $excludeId) {
            $qb->andWhere('operation.id <> :excludeId')
                ->setParameter('excludeId', $excludeId);
        }

        return (float) $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Save.
     *
     * @param Operation $operation Operation
     */
    public function save(Operation $operation): void
    {
        $this->getEntityManager()->persist($operation);
        $this->getEntityManager()->flush();
    }

    /**
     * Delete.
     *
     * @param Operation $operation Operation
     */
    public function delete(Operation $operation): void
    {
        $this->getEntityManager()->remove($operation);
        $this->getEntityManager()->flush();
    }

    /**
     * Apply filters to list.
     *
     * @param QueryBuilder            $queryBuilder Query builder
     * @param OperationListFiltersDto $filters      Filters
     *
     * @return QueryBuilder Apply filters to list
     */
    private function applyFiltersToList(QueryBuilder $queryBuilder, OperationListFiltersDto $filters): QueryBuilder
    {
        if ($filters->category instanceof Category) {
            $queryBuilder->andWhere('category = :category')
                ->setParameter('category', $filters->category);
        }

        if ($filters->dateFrom instanceof \DateTimeImmutable) {
            $queryBuilder->andWhere('operation.date >= :dateFrom')
                ->setParameter('dateFrom', $filters->dateFrom);
        }

        if ($filters->dateTo instanceof \DateTimeImmutable) {
            $queryBuilder->andWhere('operation.date <= :dateTo')
                ->setParameter('dateTo', $filters->dateTo);
        }

        if ($filters->tag instanceof Tag) {
            $queryBuilder->andWhere(':tag MEMBER OF operation.tags')
                ->setParameter('tag', $filters->tag);
        }

        return $queryBuilder;
    }

    /**
     * Sum filtered by type.
     *
     * @param User                    $author  Author
     * @param string                  $type    Type
     * @param OperationListFiltersDto $filters Filters
     *
     * @return float Sum filtered by type
     */
    private function sumFilteredByType(User $author, string $type, OperationListFiltersDto $filters): float
    {
        $queryBuilder = $this->createQueryBuilder('operation')
            ->select('COALESCE(SUM(operation.amount), 0)')
            ->join('operation.category', 'category')
            ->andWhere('operation.author = :author')
            ->andWhere('operation.type = :type')
            ->setParameter('author', $author)
            ->setParameter('type', $type);

        $this->applyFiltersToList($queryBuilder, $filters);

        return (float) $queryBuilder->getQuery()->getSingleScalarResult();
    }
}
